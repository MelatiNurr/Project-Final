<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    protected $baseUrl = 'https://api.worldbank.org/v2';

    public function getMacroEconomics($countryIso2Code)
    {
        // GDP (NY.GDP.MKTP.CD)
        $gdpResponse = Http::get("{$this->baseUrl}/country/{$countryIso2Code}/indicator/NY.GDP.MKTP.CD", [
            'format' => 'json',
            'per_page' => 1,
            'mrv' => 1 // Most recent value
        ]);

        // Inflation (FP.CPI.TOTL.ZG)
        $inflationResponse = Http::get("{$this->baseUrl}/country/{$countryIso2Code}/indicator/FP.CPI.TOTL.ZG", [
            'format' => 'json',
            'per_page' => 1,
            'mrv' => 1
        ]);

        return [
            'gdp' => $gdpResponse->json(),
            'inflation' => $inflationResponse->json()
        ];
    }
}
