<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Port;
use App\Models\RiskScore;
use App\Models\Article;

class ApiController extends Controller
{
    public function countries()
    {
        return response()->json(Country::where('is_active', true)->get());
    }

    public function risk()
    {
        // Get latest risk scores joined with country names
        $scores = RiskScore::with('country')->get();
        return response()->json($scores);
    }

    public function ports()
    {
        return response()->json(Port::whereHas('country', function($q) {
            $q->where('is_active', true);
        })->with('country')->get());
    }

    public function news(Request $request)
    {
        $query = Article::with('country')->latest('published_at');
        
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json($query->limit(20)->get());
    }

    public function currency()
    {
        return response()->json(Country::where('is_active', true)->select('id', 'name', 'code', 'currency', 'exchange_rate')->get());
    }

    public function syncMetrics(Request $request)
    {
        set_time_limit(0); // Prevent timeout
        $params = ['--type' => 'metrics'];
        if ($request->has('country_id')) {
            $params['--country'] = $request->country_id;
        }
        
        \Illuminate\Support\Facades\Artisan::call('api:fetch', $params);
        return response()->json(['status' => 'success', 'message' => 'Weather and Economic data synced successfully.']);
    }

    public function syncNews(Request $request)
    {
        set_time_limit(0); // Prevent timeout
        $params = ['--type' => 'news'];
        if ($request->has('country_id')) {
            $params['--country'] = $request->country_id;
        }
        
        \Illuminate\Support\Facades\Artisan::call('api:fetch', $params);
        return response()->json(['status' => 'success', 'message' => 'Intelligence News synced successfully.']);
    }
}
