<script setup lang="ts">
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';

interface Props {
    activeTab: 'films' | 'people';
    loading: boolean;
}

interface Emits {
    (e: 'search', query: string): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const searchQuery = ref('');
const searchTimeout = ref<number | null>(null);

const placeholders = {
    films: 'Search for Star Wars films...',
    people: 'Search for Star Wars characters...',
};

const examples = {
    films: ['A New Hope', 'Empire Strikes Back', 'Return of the Jedi'],
    people: ['Luke Skywalker', 'Darth Vader', 'Leia Organa'],
};

const handleInput = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }

    searchTimeout.value = setTimeout(() => {
        emit('search', searchQuery.value);
    }, 300);
};

const handleSubmit = (e: Event) => {
    e.preventDefault();
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value);
    }
    emit('search', searchQuery.value);
};

const handleExampleClick = (example: string) => {
    searchQuery.value = example;
    emit('search', example);
};

const clearSearch = () => {
    searchQuery.value = '';
    emit('search', '');
};

// Clear search when tab changes
watch(
    () => props.activeTab,
    () => {
        clearSearch();
    },
);
</script>

<template>
    <div class="space-y-4">
        <!-- Search Form -->
        <form @submit="handleSubmit" class="flex gap-2">
            <div class="relative flex-1">
                <TextInput
                    v-model="searchQuery"
                    @input="handleInput"
                    :placeholder="placeholders[activeTab]"
                    :disabled="loading"
                    class="w-full pl-10 pr-10"
                />

                <!-- Search Icon -->
                <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                >
                    <svg
                        class="h-5 w-5 text-gray-400"
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

                <!-- Clear Button -->
                <button
                    v-if="searchQuery && !loading"
                    @click="clearSearch"
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <!-- Loading Spinner -->
                <div
                    v-else-if="loading"
                    class="absolute inset-y-0 right-0 flex items-center pr-3"
                >
                    <svg
                        class="h-5 w-5 animate-spin text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                </div>
            </div>

            <PrimaryButton
                type="submit"
                :disabled="loading || !searchQuery.trim()"
                class="px-6"
            >
                Search
            </PrimaryButton>
        </form>

        <!-- Example Searches -->
        <div class="text-sm">
            <span class="font-medium text-gray-500">Try searching for:</span>
            <div class="mt-2 flex flex-wrap gap-2">
                <button
                    v-for="example in examples[activeTab]"
                    :key="example"
                    @click="handleExampleClick(example)"
                    :disabled="loading"
                    class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    {{ example }}
                </button>
            </div>
        </div>
    </div>
</template>
