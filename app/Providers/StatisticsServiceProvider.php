<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class StatisticsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Statistics\StatisticsComputationServiceInterface::class,
            \App\Services\Statistics\StatisticsComputationService::class
        );

        $this->app->bind(
            \App\Contracts\Statistics\QueryAnalysisServiceInterface::class,
            \App\Services\Statistics\QueryAnalysisService::class
        );

        $this->app->bind(
            \App\Contracts\Statistics\PerformanceAnalysisServiceInterface::class,
            \App\Services\Statistics\PerformanceAnalysisService::class
        );

        $this->app->bind(
            \App\Contracts\Statistics\UserBehaviorServiceInterface::class,
            \App\Services\Statistics\UserBehaviorService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
