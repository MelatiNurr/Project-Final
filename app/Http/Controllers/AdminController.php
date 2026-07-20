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
        $countries = Country::orderBy('name')->get();
        $users = User::all();
        return view('admin.dashboard', compact('ports', 'articles', 'countries', 'users'));
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

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        return back()->with('success', 'User created successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete yourself.']);
        }
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function storeCountry(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:countries',
            'code' => 'required|string|max:10|unique:countries',
            'region' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Country::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'region' => $request->region,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_active' => true,
        ]);

        return back()->with('success', 'Country added. Run "Sync Metrics" to fetch its data.');
    }

    public function destroyCountry(Country $country)
    {
        $country->delete();
        return back()->with('success', 'Country deleted successfully.');
    }
    public function toggleCountryStatus(Request $request)
    {
        $country = Country::findOrFail($request->country_id);
        $country->is_active = !$country->is_active;
        $country->save();
        return back()->with('success', $country->name . ' has been ' . ($country->is_active ? 'activated' : 'deactivated'));
    }

    public function bulkToggleCountries(Request $request)
    {
        $status = $request->status === 'activate' ? true : false;
        Country::query()->update(['is_active' => $status]);
        return back()->with('success', 'All countries have been ' . ($status ? 'activated' : 'deactivated'));
    }
}
