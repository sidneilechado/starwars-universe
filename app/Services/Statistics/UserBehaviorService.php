<?php

namespace App\Services\Statistics;

use App\Contracts\Statistics\UserBehaviorServiceInterface;
use Illuminate\Support\Collection;

class UserBehaviorService implements UserBehaviorServiceInterface
{
    public function computeUserBehavior(Collection $queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'device_types' => [],
                'browsers' => [],
                'search_type_distribution' => [],
            ];
        }

        $deviceTypes = $queries->groupBy(function ($query) {
            return $this->parseDeviceType($query->user_agent);
        })->map(function ($group, $device) use ($totalQueries) {
            return [
                'device' => $device,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->sortByDesc('count')->values()->toArray();

        $browsers = $queries->groupBy(function ($query) {
            return $this->parseBrowser($query->user_agent);
        })->map(function ($group, $browser) use ($totalQueries) {
            return [
                'browser' => $browser,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->sortByDesc('count')->take(5)->values()->toArray();

        $typeDistribution = $queries->groupBy('type')->map(function ($group, $type) use ($totalQueries) {
            return [
                'type' => $type,
                'count' => $group->count(),
                'percentage' => round(($group->count() / $totalQueries) * 100, 1),
            ];
        })->values()->toArray();

        return [
            'device_types' => $deviceTypes,
            'browsers' => $browsers,
            'search_type_distribution' => $typeDistribution,
        ];
    }

    public function computeGeographicInsights(Collection $queries): array
    {
        $totalQueries = $queries->count();

        if ($totalQueries === 0) {
            return [
                'top_countries' => [],
                'unique_ips' => 0,
                'international_percentage' => 0,
            ];
        }

        $ipGroups = $queries->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->map(function ($group) {
                return [
                    'ip' => $group->first()->ip_address,
                    'count' => $group->count(),
                    'country' => $this->getCountryFromIp($group->first()->ip_address),
                ];
            });

        $countryGroups = $ipGroups->groupBy('country')
            ->map(function ($group, $country) use ($totalQueries) {
                $totalCount = $group->sum('count');

                return [
                    'country' => $country ?: 'Unknown',
                    'count' => $totalCount,
                    'percentage' => round(($totalCount / $totalQueries) * 100, 1),
                    'unique_ips' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values()
            ->toArray();

        $domesticQueries = $ipGroups->where('country', 'United States')->sum('count');
        $internationalPercentage = $totalQueries > 0 ?
            round((($totalQueries - $domesticQueries) / $totalQueries) * 100, 1) : 0;

        return [
            'top_countries' => $countryGroups,
            'unique_ips' => $ipGroups->count(),
            'international_percentage' => $internationalPercentage,
        ];
    }

    private function parseDeviceType(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        $userAgent = strtolower($userAgent);

        if (preg_match('/(tablet|ipad)/', $userAgent)) {
            return 'Tablet';
        }

        if (preg_match('/(mobile|phone|android|iphone)/', $userAgent)) {
            return 'Mobile';
        }

        return 'Desktop';
    }

    private function parseBrowser(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'Unknown';
        }

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'chrome') !== false && strpos($userAgent, 'edg') === false) {
            return 'Chrome';
        }

        if (strpos($userAgent, 'firefox') !== false) {
            return 'Firefox';
        }

        if (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'chrome') === false) {
            return 'Safari';
        }

        if (strpos($userAgent, 'edg') !== false) {
            return 'Edge';
        }

        if (strpos($userAgent, 'opera') !== false || strpos($userAgent, 'opr') !== false) {
            return 'Opera';
        }

        return 'Other';
    }

    private function getCountryFromIp(string $ipAddress): ?string
    {
        if (! $ipAddress || $ipAddress === '127.0.0.1' || $ipAddress === '::1') {
            return 'Local';
        }

        if (filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'Private/Reserved';
        }

        return 'Unknown';
    }
}