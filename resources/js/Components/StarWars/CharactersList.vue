<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="px-6 py-6">
            <h2 class="mb-6 text-xl font-bold text-gray-900">Characters</h2>

            <!-- Loading state -->
            <LoadingSpinner
                v-if="loading"
                size="md"
                text="Loading characters from a galaxy far, far away..."
            />

            <!-- Loaded characters -->
            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <CharacterCard
                    v-for="character in characters"
                    :key="character.uid"
                    :character="character"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import type { Character } from '@/types/starwars';
import CharacterCard from './CharacterCard.vue';
import LoadingSpinner from './LoadingSpinner.vue';

interface Props {
    characters?: Character[];
    loading?: boolean;
}

withDefaults(defineProps<Props>(), {
    characters: () => [],
    loading: false,
});
</script>
