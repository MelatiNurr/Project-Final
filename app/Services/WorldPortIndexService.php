<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WorldPortIndexService
{
    // The World Port Index JSON from tayljordan
    protected $baseUrl = 'https://raw.githubusercontent.com/tayljordan/ports/master/ports.json'; 

    public function getPorts()
    {
        // Cache for 24 hours to avoid downloading a large JSON file repeatedly
        return Cache::remember('world_ports', 86400, function () {
            $response = Http::timeout(30)->get($this->baseUrl);
            if ($response->successful()) {
                $data = $response->json();
                return isset($data['ports']) ? $data['ports'] : [];
            }
            return [];
        });
    }
}
