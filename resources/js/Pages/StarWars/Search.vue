<script setup lang="ts">
import FilmCard from '@/Components/StarWars/FilmCard.vue';
import LoadingSpinner from '@/Components/StarWars/LoadingSpinner.vue';
import PersonCard from '@/Components/StarWars/PersonCard.vue';
import SearchForm from '@/Components/StarWars/SearchForm.vue';
import StarWarsLayout from '@/Layouts/StarWarsLayout.vue';
import type { Character, Film } from '@/types/starwars';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const activeTab = ref<'films' | 'people'>('films');
const films = ref<Film[]>([]);
const people = ref<Character[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const hasSearched = ref(false);

const results = computed(() => {
    return activeTab.value === 'films' ? films.value : people.value;
});

const handleSearch = async (query: string) => {
    if (!query.trim()) {
        films.value = [];
        people.value = [];
        hasSearched.value = false;
        return;
    }

    loading.value = true;
    error.value = null;
    hasSearched.value = true;

    try {
        const queryParams = new URLSearchParams({
            query: query.trim(),
            type: activeTab.value,
        });

        const response = await fetch(`/api/search?${queryParams}`);

        const data = await response.json();
        console.log(data);

        if (data.success) {
            if (activeTab.value === 'films') {
                films.value = data.data;
                people.value = [];
            } else {
                people.value = data.data;
                films.value = [];
            }
        } else {
            error.value = data.error || 'An error occurred while searching';
        }
    } catch (err) {
        error.value = 'Failed to connect to the server';
        console.error('Search error:', err);
    } finally {
        loading.value = false;
    }
};

const handleTabChange = (tab: 'films' | 'people') => {
    activeTab.value = tab;
    films.value = [];
    people.value = [];
    hasSearched.value = false;
    error.value = null;
};
</script>

<template>
    <Head title="Star Wars Search" />

    <StarWarsLayout>
        <template #content>
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="mb-4 text-4xl font-bold text-gray-900">
                    Explore the Star Wars Universe
                </h1>
                <p class="mx-auto max-w-2xl text-lg text-gray-600">
                    Search through the vast galaxy of Star Wars films and
                    characters. Discover details about your favorite movies and
                    heroes from a galaxy far, far away.
                </p>
            </div>

            <!-- Search Section -->
            <div class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Tab Navigation -->
                    <div class="mb-6 flex space-x-1 rounded-lg bg-gray-100 p-1">
                        <button
                            @click="handleTabChange('films')"
                            :class="{
                                'bg-white shadow': activeTab === 'films',
                                'text-gray-600 hover:text-gray-800':
                                    activeTab !== 'films',
                            }"
                            class="flex-1 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                        >
                            Films
                        </button>
                        <button
                            @click="handleTabChange('people')"
                            :class="{
                                'bg-white shadow': activeTab === 'people',
                                'text-gray-600 hover:text-gray-800':
                                    activeTab !== 'people',
                            }"
                            class="flex-1 rounded-md px-3 py-2 text-sm font-medium transition-colors"
                        >
                            People
                        </button>
                    </div>

                    <!-- Search Form -->
                    <SearchForm
                        :active-tab="activeTab"
                        :loading="loading"
                        @search="handleSearch"
                    />
                </div>
            </div>

            <!-- Results Section -->
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Loading State -->
                    <LoadingSpinner v-if="loading" />

                    <!-- Error State -->
                    <div v-else-if="error" class="py-12 text-center">
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100"
                        >
                            <svg
                                class="h-6 w-6 text-red-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            Search Error
                        </h3>
                        <p class="text-gray-500">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-else-if="hasSearched && results.length === 0"
                        class="py-12 text-center"
                    >
                        <div
                            class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100"
                        >
                            <svg
                                class="h-6 w-6 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            No Results Found
                        </h3>
                        <p class="text-gray-500">
                            Try searching with different keywords.
                        </p>
                    </div>

                    <!-- Default State -->
                    <div v-else-if="!hasSearched" class="py-12 text-center">
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100"
                        >
                            <svg
                                class="h-8 w-8 text-blue-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-medium text-gray-900">
                            Search the Galaxy
                        </h3>
                        <p class="mb-6 text-gray-500">
                            Use the search above to discover
                            {{ activeTab === 'films' ? 'films' : 'characters' }}
                            from the Star Wars universe.
                        </p>
                    </div>

                    <!-- Results Grid -->
                    <div
                        v-else
                        class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        <template v-if="activeTab === 'films'">
                            <FilmCard
                                v-for="film in films"
                                :key="film.uid"
                                :film="film"
                            />
                        </template>
                        <template v-else>
                            <PersonCard
                                v-for="person in people"
                                :key="person.uid"
                                :person="person"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </StarWarsLayout>
</template>
