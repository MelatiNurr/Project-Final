<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RestCountriesService
{
    protected $baseUrl = 'https://restcountries.com/v3.1';

    public function getCountryProfile($countryName)
    {
        $response = Http::get("{$this->baseUrl}/name/{$countryName}");
        return $response->json();
    }

    public function getAllCountries()
    {
        $response = Http::get("{$this->baseUrl}/all");
        return $response->json();
    }
}
