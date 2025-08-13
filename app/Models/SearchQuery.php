<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Search Query Model
 *
 * Stores search queries made through the Star Wars Universe application,
 * including query details, performance metrics, and user information.
 *
 * @property int $id
 * @property string $query The search query string
 * @property string $type The type of search (people, planets, starships, etc.)
 * @property int $results_count Number of results returned
 * @property int $response_time_ms Response time in milliseconds
 * @property string|null $ip_address IP address of the user
 * @property string|null $user_agent User agent string
 * @property string|null $session_id Session identifier
 * @property string|null $country_code Country code of the user
 * @property string|null $device_type Type of device used
 * @property string|null $browser Browser used
 * @property bool $has_results Whether the search returned results
 * @property string|null $referrer Referrer URL
 * @property array|null $query_metadata Additional metadata about the query
 * @property \Illuminate\Support\Carbon $searched_at When the search was performed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class SearchQuery extends Model
{
    protected $fillable = [
        'query',
        'type',
        'results_count',
        'response_time_ms',
        'ip_address',
        'user_agent',
        'session_id',
        'country_code',
        'device_type',
        'browser',
        'has_results',
        'referrer',
        'query_metadata',
        'searched_at',
    ];

    protected $casts = [
        'searched_at' => 'datetime',
        'has_results' => 'boolean',
        'query_metadata' => 'array',
    ];

    /**
     * Scope to filter searches within a specified number of hours
     *
     * @param Builder $query
     * @param int $hours Number of hours to look back (default: 24)
     * @return Builder
     */
    public function scopeRecent(Builder $query, int $hours = 24): Builder
    {
        return $query->where('searched_at', '>=', now()->subHours($hours));
    }
}
