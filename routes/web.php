<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');

    Route::get('/intelligence', function () {
        return view('intelligence');
    });

    Route::get('/countries', [App\Http\Controllers\PublicController::class, 'countriesIndex'])->name('countries.index');
    Route::get('/country/{id}', [App\Http\Controllers\PublicController::class, 'showCountry'])->name('country.show');

    // Watchlist
    Route::get('/watchlist', [App\Http\Controllers\PublicController::class, 'watchlistIndex'])->name('watchlist.index');
    Route::post('/watchlist/toggle', [App\Http\Controllers\PublicController::class, 'toggleWatchlist'])->name('watchlist.toggle');
});

Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    
    // Ports
    Route::post('/admin/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::delete('/admin/ports/{port}', [AdminController::class, 'destroyPort'])->name('admin.ports.destroy');
    
    // Articles
    Route::delete('/admin/articles/{article}', [AdminController::class, 'destroyArticle'])->name('admin.articles.destroy');
    
    // Users
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
    
    // Countries
    Route::post('/admin/countries', [AdminController::class, 'storeCountry'])->name('admin.countries.store');
    Route::post('/admin/countries/toggle', [AdminController::class, 'toggleCountryStatus'])->name('admin.countries.toggle');
    Route::post('/admin/countries/bulk', [AdminController::class, 'bulkToggleCountries'])->name('admin.countries.bulk');
    Route::delete('/admin/countries/{country}', [AdminController::class, 'destroyCountry'])->name('admin.countries.destroy');
});
