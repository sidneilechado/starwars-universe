<?php

use App\Http\Controllers\StarWars\StarWarsWebController;
use Illuminate\Support\Facades\Route;

// Web routes (return Inertia views)
Route::get('/', [StarWarsWebController::class, 'index'])->name('starwars.index');
Route::get('/films/{id}', [StarWarsWebController::class, 'showFilm'])->name('starwars.film')->where('id', '[0-9]+');
Route::get('/people/{id}', [StarWarsWebController::class, 'showPerson'])->name('starwars.person')->where('id', '[0-9]+');
Route::get('/statistics', [StarWarsWebController::class, 'showStatistics'])->name('starwars.statistics');
