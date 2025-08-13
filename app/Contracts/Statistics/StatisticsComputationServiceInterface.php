<?php

namespace App\Contracts\Statistics;

interface StatisticsComputationServiceInterface
{
    public function computeAndCacheStatistics(): void;
}