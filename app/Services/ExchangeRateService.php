<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    protected $baseUrl = 'https://open.er-api.com/v6/latest'; // Free Exchange Rate API

    public function getRates($baseCurrency = 'USD')
    {
        $response = Http::get("{$this->baseUrl}/{$baseCurrency}");
        return $response->json();
    }
}
