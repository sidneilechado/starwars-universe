<?php

namespace App\Services\Statistics;

use App\Contracts\Statistics\PerformanceAnalysisServiceInterface;
use Illuminate\Support\Collection;

class PerformanceAnalysisService implements PerformanceAnalysisServiceInterface
{
    public function computeAverageResponseTime(Collection $queries): int
    {
        return (int) $queries->avg('response_time_ms');
    }

    public function computeResponseTimePercentiles(Collection $queries): array
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

    public function computeSlowQueries(Collection $queries, int $thresholdMs = 1000): array
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

    private function getPercentile(Collection $sortedArray, int $percentile): int
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
}