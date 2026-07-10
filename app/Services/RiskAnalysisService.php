<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;

class RiskAnalysisService
{
    protected $positiveWords;
    protected $negativeWords;

    public function __construct()
    {
        // Cache the dictionary to avoid querying for every article
        $this->positiveWords = PositiveWord::pluck('word')->toArray();
        $this->negativeWords = NegativeWord::pluck('word')->toArray();
    }

    /**
     * Lexicon-based sentiment analysis
     * @param string $text
     * @return float Sentiment Score (-1.0 to 1.0)
     */
    public function calculateSentiment($text)
    {
        if (empty($text)) return 0;

        $text = strtolower($text);
        // Remove punctuation and extra spaces
        $text = preg_replace('/[^\w\s]/', '', $text);
        $words = explode(' ', $text);

        $posCount = 0;
        $negCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $this->positiveWords)) {
                $posCount++;
            } elseif (in_array($word, $this->negativeWords)) {
                $negCount++;
            }
        }

        $totalSentimentWords = $posCount + $negCount;
        
        if ($totalSentimentWords === 0) {
            return 0; // Neutral
        }

        // Return a score from -1.0 (very negative) to 1.0 (very positive)
        return ($posCount - $negCount) / $totalSentimentWords;
    }

    /**
     * Weighted Risk Model Calculator
     * Higher score = Higher risk
     * @return array
     */
    public function calculateRiskScore($weatherData, $gdp, $inflation, $averageSentiment)
    {
        // 1. Weather Risk (0-100)
        $weatherRisk = 0;
        if (isset($weatherData['current'])) {
            $wind = $weatherData['current']['wind_speed_10m'] ?? 0;
            $precip = $weatherData['current']['precipitation'] ?? 0;
            
            if ($wind > 30 || $precip > 20) $weatherRisk = 100;
            elseif ($wind > 10 || $precip > 5) $weatherRisk = 60;
            elseif ($wind > 3) $weatherRisk = 30;
        }

        // 2. Economic Risk (0-100) based on Inflation
        $economicRisk = 0;
        if ($inflation > 8) $economicRisk = 100;
        elseif ($inflation > 2.5) $economicRisk = 60;
        elseif ($inflation < 1) $economicRisk = 80; // Too low/deflation is risky
        else $economicRisk = 20;

        // 3. Sentiment Risk (0-100)
        // sentiment is -1 (bad) to 1 (good). We invert it for risk.
        // -1 sentiment = 100 risk, 1 sentiment = 0 risk
        $sentimentRisk = ((1 - $averageSentiment) / 2) * 100;

        // Weights: Weather 30%, Economy 40%, Sentiment 30%
        $totalScore = ($weatherRisk * 0.3) + ($economicRisk * 0.4) + ($sentimentRisk * 0.3);
        $totalScore = min(100, max(0, $totalScore)); // Clamp between 0 and 100

        return [
            'weather_risk' => $weatherRisk,
            'economic_risk' => $economicRisk,
            'sentiment_risk' => $sentimentRisk,
            'total_score' => $totalScore
        ];
    }
}
