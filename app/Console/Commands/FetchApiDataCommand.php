<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Country;
use App\Services\GNewsService;
use App\Services\WorldBankService;
use App\Services\OpenMeteoService;

class FetchApiDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and cache data from external APIs for all watchlisted countries';

    /**
     * Execute the console command.
     */
    public function handle(GNewsService $newsService, WorldBankService $wbService, OpenMeteoService $weatherService)
    {
        $this->info('Starting API Data Fetch (Caching)...');

        // Note: For now we just create the skeleton structure.
        // The actual insertion to DB & score calculation will be done in Phase 3.

        $countries = Country::whereIn('id', function($query) {
            $query->select('country_id')->from('watchlists');
        })->get();

        if ($countries->isEmpty()) {
            $this->info('No countries in watchlist. Skipping fetch.');
            return;
        }

        foreach ($countries as $country) {
            $this->info("Fetching data for {$country->name}...");

            // 1. Fetch News
            try {
                $news = $newsService->getNews($country->name . ' economy logistics');
                $this->info("   - Found " . count($news['articles'] ?? []) . " articles.");
                // TODO: Save to news_cache / articles table
            } catch (\Exception $e) {
                $this->error("   - Failed to fetch news.");
            }
            
            // Wait 1 second to avoid hitting Free API limits
            sleep(1);
        }

        $this->info('API Data Fetch Completed!');
    }
}
