<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedAllCountries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:seed-countries';
    protected $description = 'Fetch all countries from RestCountries API and seed the database';

    public function handle()
    {
        $this->info('Fetching all countries from GitHub data source...');
        
        try {
            $json = file_get_contents('https://raw.githubusercontent.com/mledoze/countries/master/countries.json');
            $countries = json_decode($json, true);
            
            if (empty($countries)) {
                $this->error('Failed to fetch data or JSON returned empty.');
                return;
            }

            $count = 0;
            $bar = $this->output->createProgressBar(count($countries));
            
            foreach ($countries as $data) {
                // Skip if doesn't have required data
                if (!isset($data['name']['common']) || !isset($data['cca2'])) {
                    $bar->advance();
                    continue;
                }
                
                $name = $data['name']['common'];
                $code = $data['cca2'];
                $region = $data['region'] ?? 'Unknown';
                
                // Get latlng
                $lat = isset($data['latlng'][0]) ? $data['latlng'][0] : 0;
                $lng = isset($data['latlng'][1]) ? $data['latlng'][1] : 0;
                
                // Use updateOrCreate so we don't duplicate existing ones
                \App\Models\Country::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'region' => $region,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        // Defaults for economy/weather (will be updated by fetch command later)
                        'gdp' => \App\Models\Country::where('code', $code)->value('gdp') ?? 0,
                        'inflation' => \App\Models\Country::where('code', $code)->value('inflation') ?? 0,
                        'temperature' => \App\Models\Country::where('code', $code)->value('temperature') ?? null,
                        'wind_speed' => \App\Models\Country::where('code', $code)->value('wind_speed') ?? null,
                        'exchange_rate' => \App\Models\Country::where('code', $code)->value('exchange_rate') ?? 1,
                        'currency' => \App\Models\Country::where('code', $code)->value('currency') ?? 'USD'
                    ]
                );
                
                $count++;
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info("Successfully seeded {$count} countries into the database.");
            
        } catch (\Exception $e) {
            $this->error('Error occurred: ' . $e->getMessage());
        }
    }
}
