<?php

namespace App\Services\Swapi;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SwapiHttpClient
{
    private const BASE_URL = 'https://swapi.tech/api';

    private const TIMEOUT = 30;

    /**
     * Make a GET request to a SWAPI endpoint
     */
    public function get(string $endpoint): Response
    {
        return Http::timeout(self::TIMEOUT)
            ->get(self::BASE_URL.$endpoint);
    }

    /**
     * Search films by title
     */
    public function searchFilms(string $query): Response
    {
        return $this->get('/films?title='.urlencode($query));
    }

    /**
     * Search people by name
     */
    public function searchPeople(string $query): Response
    {
        Log::info('Searching people', ['query' => urlencode($query)]);

        return $this->get('/people?name='.urlencode($query).'&expanded=true');
    }

    /**
     * Get film by ID
     */
    public function getFilm(int $id): Response
    {
        return $this->get("/films/{$id}");
    }

    /**
     * Get person by ID
     */
    public function getPerson(int $id): Response
    {
        return $this->get("/people/{$id}");
    }
}
