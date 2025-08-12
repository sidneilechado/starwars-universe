<script setup lang="ts">
import type { StatisticsData } from '@/types/starwars';
import { computed } from 'vue';

/**
 * Props interface for Statistics components
 */
interface Props {
    statistics: StatisticsData | null;
    loading?: boolean;
    error?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    loading: false,
    error: null,
});

const formatTime = (ms: number): string => {
    if (ms < 1000) {
        return `${ms}ms`;
    }
    return `${(ms / 1000).toFixed(1)}s`;
};

const formatHour = (hour: number): string => {
    if (hour === 0) return '12 AM';
    if (hour < 12) return `${hour} AM`;
    if (hour === 12) return '12 PM';
    return `${hour - 12} PM`;
};

const lastUpdated = computed(() => {
    if (!props.statistics?.computed_at) return 'Never';

    const date = new Date(props.statistics.computed_at);
    return date.toLocaleString();
});

const peakHour = computed(() => {
    if (!props.statistics?.hourly_distribution) return null;

    const peak = props.statistics.hourly_distribution.reduce((max, current) =>
        current.count > max.count ? current : max,
    );

    return peak.count > 0 ? peak : null;
});
</script>

<template>
    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                Search Statistics
            </h2>
        </div>

        <!-- Loading State -->
        <div v-if="props.loading" class="flex items-center justify-center py-8">
            <div
                class="h-8 w-8 animate-spin rounded-full border-b-2 border-blue-600"
            ></div>
            <span class="ml-2 text-gray-600">Loading statistics...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="props.error" class="py-8 text-center">
            <div class="mb-2 text-red-600">{{ props.error }}</div>
        </div>

        <!-- Statistics Content -->
        <div v-else-if="props.statistics" class="space-y-6">
            <!-- Overview Stats -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-blue-50 p-4">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ props.statistics.total_searches.toLocaleString() }}
                    </div>
                    <div class="text-sm text-blue-800">Total Searches</div>
                </div>

                <div class="rounded-lg bg-green-50 p-4">
                    <div class="text-2xl font-bold text-green-600">
                        {{
                            formatTime(
                                props.statistics.average_response_time_ms,
                            )
                        }}
                    </div>
                    <div class="text-sm text-green-800">Avg Response Time</div>
                </div>

                <div class="rounded-lg bg-yellow-50 p-4">
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ props.statistics.search_quality.effectiveness_ratio }}%
                    </div>
                    <div class="text-sm text-yellow-800">Search Success Rate</div>
                </div>

                <div v-if="peakHour" class="rounded-lg bg-purple-50 p-4">
                    <div class="text-2xl font-bold text-purple-600">
                        {{ formatHour(peakHour.hour) }}
                    </div>
                    <div class="text-sm text-purple-800">Peak Search Hour</div>
                </div>
            </div>

            <!-- Top Queries -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    Top Search Queries
                </h3>
                <div
                    v-if="props.statistics.top_queries.length > 0"
                    class="space-y-3"
                >
                    <div
                        v-for="(query, index) in props.statistics.top_queries"
                        :key="`${query.query}-${query.type}`"
                        class="flex items-center justify-between rounded-lg bg-gray-50 p-3"
                    >
                        <div class="flex items-center">
                            <span
                                class="mr-3 flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white"
                            >
                                {{ index + 1 }}
                            </span>
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ query.query }}
                                </div>
                                <div class="text-sm capitalize text-gray-500">
                                    {{ query.type }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold text-gray-900">
                                {{ query.percentage }}%
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ query.count }} searches
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="py-4 text-center text-gray-500">
                    No search data available yet
                </div>
            </div>

            <!-- Performance Metrics -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    Performance Metrics
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Response Time Percentiles -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Response Time Percentiles</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">P50 (Median)</span>
                                <span class="font-medium">{{ formatTime(props.statistics.response_time_percentiles.p50) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">P90</span>
                                <span class="font-medium">{{ formatTime(props.statistics.response_time_percentiles.p90) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">P95</span>
                                <span class="font-medium">{{ formatTime(props.statistics.response_time_percentiles.p95) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">P99</span>
                                <span class="font-medium">{{ formatTime(props.statistics.response_time_percentiles.p99) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Slow Queries -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Slow Queries</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Count</span>
                                <span class="font-medium">{{ props.statistics.slow_queries.count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Percentage</span>
                                <span class="font-medium">{{ props.statistics.slow_queries.percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Threshold</span>
                                <span class="font-medium">{{ formatTime(props.statistics.slow_queries.threshold_ms) }}</span>
                            </div>
                            <div v-if="props.statistics.slow_queries.slowest_query" class="pt-2 border-t border-gray-100">
                                <div class="text-xs text-gray-500 mb-1">Slowest Query:</div>
                                <div class="text-xs font-mono bg-gray-50 p-2 rounded">
                                    "{{ props.statistics.slow_queries.slowest_query.query }}" 
                                    ({{ props.statistics.slow_queries.slowest_query.type }}) - 
                                    {{ formatTime(props.statistics.slow_queries.slowest_query.response_time_ms) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Quality -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    Search Quality
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Quality Metrics -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Quality Metrics</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Zero Results</span>
                                <span class="font-medium">{{ props.statistics.search_quality.zero_result_searches.count }} ({{ props.statistics.search_quality.zero_result_searches.percentage }}%)</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Success Rate</span>
                                <span class="font-medium text-green-600">{{ props.statistics.search_quality.effectiveness_ratio }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Avg Results</span>
                                <span class="font-medium">{{ props.statistics.search_quality.average_results_per_search }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Results Distribution -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Results Distribution</h4>
                        <div class="space-y-2">
                            <div
                                v-for="dist in props.statistics.search_quality.results_distribution"
                                :key="dist.range"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600">{{ dist.range }} results</span>
                                <span class="font-medium">{{ dist.count }} ({{ dist.percentage }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Behavior -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    User Behavior
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <!-- Device Types -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Device Types</h4>
                        <div class="space-y-2">
                            <div
                                v-for="device in props.statistics.user_behavior.device_types.slice(0, 5)"
                                :key="device.device"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600">{{ device.device }}</span>
                                <span class="font-medium">{{ device.percentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Browsers -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Browsers</h4>
                        <div class="space-y-2">
                            <div
                                v-for="browser in props.statistics.user_behavior.browsers.slice(0, 5)"
                                :key="browser.browser"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600">{{ browser.browser }}</span>
                                <span class="font-medium">{{ browser.percentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search Types -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Search Types</h4>
                        <div class="space-y-2">
                            <div
                                v-for="searchType in props.statistics.user_behavior.search_type_distribution"
                                :key="searchType.type"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600 capitalize">{{ searchType.type }}</span>
                                <span class="font-medium">{{ searchType.percentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Analysis -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    Content Analysis
                </h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Query Length Distribution -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Query Length Distribution</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-600">Average Length</span>
                                <span class="font-medium">{{ props.statistics.content_analysis.average_query_length }} chars</span>
                            </div>
                            <div
                                v-for="length in props.statistics.content_analysis.query_length_distribution"
                                :key="length.range"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600">{{ length.range }} chars</span>
                                <span class="font-medium">{{ length.percentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Most Common Words -->
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h4 class="mb-3 font-medium text-gray-900">Most Common Words</h4>
                        <div class="space-y-2">
                            <div
                                v-for="word in props.statistics.content_analysis.most_common_words.slice(0, 8)"
                                :key="word.word"
                                class="flex justify-between"
                            >
                                <span class="text-sm text-gray-600 font-mono">{{ word.word }}</span>
                                <span class="font-medium">{{ word.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hourly Distribution -->
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900">
                    Search Activity by Hour
                </h3>
                <div class="grid grid-cols-12 gap-1">
                    <div
                        v-for="hour in props.statistics.hourly_distribution"
                        :key="hour.hour"
                        class="text-center"
                    >
                        <div
                            class="mb-1 rounded-sm bg-blue-200 transition-all duration-200 hover:bg-blue-300"
                            :style="{
                                height: `${Math.max(4, (hour.percentage / Math.max(...props.statistics.hourly_distribution.map((h) => h.percentage))) * 60)}px`,
                            }"
                            :title="`${formatHour(hour.hour)}: ${hour.count} searches (${hour.percentage}%)`"
                        ></div>
                        <div class="text-xs text-gray-500">{{ hour.hour }}</div>
                    </div>
                </div>
                <div class="mt-2 flex justify-between text-xs text-gray-500">
                    <span>12 AM</span>
                    <span>12 PM</span>
                    <span>11 PM</span>
                </div>
            </div>

            <!-- Last Updated -->
            <div
                class="border-t border-gray-200 pt-4 text-center text-sm text-gray-500"
            >
                Last updated: {{ lastUpdated }}
                <div class="mt-1 text-xs">
                    Statistics are computed every 5 minutes automatically
                </div>
            </div>
        </div>

        <!-- No Data State -->
        <div v-else class="py-8 text-center">
            <div class="mb-2 text-gray-500">No statistics available</div>
            <div class="text-sm text-gray-400">
                Perform some searches to generate statistics
            </div>
        </div>
    </div>
</template>
