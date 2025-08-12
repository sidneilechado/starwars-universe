<?php

namespace App\Http\Controllers\StarWars;

use App\Http\Controllers\Controller;
use App\Services\Swapi\StarWarsService;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class StarWarsWebController extends Controller
{
    public function __construct(
        private StarWarsService $starWarsService
    ) {}

    /**
     * Show the main search page
     */
    public function index()
    {
        return Inertia::render('StarWars/Search');
    }

    /**
     * Show film details page
     */
    public function showFilm(int $id)
    {
        $result = $this->starWarsService->getFilmById($id);

        if (! $result['success']) {
            abort(404, $result['error'] ?? 'Film not found');
        }

        return Inertia::render('StarWars/FilmDetails', [
            'filmId' => $id,
            'film' => $result['data'],
        ]);
    }

    /**
     * Show person details page
     */
    public function showPerson(int $id)
    {
        $result = $this->starWarsService->getPersonById($id);

        if (! $result['success']) {
            abort(404, $result['error'] ?? 'Person not found');
        }

        return Inertia::render('StarWars/CharacterDetails', [
            'characterId' => $id,
            'character' => $result['data'],
        ]);
    }

    /**
     * Show statistics page
     */
    public function showStatistics()
    {
        $statistics = Cache::get('search_statistics');
        $error = null;

        if (! $statistics) {
            $error = 'Statistics not available yet. Search for some films or characters first!';
        }

        return Inertia::render('StarWars/Statistics', [
            'statistics' => $statistics,
            'error' => $error,
        ]);
    }
}
