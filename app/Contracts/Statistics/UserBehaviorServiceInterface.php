<?php

namespace App\Contracts\Statistics;

use Illuminate\Support\Collection;

interface UserBehaviorServiceInterface
{
    public function computeUserBehavior(Collection $queries): array;

    public function computeGeographicInsights(Collection $queries): array;
}