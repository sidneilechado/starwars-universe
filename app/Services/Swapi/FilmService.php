<?php

namespace App\Services\Swapi;

use App\Services\Cache\CacheService;
use App\Services\Swapi\SwapiHttpClient;

class FilmService
{
    private const CACHE_TTL_SEARCH = 21600; // 6 hours
    private const CACHE_TTL_INDIVIDUAL = 86400; // 24 hours

    public function __construct(
        private SwapiHttpClient $httpClient,
        private CacheService $cacheService
    ) {}

    /**
     * Search films by title
     */
    public function search(string $query): array
    {
        $cacheKey = $this->cacheService->generateSearchKey('swapi:search:films', $query);

        try {
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL_SEARCH,
                fn() => $this->performFilmSearch($query),
                'film search',
                $query
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while searching films');
        }
    }

    /**
     * Get film by ID
     */
    public function getById(int $id): array
    {
        $cacheKey = $this->cacheService->generateKey('swapi:film', (string)$id);

        try {
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL_INDIVIDUAL,
                fn() => $this->performFilmFetch($id),
                'film fetch',
                $id
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while fetching film details');
        }
    }

    /**
     * Perform the actual film search API call
     */
    private function performFilmSearch(string $query): array
    {
        $response = $this->httpClient->searchFilms($query);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch films from SWAPI');
        }

        $data = $response->json();
        $films = $data['result'] ?? [];

        return [
            'success' => true,
            'data' => $films,
            'total' => count($films),
        ];
    }

    /**
     * Perform the actual film fetch API call
     */
    private function performFilmFetch(int $id): array
    {
        $response = $this->httpClient->getFilm($id);

        if (!$response->successful()) {
            throw new \Exception('Film not found');
        }

        $filmData = $response->json()['result'] ?? null;

        // Ensure characters array is available for the frontend
        if ($filmData && isset($filmData['properties']['characters'])) {
            // Characters are already URLs in the SWAPI response, no need to modify
        }

        return [
            'success' => true,
            'data' => $filmData,
        ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'error' => $message,
            'data' => [],
            'total' => 0,
        ];
    }
}