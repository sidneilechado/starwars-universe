<template>
    <Link
        :href="`/people/${character?.uid || ''}`"
        class="group block rounded-lg border border-gray-200 p-4 transition-all duration-200 hover:border-blue-300 hover:shadow-md"
    >
        <div class="flex items-center gap-3">
            <!-- Avatar -->
            <div
                class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-green-600 to-teal-600 text-sm font-bold text-white"
            >
                {{ generateInitials(character?.properties?.name) }}
            </div>

            <div class="min-w-0 flex-1">
                <!-- Name -->
                <h3
                    class="truncate font-medium text-gray-900 group-hover:text-blue-600"
                >
                    {{ character?.properties?.name || 'Unknown' }}
                </h3>

                <!-- Details -->
                <div class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                    <span
                        v-if="
                            character?.properties?.birth_year &&
                            character.properties.birth_year !== 'unknown'
                        "
                    >
                        {{ character.properties.birth_year }}
                    </span>
                    <span
                        v-if="
                            character?.properties?.birth_year &&
                            character.properties.birth_year !== 'unknown' &&
                            character?.properties?.gender &&
                            character.properties.gender !== 'n/a'
                        "
                        >•</span
                    >
                    <span
                        v-if="
                            character?.properties?.gender &&
                            character.properties.gender !== 'n/a'
                        "
                        class="capitalize"
                    >
                        {{ character.properties.gender }}
                    </span>
                </div>
            </div>

            <!-- Arrow icon -->
            <div class="flex-shrink-0">
                <svg
                    class="h-5 w-5 text-gray-400 transition-colors group-hover:text-blue-600"
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
</template>

<script setup lang="ts">
import type { Character } from '@/types/starwars';
import { generateInitials } from '@/utils/starwars';
import { Link } from '@inertiajs/vue3';

interface Props {
    character: Character;
}

defineProps<Props>();
</script>
