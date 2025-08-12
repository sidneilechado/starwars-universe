<script setup lang="ts">
import type { Character } from '@/types/starwars';
import {
    formatAttribute,
    formatHeight,
    formatMass,
    generateInitials,
    getGenderColor,
} from '@/utils/starwars';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Props {
    person: Character;
}

const props = defineProps<Props>();

const formattedHeight = computed(() => {
    return formatHeight(props.person.properties.height);
});

const formattedMass = computed(() => {
    return formatMass(props.person.properties.mass);
});

const genderColor = computed(() => {
    return getGenderColor(props.person.properties.gender);
});

const initials = computed(() => {
    return generateInitials(props.person.properties.name);
});
</script>

<template>
    <Link
        :href="`/people/${person.uid}`"
        class="block cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:border-green-300 hover:shadow-md"
    >
        <!-- Header -->
        <div
            class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4 text-white"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 font-bold"
                >
                    {{ initials }}
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold leading-tight">
                        {{ person.properties.name || 'Unknown' }}
                    </h3>
                    <div class="mt-1 flex items-center gap-2">
                        <span
                            :class="genderColor"
                            class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium"
                        >
                            {{ formatAttribute(person.properties.gender) }}
                        </span>
                        <span
                            v-if="person.properties.birth_year"
                            class="text-sm text-green-100"
                        >
                            {{ formatAttribute(person.properties.birth_year) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="space-y-4 p-6">
            <!-- Physical Attributes -->
            <div class="grid grid-cols-2 gap-4">
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
                        <dt class="text-sm font-medium text-gray-500">
                            Height
                        </dt>
                        <dd class="text-sm text-gray-900">
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
                        <dt class="text-sm font-medium text-gray-500">Mass</dt>
                        <dd class="text-sm text-gray-900">
                            {{ formattedMass }}
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Appearance -->
            <div class="border-t pt-4">
                <h4 class="mb-3 text-sm font-medium text-gray-900">
                    Appearance
                </h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Hair Color:</span>
                        <span class="text-sm text-gray-900">{{
                            formatAttribute(person.properties.hair_color)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Eye Color:</span>
                        <span class="text-sm text-gray-900">{{
                            formatAttribute(person.properties.eye_color)
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Skin Color:</span>
                        <span class="text-sm text-gray-900">{{
                            formatAttribute(person.properties.skin_color)
                        }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-3">
            <div
                class="flex items-center justify-between text-xs text-gray-500"
            >
                <span>{{ formatAttribute(person.properties.gender) }}</span>
                <span v-if="person.properties.birth_year"
                    >Born in
                    {{ formatAttribute(person.properties.birth_year) }}</span
                >
            </div>
        </div>
    </Link>
</template>
