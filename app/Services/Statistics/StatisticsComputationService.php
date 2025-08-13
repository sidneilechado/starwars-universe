<?php

namespace App\Services\Statistics;

use App\Contracts\Statistics\StatisticsComputationServiceInterface;
use App\Contracts\Statistics\QueryAnalysisServiceInterface;
use App\Contracts\Statistics\UserBehaviorServiceInterface;
use App\Contracts\Statistics\PerformanceAnalysisServiceInterface;
use App\Models\SearchQuery;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StatisticsComputationService implements StatisticsComputationServiceInterface
{
    const CACHE_KEY = 'search_statistics';
    const CACHE_TTL = 604800; // 7 days in seconds

    public function __construct(
        private readonly QueryAnalysisServiceInterface $queryAnalysisService,
        private readonly UserBehaviorServiceInterface $userBehaviorService,
        private readonly PerformanceAnalysisServiceInterface $performanceAnalysisService
    ) {}

    public function computeAndCacheStatistics(): void
    {
        try {
            Log::info('Starting search statistics computation');

            $recentQueries = SearchQuery::recent(24)->get();

            if ($recentQueries->isEmpty()) {
                Log::info('No recent queries found, skipping statistics computation');
                return;
            }

            $statistics = $this->computeStatistics($recentQueries);

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

    private function computeStatistics(Collection $queries): array
    {
        return [
            'top_queries' => $this->queryAnalysisService->computeTopQueries($queries),
            'average_response_time_ms' => $this->performanceAnalysisService->computeAverageResponseTime($queries),
            'response_time_percentiles' => $this->performanceAnalysisService->computeResponseTimePercentiles($queries),
            'slow_queries' => $this->performanceAnalysisService->computeSlowQueries($queries),
            'search_quality' => $this->queryAnalysisService->computeSearchQuality($queries),
            'user_behavior' => $this->userBehaviorService->computeUserBehavior($queries),
            'geographic_insights' => $this->userBehaviorService->computeGeographicInsights($queries),
            'content_analysis' => $this->queryAnalysisService->computeContentAnalysis($queries),
            'hourly_distribution' => $this->queryAnalysisService->computeHourlyDistribution($queries),
            'total_searches' => $queries->count(),
            'computed_at' => now(),
        ];
    }
}