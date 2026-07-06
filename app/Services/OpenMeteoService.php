<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenMeteoService
{
    protected $baseUrl = 'https://api.open-meteo.com/v1';

    public function getWeather($latitude, $longitude)
    {
        $response = Http::get("{$this->baseUrl}/forecast", [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => 'temperature_2m,wind_speed_10m,precipitation',
        ]);

        return $response->json();
    }
}
