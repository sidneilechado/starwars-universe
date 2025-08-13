<?php

namespace App\Contracts\Statistics;

use Illuminate\Support\Collection;

interface QueryAnalysisServiceInterface
{
    public function computeTopQueries(Collection $queries): array;

    public function computeHourlyDistribution(Collection $queries): array;

    public function computeSearchQuality(Collection $queries): array;

    public function computeContentAnalysis(Collection $queries): array;
}