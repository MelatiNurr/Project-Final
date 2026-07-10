<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;

class PublicController extends Controller
{
    public function showCountry($id)
    {
        $country = Country::with(['riskScores' => function($q) {
            $q->latest()->first();
        }, 'articles' => function($q) {
            $q->latest('published_at')->limit(6);
        }, 'ports'])->findOrFail($id);
        
        return view('country', compact('country'));
    }
}
