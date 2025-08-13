<?php

namespace App\Http\Controllers\StarWars;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class StatisticsController extends Controller
{
    /**
     * Get search statistics
     */
    public function index(): JsonResponse
    {
        $statistics = Cache::get('search_statistics');

        if (! $statistics) {
            return response()->json([
                'success' => false,
                'error' => 'Statistics not available',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'top_queries' => $statistics['top_queries'] ?? [],
                'average_response_time_ms' => $statistics['average_response_time_ms'] ?? 0,
                'hourly_distribution' => $statistics['hourly_distribution'] ?? [],
                'total_searches' => $statistics['total_searches'] ?? 0,
                'computed_at' => $statistics['computed_at'] ?? null,
            ],
        ]);
    }
}