<?php

use App\Http\Controllers\StarWars\StarWarsApiController;
use App\Http\Controllers\StarWars\StatisticsController;
use Illuminate\Support\Facades\Route;

// API routes (return JSON) - automatically prefixed with /api
Route::get('/search', [StarWarsApiController::class, 'search'])->name('api.search');
Route::get('/films/{id}', [StarWarsApiController::class, 'getFilm'])->name('api.film')->where('id', '[0-9]+');
Route::get('/people/{id}', [StarWarsApiController::class, 'getPerson'])->name('api.person')->where('id', '[0-9]+');
Route::get('/statistics', [StatisticsController::class, 'index'])->name('api.statistics');
