<?php

namespace App\Contracts\Statistics;

use Illuminate\Support\Collection;

interface PerformanceAnalysisServiceInterface
{
    public function computeAverageResponseTime(Collection $queries): int;

    public function computeResponseTimePercentiles(Collection $queries): array;

    public function computeSlowQueries(Collection $queries, int $thresholdMs = 1000): array;
}