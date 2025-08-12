<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { formatEpisodeRoman, formatReleaseYear, truncateText } from '@/utils/starwars';
import type { Film } from '@/types/starwars';

interface Props {
    film: Film;
}

const props = defineProps<Props>();

const episodeRoman = computed(() => {
    return formatEpisodeRoman(props.film.properties.episode_id);
});

const formattedDate = computed(() => {
    return formatReleaseYear(props.film.properties.release_date);
});

const truncatedCrawl = computed(() => {
    return truncateText(props.film.properties.opening_crawl, 150);
});

const producers = computed(() => {
    const producer = props.film.properties.producer;
    if (!producer) return [];
    return producer
        .split(',')
        .map((p) => p.trim())
        .slice(0, 2);
});
</script>

<template>
    <Link
        :href="`/films/${film.uid}`"
        class="block overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-md hover:border-blue-300 cursor-pointer"
    >
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-4 text-white"
        >
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-bold leading-tight">
                        {{ film.properties.title }}
                    </h3>
                    <div
                        class="mt-1 flex items-center gap-2 text-sm text-blue-100"
                    >
                        <span v-if="episodeRoman"
                            >Episode {{ episodeRoman }}</span
                        >
                        <span v-if="episodeRoman && formattedDate">•</span>
                        <span v-if="formattedDate">{{ formattedDate }}</span>
                    </div>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                d="M2 6a2 2 0 012-2h6l2 2h6a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM5 8a1 1 0 000 2h8a1 1 0 100-2H5z"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-4 p-6">
            <!-- Director -->
            <div class="flex items-start gap-3">
                <div class="mt-0.5 h-5 w-5 text-gray-400">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Director</dt>
                    <dd class="text-sm text-gray-900">
                        {{ film.properties.director || 'Unknown' }}
                    </dd>
                </div>
            </div>

            <!-- Producers -->
            <div v-if="producers.length" class="flex items-start gap-3">
                <div class="mt-0.5 h-5 w-5 text-gray-400">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                        />
                    </svg>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Producer{{ producers.length > 1 ? 's' : '' }}
                    </dt>
                    <dd class="text-sm text-gray-900">
                        {{ producers.join(', ') }}
                    </dd>
                </div>
            </div>

            <!-- Opening Crawl -->
            <div v-if="truncatedCrawl" class="border-t pt-4">
                <h4 class="mb-2 text-sm font-medium text-gray-900">
                    Opening Crawl
                </h4>
                <p class="text-sm italic leading-relaxed text-gray-600">
                    {{ truncatedCrawl }}
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3">
            <div
                class="flex items-center justify-between text-xs text-gray-500"
            >
                <span>Episode {{ film.properties.episode_id || '?' }}</span>
                <span>{{ formattedDate }}</span>
            </div>
        </div>
    </Link>
</template>
