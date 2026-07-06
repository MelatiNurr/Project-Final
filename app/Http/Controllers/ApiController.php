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
        return response()->json(Country::all());
    }

    public function risk()
    {
        // Get latest risk scores joined with country names
        $scores = RiskScore::with('country')->get();
        return response()->json($scores);
    }

    public function ports()
    {
        return response()->json(Port::with('country')->get());
    }

    public function news(Request $request)
    {
        $query = Article::with('country')->latest('published_at');
        
        if ($request->has('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        return response()->json($query->limit(20)->get());
    }
}
