<script setup lang="ts">
import Statistics from '@/Components/StarWars/Statistics.vue';
import StarWarsLayout from '@/Layouts/StarWarsLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { StatisticsData } from '@/types/starwars';

const props = defineProps<{
    statistics: StatisticsData | null;
    error?: string | null;
}>();
</script>

<template>
    <Head title="Star Wars Statistics" />

    <StarWarsLayout :show-back-button="true">
        <template #content>
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="mb-4 text-4xl font-bold text-gray-900">
                    Star Wars Statistics
                </h1>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    Discover interesting statistics and analytics from the Star Wars universe,
                    including API usage metrics and data insights.
                </p>
            </div>

            <!-- Statistics Section -->
            <Statistics 
                :statistics="statistics" 
                :error="error"
                :loading="false"
            />
        </template>
    </StarWarsLayout>
</template>