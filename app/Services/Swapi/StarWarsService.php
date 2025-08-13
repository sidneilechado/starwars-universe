<?php

namespace App\Services\Swapi;

use App\Services\Swapi\FilmService;
use App\Services\Swapi\PersonService;
use InvalidArgumentException;

class StarWarsService
{
    public function __construct(
        private readonly FilmService $filmService,
        private readonly PersonService $personService
    ) {}

    /**
     * Search films by title
     */
    public function searchFilms(string $query): array
    {
        return $this->filmService->search($query);
    }

    /**
     * Search people by name
     */
    public function searchPeople(string $query): array
    {
        return $this->personService->search($query);
    }

    /**
     * Get film by ID
     */
    public function getFilmById(int $id): array
    {
        return $this->filmService->getById($id);
    }

    /**
     * Get person by ID
     */
    public function getPersonById(int $id): array
    {
        return $this->personService->getById($id);
    }

    /**
     * Generic search method that delegates based on type
     * 
     * @throws InvalidArgumentException
     */
    public function search(string $query, string $type): array
    {
        return match ($type) {
            'films' => $this->searchFilms($query),
            'people' => $this->searchPeople($query),
            default => throw new InvalidArgumentException("Invalid search type: {$type}")
        };
    }
}