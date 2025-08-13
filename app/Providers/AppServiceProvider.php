<?php

namespace App\Providers;

use App\Events\SearchPerformed;
use App\Listeners\LogSearchQuery;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind statistics service interfaces to implementations
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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(SearchPerformed::class, LogSearchQuery::class);
    }
}
