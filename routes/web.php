<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/intelligence', function () {
    return view('intelligence');
});

Route::get('/countries', [App\Http\Controllers\PublicController::class, 'countriesIndex'])->name('countries.index');
Route::get('/countries/settings', [App\Http\Controllers\PublicController::class, 'countriesSettings'])->name('countries.settings');
Route::post('/countries/settings/toggle', [App\Http\Controllers\PublicController::class, 'toggleCountryStatus'])->name('countries.toggle');
Route::post('/countries/settings/bulk', [App\Http\Controllers\PublicController::class, 'bulkToggleCountries'])->name('countries.bulk');
Route::get('/country/{id}', [App\Http\Controllers\PublicController::class, 'showCountry'])->name('country.show');


Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::post('/admin/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::delete('/admin/ports/{port}', [AdminController::class, 'destroyPort'])->name('admin.ports.destroy');
    Route::delete('/admin/articles/{article}', [AdminController::class, 'destroyArticle'])->name('admin.articles.destroy');
});
