<script setup lang="ts">
import StarWarsLayout from '@/Layouts/StarWarsLayout.vue';
import type { Character, Film } from '@/types/starwars';
import {
    formatAttribute,
    formatHeight,
    formatMass,
    generateInitials,
    getGenderColor,
} from '@/utils/starwars';
import { Head, Link } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';

const props = defineProps<{
    characterId: string;
    character?: Character;
}>();

const character = ref<Character | null>(props.character || null);
const films = ref<Film[]>([]);
const loading = ref(!!props.character ? false : true);
const error = ref<string | null>(null);

const formattedHeight = computed(() => {
    if (!character.value) return 'Unknown';
    return formatHeight(character.value.properties.height);
});

const formattedMass = computed(() => {
    if (!character.value) return 'Unknown';
    return formatMass(character.value.properties.mass);
});

const genderColor = computed(() => {
    if (!character.value) return 'bg-gray-100 text-gray-800';
    return getGenderColor(character.value.properties.gender);
});

const initials = computed(() => {
    if (!character.value) return '??';
    return generateInitials(character.value.properties.name);
});

const fetchCharacterDetails = async () => {
    if (props.character) {
        character.value = props.character;
        await fetchFilms();
        return;
    }

    try {
        loading.value = true;
        error.value = null;

        const response = await fetch(`/api/people/${props.characterId}`);

        const data = await response.json();

        if (data.success) {
            character.value = data.data;
            await fetchFilms();
        } else {
            error.value = data.error || 'Failed to load character details';
        }
    } catch (err) {
        error.value = 'Failed to connect to the server';
        console.error('Character fetch error:', err);
    } finally {
        loading.value = false;
    }
};

const fetchFilms = async () => {
    // The reason this is mocked is that the SWAPI API is not returning any films as of 08/12/2025
    const mockedFilms = [
        'https://swapi.dev/api/films/1/',
        'https://swapi.dev/api/films/2/',
        'https://swapi.dev/api/films/3/',
    ];

    // if (!character.value?.properties.films?.length) {
    //     return;
    // }

    try {
        const filmPromises = mockedFilms.map(async (filmUrl) => {
            const filmId = filmUrl.split('/').filter(Boolean).pop();
            const response = await fetch(`/api/films/${filmId}`);
            const data = await response.json();
            return data.success ? data.data : null;
        });

        const filmResults = await Promise.all(filmPromises);
        films.value = filmResults.filter((film) => film !== null);
    } catch (err) {
        console.error('Films fetch error:', err);
    }
};

onMounted(() => {
    fetchCharacterDetails();
});
</script>

<template>
    <Head :title="character?.properties.name || 'Character Details'" />

    <StarWarsLayout
        :show-back-button="true"
        max-width="4xl"
        :loading="loading"
        :error="error"
    >
        <template #content>
            <!-- Character Details -->
            <div v-if="character" class="space-y-8">
                <!-- Header Card -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div
                        class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-8 text-white"
                    >
                        <div class="flex items-center gap-6">
                            <div
                                class="flex h-20 w-20 items-center justify-center rounded-full bg-white/20 text-2xl font-bold"
                            >
                                {{ initials }}
                            </div>
                            <div class="flex-1">
                                <h1 class="text-3xl font-bold leading-tight">
                                    {{ character.properties.name || 'Unknown' }}
                                </h1>
                                <div class="mt-2 flex items-center gap-3">
                                    <span
                                        :class="genderColor"
                                        class="inline-flex items-center rounded px-3 py-1 text-sm font-medium"
                                    >
                                        {{
                                            formatAttribute(
                                                character.properties.gender,
                                            )
                                        }}
                                    </span>
                                    <span
                                        v-if="
                                            character.properties.birth_year &&
                                            formatAttribute(
                                                character.properties.birth_year,
                                            ) !== 'Unknown'
                                        "
                                        class="text-lg text-green-100"
                                    >
                                        Born in
                                        {{
                                            formatAttribute(
                                                character.properties.birth_year,
                                            )
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Details Grid -->
                    <div class="px-6 py-6">
                        <h2 class="mb-4 text-lg font-semibold text-gray-900">
                            Details
                        </h2>

                        <!-- Physical Attributes -->
                        <div
                            class="mb-6 grid gap-6 md:grid-cols-2 lg:grid-cols-4"
                        >
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 h-5 w-5 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 21l3-3 3 3M7 3l3 3 3-3m0 6v12M7 9v12"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Height
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-gray-900"
                                    >
                                        {{ formattedHeight }}
                                    </dd>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 h-5 w-5 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Mass
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-gray-900"
                                    >
                                        {{ formattedMass }}
                                    </dd>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 h-5 w-5 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Eye Color
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-gray-900"
                                    >
                                        {{
                                            formatAttribute(
                                                character.properties.eye_color,
                                            )
                                        }}
                                    </dd>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 h-5 w-5 text-gray-400">
                                    <svg
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <dt
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Hair Color
                                    </dt>
                                    <dd
                                        class="mt-1 text-sm font-semibold text-gray-900"
                                    >
                                        {{
                                            formatAttribute(
                                                character.properties.hair_color,
                                            )
                                        }}
                                    </dd>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="border-t pt-6">
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="flex items-start gap-3">
                                    <div class="mt-0.5 h-5 w-5 text-gray-400">
                                        <svg
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4 4 4 0 004-4V5z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <dt
                                            class="text-sm font-medium text-gray-500"
                                        >
                                            Skin Color
                                        </dt>
                                        <dd
                                            class="mt-1 text-sm font-semibold text-gray-900"
                                        >
                                            {{
                                                formatAttribute(
                                                    character.properties
                                                        .skin_color,
                                                )
                                            }}
                                        </dd>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Movies Section -->
                <div
                    v-if="films.length"
                    class="overflow-hidden rounded-lg bg-white shadow"
                >
                    <div class="px-6 py-6">
                        <h2 class="mb-6 text-xl font-bold text-gray-900">
                            Movies
                        </h2>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <Link
                                v-for="film in films"
                                :key="film.uid"
                                :href="`/films/${film.uid}`"
                                class="group block rounded-lg border border-gray-200 p-4 transition-all duration-200 hover:border-blue-300 hover:shadow-md"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-purple-600 text-sm font-bold text-white"
                                    >
                                        {{ film.properties.episode_id || '?' }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h3
                                            class="truncate font-medium text-gray-900 group-hover:text-blue-600"
                                        >
                                            {{
                                                film.properties.title ||
                                                'Unknown'
                                            }}
                                        </h3>
                                        <div class="text-sm text-gray-500">
                                            <span
                                                v-if="
                                                    film.properties.release_date
                                                "
                                            >
                                                {{
                                                    new Date(
                                                        film.properties.release_date,
                                                    ).getFullYear()
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <svg
                                            class="h-5 w-5 text-gray-400 group-hover:text-blue-600"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </StarWarsLayout>
</template>
