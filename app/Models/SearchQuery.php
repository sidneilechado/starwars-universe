<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('searched_at', '>=', now()->subHours($hours));
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeWithResults($query)
    {
        return $query->where('has_results', true);
    }

    public function scopeWithoutResults($query)
    {
        return $query->where('has_results', false);
    }

    public function scopeByCountry($query, $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }

    public function scopeByDevice($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}