<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;
use App\Services\RiskAnalysisService;
use App\Services\RestCountriesService;

class FetchApiDataCommand extends Command
{
    protected $signature = 'api:fetch {--type=all : The type of data to fetch (all, metrics, news)} {--country= : The specific country ID to fetch data for}';
    protected $description = 'Fetch and cache data from external APIs for countries and calculate risk scores.';

    public function handle(
        GNewsService $newsService, 
        WorldBankService $wbService, 
        OpenMeteoService $weatherService, 
        RiskAnalysisService $riskService,
        \App\Services\ExchangeRateService $exchangeService,
        RestCountriesService $restCountriesService
    ) {
        $type = $this->option('type');
        $countryId = $this->option('country');
        
        $this->info("Starting API Data Fetch (Type: {$type}" . ($countryId ? ", Country ID: {$countryId}" : "") . ")...");

        if ($countryId) {
            $countries = Country::where('id', $countryId)->where('is_active', true)->get();
        } else {
            $countries = Country::where('is_active', true)->get();
        }

        $globalPorts = [];
        if ($type === 'all' || $type === 'metrics') {
            $this->info("Fetching World Port Index data...");
            try {
                $portService = app(\App\Services\WorldPortIndexService::class);
                $globalPorts = $portService->getPorts();
                $this->info("Successfully fetched " . count($globalPorts) . " global ports.");
            } catch (\Exception $e) {
                $this->error("Failed to fetch world ports.");
            }
        }

        // Auto-seed some default countries if the database is completely empty
        if ($countries->isEmpty()) {
            $this->info('Database empty. Seeding default countries & ports...');
            $countries = collect([
                Country::create(['name' => 'United States', 'code' => 'US', 'region' => 'Americas', 'latitude' => 37.0902, 'longitude' => -95.7129]),
                Country::create(['name' => 'China', 'code' => 'CN', 'region' => 'Asia', 'latitude' => 35.8617, 'longitude' => 104.1954]),
                Country::create(['name' => 'Indonesia', 'code' => 'ID', 'region' => 'Asia', 'latitude' => -0.7893, 'longitude' => 113.9213]),
                Country::create(['name' => 'Germany', 'code' => 'DE', 'region' => 'Europe', 'latitude' => 51.1657, 'longitude' => 10.4515]),
            ]);
            
            Port::create(['country_id' => 1, 'name' => 'Port of Los Angeles', 'latitude' => 33.7288, 'longitude' => -118.2620, 'status' => 'active']);
            Port::create(['country_id' => 2, 'name' => 'Port of Shanghai', 'latitude' => 31.2222, 'longitude' => 121.4581, 'status' => 'active']);
            Port::create(['country_id' => 3, 'name' => 'Tanjung Priok', 'latitude' => -6.1105, 'longitude' => 106.8797, 'status' => 'active']);
            Port::create(['country_id' => 4, 'name' => 'Port of Hamburg', 'latitude' => 53.5488, 'longitude' => 9.9872, 'status' => 'active']);
        }

        foreach ($countries as $country) {
            $this->info("Processing data for {$country->name}...");

            $weather = ['current' => ['wind_speed_10m' => $country->wind_speed, 'precipitation' => 0]];
            $gdp = $country->gdp;
            $inflation = $country->inflation;

            if ($type === 'all' || $type === 'metrics') {
                // 1. Fetch Weather
                try {
                    $weather = $weatherService->getWeather($country->latitude, $country->longitude);
                    $temperature = isset($weather['current']['temperature_2m']) ? $weather['current']['temperature_2m'] : null;
                    $windSpeed = isset($weather['current']['wind_speed_10m']) ? $weather['current']['wind_speed_10m'] : null;
                    $country->update(['temperature' => $temperature, 'wind_speed' => $windSpeed]);
                } catch (\Exception $e) {
                    $weather = ['current' => ['wind_speed_10m' => $country->wind_speed, 'precipitation' => 0]];
                    $this->error("   - Failed to fetch weather.");
                }

                // 2. Fetch Economy
                try {
                    $economy = $wbService->getMacroEconomics($country->code);
                    $gdp = isset($economy['gdp'][1][0]['value']) ? $economy['gdp'][1][0]['value'] : $gdp;
                    $inflation = isset($economy['inflation'][1][0]['value']) ? $economy['inflation'][1][0]['value'] : $inflation;
                    $population = isset($economy['population'][1][0]['value']) ? $economy['population'][1][0]['value'] : $country->population;
                    $country->update(['gdp' => $gdp, 'inflation' => $inflation, 'population' => $population]);
                } catch (\Exception $e) {
                    $this->error("   - Failed to fetch economy data.");
                }

                // 3. Fetch Currency & Exchange Rate
                try {
                    // Comprehensive Currency Map since REST Countries API v3.1 is deprecated
                    $currencyMap = [
                        'US' => 'USD', 'CN' => 'CNY', 'ID' => 'IDR', 'DE' => 'EUR',
                        'JP' => 'JPY', 'AU' => 'AUD', 'GB' => 'GBP', 'FR' => 'EUR',
                        'IR' => 'IRR', 'KP' => 'KPW', 'KR' => 'KRW', 'RU' => 'RUB',
                        'IN' => 'INR', 'BR' => 'BRL', 'ZA' => 'ZAR', 'CA' => 'CAD',
                        'SA' => 'SAR', 'AE' => 'AED', 'SG' => 'SGD', 'MY' => 'MYR'
                    ];
                    $currencyCode = $currencyMap[$country->code] ?? 'USD';
                    
                    // Fetch Exchange Rate
                    $rates = $exchangeService->getRates('USD');
                    $rate = $rates['rates'][$currencyCode] ?? 1;
                    $country->update(['exchange_rate' => $rate, 'currency' => $currencyCode]);
                } catch (\Exception $e) {
                    $this->error("   - Failed to fetch exchange rates: " . $e->getMessage());
                }

                // 3. Sync Ports
                try {
                    if (!empty($globalPorts)) {
                        $countryPorts = array_filter($globalPorts, function($port) use ($country) {
                            return isset($port['country']) && (
                                stripos($port['country'], $country->name) !== false || 
                                ($country->code === 'US' && stripos($port['country'], 'United States') !== false) ||
                                ($country->code === 'GB' && stripos($port['country'], 'United Kingdom') !== false)
                            );
                        });
                        // Sort ports by size to get the largest ones first
                        $sizeWeights = [
                            'Very Large' => 5,
                            'Large' => 4,
                            'Medium' => 3,
                            'Small' => 2,
                            'Very Small' => 1
                        ];
                        
                        usort($countryPorts, function($a, $b) use ($sizeWeights) {
                            $weightA = $sizeWeights[$a['port_size'] ?? ''] ?? 0;
                            $weightB = $sizeWeights[$b['port_size'] ?? ''] ?? 0;
                            return $weightB <=> $weightA;
                        });

                        // Limit to top 10 largest ports per country
                        $countryPorts = array_slice($countryPorts, 0, 10);

                        if (count($countryPorts) > 0) {
                            Port::where('country_id', $country->id)->delete();
                            foreach ($countryPorts as $p) {
                                Port::create([
                                    'country_id' => $country->id,
                                    'name' => substr($p['wpi_port_name'] ?? 'Unknown Port', 0, 255),
                                    'latitude' => $p['latitude'] ?? 0,
                                    'longitude' => $p['longitude'] ?? 0,
                                    'status' => 'active'
                                ]);
                            }
                            $this->info("   - Synced " . count($countryPorts) . " ports.");
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("   - Failed to sync ports: " . $e->getMessage());
                }
            }

            $averageSentiment = \App\Models\Article::where('country_id', $country->id)->avg('sentiment_score') ?? 0;

            if ($type === 'all' || $type === 'news') {
                // 3. Fetch News & Calculate Sentiment
                try {
                    // Simplified search query to get more results without API errors
                    $news = $newsService->getNews($country->name . ' economy', strtolower($country->code));
                    $articles = $news['articles'] ?? [];
                    
                    if (empty($articles)) {
                        throw new \Exception("Empty articles, falling back to mock.");
                    }

                    $totalSentiment = 0;
                    \App\Models\Article::where('country_id', $country->id)->delete(); // Clear old news
                    
                    foreach ($articles as $article) {
                        $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
                        $score = $riskService->calculateSentiment($text);
                        $totalSentiment += $score;
                        
                        \App\Models\Article::create([
                            'country_id' => $country->id,
                            'title' => substr($article['title'] ?? 'No Title', 0, 255),
                            'source' => $article['source']['name'] ?? 'GNews',
                            'url' => $article['url'] ?? '#',
                            'published_at' => isset($article['publishedAt']) ? \Carbon\Carbon::parse($article['publishedAt']) : now(),
                            'sentiment_score' => $score
                        ]);
                    }
                    
                    $averageSentiment = count($articles) > 0 ? ($totalSentiment / count($articles)) : 0;
                    $this->info("   - Analyzed " . count($articles) . " articles. Avg Sentiment: {$averageSentiment}");
                } catch (\Exception $e) {
                    // Mock Data for Demonstration
                    $averageSentiment = -0.2; // Fixed sentiment score bug (-1 to 1)
                    \App\Models\Article::where('country_id', $country->id)->delete();
                    \App\Models\Article::create([
                        'country_id' => $country->id,
                        'title' => "Mock: Supply Chain Disruptions in {$country->name}",
                        'source' => "Simulated News",
                        'url' => 'https://news.google.com/search?q=' . urlencode($country->name . ' supply chain'),
                        'published_at' => now(),
                        'sentiment_score' => -0.4 // Fixed sentiment
                    ]);
                    \App\Models\Article::create([
                        'country_id' => $country->id,
                        'title' => "Mock: Economic recovery boosts {$country->name} exports",
                        'source' => "Simulated News",
                        'url' => 'https://news.google.com/search?q=' . urlencode($country->name . ' economic recovery exports'),
                        'published_at' => now()->subHours(2),
                        'sentiment_score' => 0.6 // Fixed sentiment
                    ]);

                    $this->error("   - Failed to fetch news from GNews. Using mock data.");
                }
            }

            // 4. Calculate Risk
            $riskData = $riskService->calculateRiskScore($weather, $gdp, $inflation, $averageSentiment);

            // 5. Save/Update to DB
            RiskScore::where('country_id', $country->id)->delete(); // Keeping only latest for dashboard
            
            RiskScore::create([
                'country_id' => $country->id,
                'weather_risk' => $riskData['weather_risk'],
                'economic_risk' => $riskData['economic_risk'],
                'sentiment_risk' => $riskData['sentiment_risk'],
                'total_score' => $riskData['total_score']
            ]);

            $this->info("   -> Final Risk Score: {$riskData['total_score']}%");
            
            sleep(1); // Respect free API rate limits
        }

        $this->info('======================================');
        $this->info('API Data Fetch & Risk Calculation Completed!');
    }
}
