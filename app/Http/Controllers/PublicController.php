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
        return view('countries', compact('countries'));
    }

    public function showCountry($id)
    {
        $country = Country::where('is_active', true)->with(['riskScores' => function($q) {
            $q->latest()->first();
        }, 'articles' => function($q) {
            $q->latest('published_at')->limit(6);
        }, 'ports'])->findOrFail($id);
        
        return view('country', compact('country'));
    }

    public function countriesSettings()
    {
        $countries = Country::orderBy('name')->get();
        return view('countries-settings', compact('countries'));
    }

    public function toggleCountryStatus(Request $request)
    {
        $country = Country::findOrFail($request->country_id);
        $country->is_active = !$country->is_active;
        $country->save();

        return response()->json([
            'success' => true,
            'is_active' => $country->is_active,
            'message' => $country->name . ' has been ' . ($country->is_active ? 'activated' : 'deactivated')
        ]);
    }
    public function bulkToggleCountries(Request $request)
    {
        $status = $request->status === 'activate' ? true : false;
        Country::query()->update(['is_active' => $status]);
        
        return response()->json([
            'success' => true,
            'message' => 'All countries have been ' . ($status ? 'activated' : 'deactivated')
        ]);
    }
}
