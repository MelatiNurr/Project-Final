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
Route::get('/country/{id}', [App\Http\Controllers\PublicController::class, 'showCountry'])->name('country.show');


Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
});
