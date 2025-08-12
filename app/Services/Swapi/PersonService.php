<?php

namespace App\Services\Swapi;

use App\Services\Cache\CacheService;

class PersonService
{
    private const CACHE_TTL_SEARCH = 21600; // 6 hours

    private const CACHE_TTL_INDIVIDUAL = 86400; // 24 hours

    public function __construct(
        private SwapiHttpClient $httpClient,
        private CacheService $cacheService
    ) {}

    /**
     * Search people by name
     */
    public function search(string $query): array
    {
        $cacheKey = $this->cacheService->generateSearchKey('swapi:search:people', $query);

        try {
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL_SEARCH,
                fn () => $this->performPeopleSearch($query),
                'people search',
                $query
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while searching people');
        }
    }

    /**
     * Get person by ID
     */
    public function getById(int $id): array
    {
        $cacheKey = $this->cacheService->generateKey('swapi:person', (string) $id);

        try {
            return $this->cacheService->remember(
                $cacheKey,
                self::CACHE_TTL_INDIVIDUAL,
                fn () => $this->performPersonFetch($id),
                'person fetch',
                $id
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while fetching person details');
        }
    }

    /**
     * Perform the actual people search API call
     */
    private function performPeopleSearch(string $query): array
    {
        $response = $this->httpClient->searchPeople($query);

        if (! $response->successful()) {
            throw new \Exception('Failed to fetch people from SWAPI');
        }

        $data = $response->json();
        $people = $data['result'] ?? [];

        return [
            'success' => true,
            'data' => $people,
            'total' => count($people),
        ];
    }

    /**
     * Perform the actual person fetch API call
     */
    private function performPersonFetch(int $id): array
    {
        $response = $this->httpClient->getPerson($id);

        if (! $response->successful()) {
            throw new \Exception('Person not found');
        }

        $personData = $response->json()['result'] ?? null;

        return [
            'success' => true,
            'data' => $personData,
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
