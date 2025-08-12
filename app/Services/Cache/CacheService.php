<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Get cached data with graceful degradation
     *
     * @param  string  $cacheKey  The cache key
     * @param  int  $ttl  Time to live in seconds
     * @param  callable  $dataProvider  Function that fetches fresh data
     * @param  string  $operation  Operation name for logging
     * @param  mixed  $identifier  Identifier for logging (query, id, etc.)
     */
    public function remember(
        string $cacheKey,
        int $ttl,
        callable $dataProvider,
        string $operation,
        mixed $identifier
    ): array {
        try {
            // Try to get fresh data from cache
            return Cache::remember($cacheKey, $ttl, function () use (
                $dataProvider,
                $cacheKey,
                $operation,
                $identifier,
                $ttl
            ) {
                try {
                    $data = $dataProvider();

                    // Store stale cache for graceful degradation (7x longer TTL)
                    $this->storeStaleCache($cacheKey, $data, $ttl * 7);

                    return $data;
                } catch (\Exception $e) {
                    Log::error("Cache data provider failed for {$operation}", [
                        'operation' => $operation,
                        'identifier' => $identifier,
                        'error' => $e->getMessage(),
                    ]);

                    // If data provider fails, try to get stale data from cache
                    $staleData = $this->getStaleCache($cacheKey);
                    if ($staleData) {
                        Log::info("Serving stale cache data for {$operation}", [
                            'operation' => $operation,
                            'identifier' => $identifier,
                        ]);

                        return $staleData;
                    }

                    // If no stale data, re-throw the exception
                    throw $e;
                }
            });
        } catch (\Exception $e) {
            Log::error("Cache error for {$operation} - No cached data available", [
                'operation' => $operation,
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);

            // Re-throw to let the calling service handle the error
            throw $e;
        }
    }

    /**
     * Store data in stale cache for graceful degradation
     */
    private function storeStaleCache(string $cacheKey, array $data, int $ttl): void
    {
        Cache::put($cacheKey.':stale', $data, $ttl);
    }

    /**
     * Get stale cache data
     */
    private function getStaleCache(string $cacheKey): ?array
    {
        return Cache::get($cacheKey.':stale');
    }

    /**
     * Generate cache key from components
     */
    public function generateKey(string ...$components): string
    {
        return implode(':', $components);
    }

    /**
     * Generate cache key for search queries (with normalization)
     */
    public function generateSearchKey(string $prefix, string $query): string
    {
        return $prefix.':'.md5(strtolower(trim($query)));
    }
}
