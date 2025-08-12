<script setup lang="ts">
import CharactersList from '@/Components/StarWars/CharactersList.vue';
import StarWarsLayout from '@/Layouts/StarWarsLayout.vue';
import type { Character, Film } from '@/types/starwars';
import {
    formatEpisodeRoman,
    formatProducers,
    formatReleaseYear,
} from '@/utils/starwars';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    filmId: number;
    film?: Film;
}>();

const film = ref<Film | null>(props.film || null);
const characters = ref<Character[]>([]);
const loading = ref(!!props.film ? false : true);
const charactersLoading = ref(false);
const error = ref<string | null>(null);

const episodeRoman = computed(() => {
    if (!film.value) return '';
    return formatEpisodeRoman(film.value.properties.episode_id);
});

const formattedDate = computed(() => {
    if (!film.value?.properties.release_date) return '';
    return formatReleaseYear(film.value.properties.release_date);
});

const producers = computed(() => {
    if (!film.value?.properties.producer) return [];
    return formatProducers(film.value.properties.producer);
});

const fetchFilmDetails = async () => {
    if (props.film) {
        film.value = props.film;
        await fetchCharacters();
        return;
    }

    try {
        loading.value = true;
        error.value = null;

        const response = await fetch(`/api/films/${props.filmId}`);

        const data = await response.json();
        console.log('Film fetch response:', data);

        if (data.success) {
            film.value = data.data;
            await fetchCharacters();
        } else {
            error.value = data.error || 'Failed to load film details';
        }
    } catch (err) {
        error.value = 'Failed to connect to the server';
        console.error('Film fetch error:', err);
    } finally {
        loading.value = false;
    }
};

const fetchCharacters = async () => {
    if (!film.value?.properties.characters?.length) {
        return;
    }

    try {
        charactersLoading.value = true;
        const characterPromises = film.value.properties.characters.map(
            async (characterUrl) => {
                const characterId = characterUrl.split('/').pop();
                const response = await fetch(`/api/people/${characterId}`);
                const data = await response.json();
                return data.success ? data.data : null;
            },
        );

        const characterResults = await Promise.all(characterPromises);
        characters.value = characterResults.filter((char) => char !== null);
    } catch (err) {
        console.error('Characters fetch error:', err);
    } finally {
        charactersLoading.value = false;
    }
};

onMounted(() => {
    fetchFilmDetails();
});
</script>

<template>
    <Head :title="film?.properties.title || 'Film Details'" />

    <StarWarsLayout
        :show-back-button="true"
        max-width="4xl"
        :loading="loading"
        :error="error"
    >
        <template #content>
            <!-- Film Details -->
            <div v-if="film" class="space-y-8">
                <!-- Header Card -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div
                        class="bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-8 text-white"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold leading-tight">
                                    {{ film.properties.title }}
                                </h1>
                                <div
                                    class="mt-2 flex items-center gap-3 text-lg text-blue-100"
                                >
                                    <span v-if="episodeRoman"
                                        >Episode {{ episodeRoman }}</span
                                    >
                                    <span v-if="episodeRoman && formattedDate"
                                        >•</span
                                    >
                                    <span v-if="formattedDate">{{
                                        formattedDate
                                    }}</span>
                                </div>
                            </div>
                            <div class="ml-6 flex-shrink-0">
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20"
                                >
                                    <span class="text-2xl font-bold">{{
                                        episodeRoman
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-6">
                        <!-- Director & Producer Info -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 h-6 w-6 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Director
                                    </dt>
                                    <dd class="mt-1 text-lg text-gray-900">
                                        {{
                                            film.properties.director ||
                                            'Unknown'
                                        }}
                                    </dd>
                                </div>
                            </div>

                            <div
                                v-if="producers.length"
                                class="flex items-start gap-3"
                            >
                                <div class="mt-0.5 h-6 w-6 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Producer{{
                                            producers.length > 1 ? 's' : ''
                                        }}
                                    </dt>
                                    <dd class="mt-1 text-lg text-gray-900">
                                        {{ producers.join(', ') }}
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opening Crawl -->
                <div
                    v-if="film.properties.opening_crawl"
                    class="overflow-hidden rounded-lg bg-white shadow"
                >
                    <div class="px-6 py-6">
                        <h2 class="mb-4 text-xl font-bold text-gray-900">
                            Opening Crawl
                        </h2>
                        <div class="rounded-lg bg-gray-50 p-6">
                            <p
                                class="text-sm italic leading-relaxed text-gray-700"
                                style="white-space: pre-line"
                            >
                                {{ film.properties.opening_crawl }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Characters Section -->
                <CharactersList
                    v-if="characters.length || charactersLoading"
                    :characters="characters"
                    :loading="charactersLoading"
                />
            </div>
        </template>
    </StarWarsLayout>
</template>
