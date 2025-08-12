<?php

namespace App\Http\Controllers\StarWars;

use App\Events\SearchPerformed;
use App\Http\Controllers\Controller;
use App\Services\Swapi\StarWarsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StarWarsApiController extends Controller
{
    public function __construct(
        private StarWarsService $starWarsService
    ) {}

    /**
     * Search films or people
     */
    public function search(Request $request): JsonResponse
    {
        $startTime = microtime(true);

        $request->validate([
            'query' => 'required|string|min:1|max:100',
            'type' => 'required|string|in:films,people',
        ]);

        $query = trim($request->query('query'));
        $type = $request->query('type');

        $result = $this->starWarsService->search($query, $type);

        $responseTime = (int) ((microtime(true) - $startTime) * 1000);

        event(new SearchPerformed(
            $query,
            $type,
            $result['total'] ?? 0,
            $responseTime,
            $request->ip(),
            $request->userAgent()
        ));

        return response()->json($result);
    }

    /**
     * Get film by ID
     */
    public function getFilm(int $id): JsonResponse
    {
        $result = $this->starWarsService->getFilmById($id);

        if (! $result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }

    /**
     * Get person by ID
     */
    public function getPerson(int $id): JsonResponse
    {
        $result = $this->starWarsService->getPersonById($id);

        if (! $result['success']) {
            return response()->json($result, 404);
        }

        return response()->json($result);
    }
}
