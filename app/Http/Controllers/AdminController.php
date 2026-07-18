<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Port;
use App\Models\Article;
use App\Models\Country;

class AdminController extends Controller
{
    public function index()
    {
        $ports = Port::with('country')->get();
        $articles = Article::with('country')->latest('published_at')->get();
        $countries = Country::where('is_active', true)->get();
        return view('admin.dashboard', compact('ports', 'articles', 'countries'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Port::create([
            'country_id' => $request->country_id,
            'name' => $request->name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'active',
        ]);

        return back()->with('success', 'Port added successfully.');
    }

    public function destroyPort(Port $port)
    {
        $port->delete();
        return back()->with('success', 'Port deleted successfully.');
    }

    public function destroyArticle(Article $article)
    {
        $article->delete();
        return back()->with('success', 'Article deleted successfully.');
    }
}
