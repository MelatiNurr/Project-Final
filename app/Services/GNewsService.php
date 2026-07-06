<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GNewsService
{
    protected $baseUrl = 'https://gnews.io/api/v4';
    protected $apiKey = 'YOUR_GNEWS_API_KEY'; // In real app, put this in .env

    public function getNews($keyword, $country = 'us')
    {
        // Retrieve key from config/env
        $key = env('GNEWS_API_KEY', $this->apiKey);
        
        $response = Http::get("{$this->baseUrl}/search", [
            'q' => $keyword,
            'lang' => 'en',
            'country' => $country,
            'max' => 10,
            'apikey' => $key
        ]);

        return $response->json();
    }
}
