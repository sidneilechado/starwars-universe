<?php

namespace App\Jobs;

use App\Models\SearchQuery;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ComputeSearchStatistics implements ShouldQueue
{
    const CACHE_KEY = 'search_statistics';

    const CACHE_TTL = 604800; // 7 days in seconds

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            Log::info('Starting search statistics computation');

            // Get data from the last 24 hours for meaningful statistics
            $recentQueries = SearchQuery::recent(24)->get();

            if ($recentQueries->isEmpty()) {
                Log::info('No recent queries found, skipping statistics computation');

                return;
            }

            $statistics = [
                'top_queries' => $this->computeTopQueries($recentQueries),
                'average_response_time_ms' => $this->computeAverageResponseTime($recentQueries),
                'response_time_percentiles' => $this->computeResponseTimePercentiles($recentQueries),
                'slow_queries' => $this->computeSlowQueries($recentQueries),
                'search_quality' => $this->computeSearchQuality($recentQueries),
                'user_behavior' => $this->computeUserBehavior($recentQueries),
                'geographic_insights' => $this->computeGeographicInsights($recentQueries),
                'content_analysis' => $this->computeContentAnalysis($recentQueries),
                'hourly_distribution' => $this->computeHourlyDistribution($recentQueries),
                'total_searches' => $recentQueries->count(),
                'computed_at' => now(),
            ];

            Cache::put(self::CACHE_KEY, $statistics, self::CACHE_TTL);

            Log::info('Finished statistics computation');
        } catch (\Exception $e) {
            Log::error('Error computing search statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function computeTopQueries($queries): array
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

    private function computeAverageResponseTime($queries): int
    {
        return (int) $queries->avg('response_time_ms');
    }

    private function computeHourlyDistribution($queries): array
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

    private function computeResponseTimePercentiles($queries): array
    {
        $responseTimes = $queries->pluck('response_time_ms')->sort()->values();
        $count = $responseTimes->count();

        if ($count === 0) {
            return [
                'p50' => 0,
                'p90' => 0,
                'p95' => 0,
                'p99' => 0,
            ];
        }

        return [
            'p50' => $this->getPercentile($responseTimes, 50),
            'p90' => $this->getPercentile($responseTimes, 90),
            'p95' => $this->getPercentile($responseTimes, 95),
            'p99' => $this->getPercentile($responseTimes, 99),
        ];
    }

    private function getPercentile($sortedArray, $percentile): int
    {
        $count = $sortedArray->count();
        $index = ($percentile / 100) * ($count - 1);

        if ($index == floor($index)) {
            return (int) $sortedArray->get((int) $index);
        }

        $lower = (int) $sortedArray->get((int) floor($index));
        $upper = (int) $sortedArray->get((int) ceil($index));

        return (int) ($lower + ($upper - $lower) * ($index - floor($index)));
    }

    private function computeSlowQueries($queries, $thresholdMs = 1000): array
    {
        $slowQueries = $queries->where('response_time_ms', '>', $thresholdMs);
        $totalQueries = $queries->count();

        return [
            'count' => $slowQueries->count(),
            'percentage' => $totalQueries > 0 ? round(($slowQueries->count() / $totalQueries) * 100, 1) : 0,
            'threshold_ms' => $thresholdMs,
            'slowest_query' => $slowQueries->isEmpty() ? null : [
                'query' => $slowQueries->sortByDesc('response_time_ms')->first()->query,
                'type' => $slowQueries->sortByDesc('response_time_ms')->first()->type,
                'response_time_ms' => $slowQueries->sortByDesc('response_time_ms')->first()->response_time_ms,
            ],
        ];
    }

    private function computeSearchQuality($queries): array
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

    private function computeUserBehavior($queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'device_types' => [],
                'browsers' => [],
                'search_type_distribution' => [],
            ];
        }

        $deviceTypes = $queries->groupBy(function ($query) {
            return $this->parseDeviceType($query->user_agent);
        })->map(function ($group, $device) use ($totalQueries) {
            return [
                'device' => $device,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->sortByDesc('count')->values()->toArray();

        $browsers = $queries->groupBy(function ($query) {
            return $this->parseBrowser($query->user_agent);
        })->map(function ($group, $browser) use ($totalQueries) {
            return [
                'browser' => $browser,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->sortByDesc('count')->take(5)->values()->toArray();

        $typeDistribution = $queries->groupBy('type')->map(function ($group, $type) use ($totalQueries) {
            return [
                'type' => $type,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->values()->toArray();

        return [
            'device_types' => $deviceTypes,
            'browsers' => $browsers,
            'search_type_distribution' => $typeDistribution,
        ];
    }

    private function parseDeviceType($userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        $userAgent = strtolower($userAgent);

        if (preg_match('/(tablet|ipad)/', $userAgent)) {
            return 'Tablet';
        }

        if (preg_match('/(mobile|phone|android|iphone)/', $userAgent)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private function parseBrowser($userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'chrome') !== false && strpos($userAgent, 'edg') === false) {
            return 'Chrome';
        }

        if (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        }

        if (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'chrome') === false) {
            return 'Safari';
        }

        if (strpos($userAgent, 'edg') !== false) {
            return 'Edge';
        }

        if (strpos($userAgent, 'opera') !== false || strpos($userAgent, 'opr') !== false) {
            return 'Opera';
        }

        return 'Other';
    }

    private function computeGeographicInsights($queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'top_countries' => [],
                'unique_ips' => 0,
                'international_percentage' => 0,
            ];
        }

        $ipGroups = $queries->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->map(function ($group) {
                return [
                    'ip' => $group->first()->ip_address,
                    'count' => $group->count(),
                    'country' => $this->getCountryFromIp($group->first()->ip_address),
                ];
            });

        $countryGroups = $ipGroups->groupBy('country')
            ->map(function ($group, $country) use ($totalQueries) {
                $totalCount = $group->sum('count');

                return [
                    'country' => $country ?: 'Unknown',
                    'count' => $totalCount,
                    'percentage' => round(($totalCount / $totalQueries) * 100, 1),
                    'unique_ips' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values()
            ->toArray();

        $domesticQueries = $ipGroups->where('country', 'United States')->sum('count');
        $internationalPercentage = $totalQueries > 0 ?
            round((($totalQueries - $domesticQueries) / $totalQueries) * 100, 1) : 0;

        return [
            'top_countries' => $countryGroups,
            'unique_ips' => $ipGroups->count(),
            'international_percentage' => $internationalPercentage,
        ];
    }

    private function getCountryFromIp($ipAddress): ?string
    {
        if (! $ipAddress || $ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return 'Local';
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'Private/Reserved';
        }

        return 'Unknown';
    }

    private function computeContentAnalysis($queries): array
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
