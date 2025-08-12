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
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(SearchPerformed::class, LogSearchQuery::class);
    }
}
