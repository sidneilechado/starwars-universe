<?php

namespace App\Services\Statistics;

use App\Contracts\Statistics\QueryAnalysisServiceInterface;
use Illuminate\Support\Collection;

class QueryAnalysisService implements QueryAnalysisServiceInterface
{
    public function computeTopQueries(Collection $queries): array
    {
        $queryGroups = $queries->groupBy(function ($query) {
            return strtolower(trim($query->query)).'|'.$query->type;
        });

        $queryStats = $queryGroups->map(function ($group, $key) {
            [$query, $type] = explode('|', $key);

            return [
                'query' => $query,
                'type' => $type,
                'count' => $group->count(),
                'percentage' => 0, // Will be calculated after sorting
            ];
        });

        $sortedQueries = $queryStats->sortByDesc('count')->values();
        $totalSearches = $queries->count();

        // Calculate percentages and take top 5
        return $sortedQueries->take(5)->map(function ($item) use ($totalSearches) {
            $item['percentage'] = round(($item['count'] / $totalSearches) * 100, 1);

            return $item;
        })->toArray();
    }

    public function computeHourlyDistribution(Collection $queries): array
    {
        $hourlyData = [];

        // Initialize all hours with 0
        for ($hour = 0; $hour < 24; $hour++) {
            $hourlyData[$hour] = [
                'hour' => $hour,
                'count' => 0,
                'percentage' => 0.0,
            ];
        }

        // Count queries by hour
        $queriesByHour = $queries->groupBy(function ($query) {
            return $query->searched_at->format('H');
        });

        $totalSearches = $queries->count();

        foreach ($queriesByHour as $hour => $hourQueries) {
            $count = $hourQueries->count();
            $hourlyData[(int) $hour] = [
                'hour' => (int) $hour,
                'count' => $count,
                'percentage' => round(($count / $totalSearches) * 100, 1),
            ];
        }

        return array_values($hourlyData);
    }

    public function computeSearchQuality(Collection $queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'zero_result_searches' => ['count' => 0, 'percentage' => 0],
                'effectiveness_ratio' => 0,
                'results_distribution' => [],
            ];
        }

        $zeroResultQueries = $queries->where('results_count', 0);
        $zeroResultCount = $zeroResultQueries->count();

        $resultsDistribution = $queries->groupBy(function ($query) {
            if ($query->results_count === 0) {
                return '0';
            }
            if ($query->results_count <= 5) {
                return '1-5';
            }
            if ($query->results_count <= 20) {
                return '6-20';
            }
            if ($query->results_count <= 50) {
                return '21-50';
            }

            return '50+';
        })->map(function ($group, $range) use ($totalQueries) {
            return [
                'range' => $range,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->values()->toArray();

        return [
            'zero_result_searches' => [
                'count' => $zeroResultCount,
                'percentage' => round(($zeroResultCount / $totalQueries) * 100, 1),
            ],
            'effectiveness_ratio' => round((($totalQueries - $zeroResultCount) / $totalQueries) * 100, 1),
            'results_distribution' => $resultsDistribution,
            'average_results_per_search' => round($queries->avg('results_count'), 1),
        ];
    }

    public function computeContentAnalysis(Collection $queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'query_length_distribution' => [],
                'average_query_length' => 0,
                'most_common_words' => [],
                'search_patterns' => [],
            ];
        }

        $queryLengths = $queries->pluck('query')->map(function ($query) {
            return strlen(trim($query));
        });

        $lengthDistribution = $queryLengths->groupBy(function ($length) {
            if ($length <= 5) {
                return '1-5';
            }
            if ($length <= 15) {
                return '6-15';
            }
            if ($length <= 30) {
                return '16-30';
            }
            if ($length <= 50) {
                return '31-50';
            }

            return '50+';
        })->map(function ($group, $range) use ($totalQueries) {
            return [
                'range' => $range,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->values()->toArray();

        $allWords = $queries->pluck('query')
            ->flatMap(function ($query) {
                return array_filter(
                    array_map('trim',
                        preg_split('/[\s,]+/', strtolower($query))
                    ),
                    function ($word) {
                        return strlen($word) >= 3;
                    }
                );
            })
            ->countBy()
            ->sortByDesc(function ($count) {
                return $count;
            })
            ->take(10)
            ->map(function ($count, $word) use ($totalQueries) {
                return [
                    'word' => $word,
                    'count' => $count,
                    'percentage' => round(($count / $totalQueries) * 100, 1),
                ];
            })
            ->values()
            ->toArray();

        $searchPatterns = [
            'contains_quotes' => $queries->filter(function ($query) {
                return strpos($query->query, '"') !== false;
            })->count(),
            'single_character' => $queries->filter(function ($query) {
                return strlen(trim($query->query)) === 1;
            })->count(),
            'numeric_queries' => $queries->filter(function ($query) {
                return is_numeric(trim($query->query));
            })->count(),
            'very_long_queries' => $queries->filter(function ($query) {
                return strlen(trim($query->query)) > 100;
            })->count(),
        ];

        return [
            'query_length_distribution' => $lengthDistribution,
            'average_query_length' => round($queryLengths->avg(), 1),
            'most_common_words' => $allWords,
            'search_patterns' => array_map(function ($count) use ($totalQueries) {
                return [
                    'count' => $count,
                    'percentage' => $totalQueries > 0 ? round(($count / $totalQueries) * 100, 1) : 0,
                ];
            }, $searchPatterns),
        ];
    }
}