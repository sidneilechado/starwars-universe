<?php

use App\Http\Controllers\StarWars\StarWarsApiController;
use App\Http\Controllers\StarWars\StarWarsWebController;
use Illuminate\Support\Facades\Route;

// Web routes (return Inertia views)
Route::get('/', [StarWarsWebController::class, 'index'])->name('starwars.index');
Route::get('/films/{id}', [StarWarsWebController::class, 'showFilm'])->name('starwars.film');
Route::get('/people/{id}', [StarWarsWebController::class, 'showPerson'])->name('starwars.person');
Route::get('/statistics', [StarWarsWebController::class, 'showStatistics'])->name('starwars.statistics');

// API routes (return JSON)
Route::prefix('api')->group(function () {
    Route::get('/search', [StarWarsApiController::class, 'search'])->name('api.search');
    Route::get('/films/{id}', [StarWarsApiController::class, 'getFilm'])->name('api.film');
    Route::get('/people/{id}', [StarWarsApiController::class, 'getPerson'])->name('api.person');
});
