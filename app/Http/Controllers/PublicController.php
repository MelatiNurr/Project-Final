<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class PublicController extends Controller
{
    public function countriesIndex()
    {
        $countries = Country::where('is_active', true)->with(['riskScores' => function($q) {
            $q->latest()->limit(1);
        }])->get();
        
        $watchedIds = auth()->check() ? auth()->user()->watchlists()->pluck('country_id')->toArray() : [];
        
        return view('countries', compact('countries', 'watchedIds'));
    }

    public function showCountry($id)
    {
        $country = Country::where('is_active', true)->with(['riskScores' => function($q) {
            $q->latest()->first();
        }, 'articles' => function($q) {
            $q->latest('published_at')->limit(6);
        }, 'ports'])->findOrFail($id);
        
        $watchedIds = auth()->check() ? auth()->user()->watchlists()->pluck('country_id')->toArray() : [];
        
        return view('country', compact('country', 'watchedIds'));
    }

    public function watchlistIndex()
    {
        $user = auth()->user();
        $countryIds = $user->watchlists()->pluck('country_id');
        
        $countries = Country::whereIn('id', $countryIds)
            ->where('is_active', true)
            ->with(['riskScores' => function($q) {
                $q->latest()->limit(1);
            }])->get();
            
        $watchedIds = $countryIds->toArray();
            
        return view('watchlist', compact('countries', 'watchedIds'));
    }

    public function toggleWatchlist(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id'
        ]);

        $user = auth()->user();
        $exists = $user->watchlists()->where('country_id', $request->country_id)->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['status' => 'removed', 'message' => 'Country removed from watchlist']);
        } else {
            $user->watchlists()->create([
                'country_id' => $request->country_id
            ]);
            return response()->json(['status' => 'added', 'message' => 'Country added to watchlist']);
        }
    }
}
