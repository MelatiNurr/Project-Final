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

class FetchApiDataCommand extends Command
{
    protected $signature = 'api:fetch';
    protected $description = 'Fetch and cache data from external APIs for countries and calculate risk scores.';

    public function handle(
        GNewsService $newsService, 
        WorldBankService $wbService, 
        OpenMeteoService $weatherService, 
        RiskAnalysisService $riskService
    ) {
        $this->info('Starting API Data Fetch (Caching & Calculation)...');

        $countries = Country::all();

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
            $this->info("Fetching data for {$country->name}...");

            // 1. Fetch Weather
            try {
                $weather = $weatherService->getWeather($country->latitude, $country->longitude);
            } catch (\Exception $e) {
                $weather = [];
                $this->error("   - Failed to fetch weather.");
            }

            // 2. Fetch Economy (World Bank uses 2-letter ISO)
            try {
                $economy = $wbService->getMacroEconomics($country->code);
                $gdp = isset($economy['gdp'][1][0]['value']) ? $economy['gdp'][1][0]['value'] : 0;
                $inflation = isset($economy['inflation'][1][0]['value']) ? $economy['inflation'][1][0]['value'] : 0;
                
                // update country record with latest macroeconomic data
                $country->update(['gdp' => $gdp, 'inflation' => $inflation]);
            } catch (\Exception $e) {
                $gdp = 0;
                $inflation = 0;
                $this->error("   - Failed to fetch economy data.");
            }

            // 3. Fetch News & Calculate Sentiment
            try {
                $news = $newsService->getNews($country->name . ' economy logistics', strtolower($country->code));
                $articles = $news['articles'] ?? [];
                $totalSentiment = 0;
                
                foreach ($articles as $article) {
                    $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
                    $score = $riskService->calculateSentiment($text);
                    $totalSentiment += $score;
                }
                
                $averageSentiment = count($articles) > 0 ? ($totalSentiment / count($articles)) : 0;
                $this->info("   - Analyzed " . count($articles) . " articles. Avg Sentiment: {$averageSentiment}");
            } catch (\Exception $e) {
                $averageSentiment = 0;
                $this->error("   - Failed to fetch news from GNews.");
            }

            // 4. Calculate Risk
            $riskData = $riskService->calculateRiskScore($weather, $gdp, $inflation, $averageSentiment);

            // 5. Save/Update to DB (Delete old scores for this country to keep it simple, or insert new row for history)
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
