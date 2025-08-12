<?php

namespace App\Listeners;

use App\Events\SearchPerformed;
use App\Models\SearchQuery;
use Illuminate\Support\Facades\Log;

class LogSearchQuery
{
    public function handle(SearchPerformed $event): void
    {
        try {
            SearchQuery::create([
                'query' => $event->query,
                'type' => $event->type,
                'results_count' => $event->resultsCount,
                'response_time_ms' => $event->responseTimeMs,
                'ip_address' => $event->ipAddress,
                'user_agent' => $event->userAgent,
                'searched_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log search query', [
                'query' => $event->query,
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);
        }
    }
}