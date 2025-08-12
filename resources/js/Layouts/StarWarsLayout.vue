<script setup lang="ts">
import LoadingSpinner from '@/Components/StarWars/LoadingSpinner.vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    showBackButton?: boolean;
    maxWidth?: '4xl' | '7xl';
    loading?: boolean;
    error?: string | null;
}

const props = withDefaults(defineProps<Props>(), {
    showBackButton: false,
    maxWidth: '7xl',
    loading: false,
    error: null,
});

const maxWidthClass = props.maxWidth === '4xl' ? 'max-w-4xl' : 'max-w-7xl';
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="border-b border-gray-100 bg-white shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center">
                            <span class="ml-2 text-xl font-semibold text-gray-900">
                                Star Wars Universe
                            </span>
                        </Link>
                    </div>
                    
                    <!-- Navigation Menu -->
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <Link
                                href="/"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-gray-900"
                            >
                                Search
                            </Link>
                            <Link
                                href="/statistics"
                                class="rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            >
                                Statistics
                            </Link>
                        </div>
                    </div>
                    <div v-if="showBackButton">
                        <Link 
                            href="/" 
                            class="inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500"
                        >
                            <svg 
                                class="mr-2 h-4 w-4" 
                                fill="none" 
                                viewBox="0 0 24 24" 
                                stroke="currentColor"
                            >
                                <path 
                                    stroke-linecap="round" 
                                    stroke-linejoin="round" 
                                    stroke-width="2" 
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" 
                                />
                            </svg>
                            Back to Search
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="py-12">
            <div class="mx-auto px-4 sm:px-6 lg:px-8" :class="maxWidthClass">
                <!-- Loading State -->
                <LoadingSpinner v-if="loading" />

                <!-- Error State -->
                <div v-else-if="error" class="py-12 text-center">
                    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
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
                    <h3 class="mb-2 text-lg font-medium text-gray-900">Error</h3>
                    <p class="text-gray-500">{{ error }}</p>
                </div>

                <!-- Content Slot -->
                <div v-else>
                    <slot name="content" />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="mt-16 border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    <p>Built with Laravel, Vue.js, and the Star Wars API (SWAPI)</p>
                    <p class="mt-2">May the Force be with you!</p>
                </div>
            </div>
        </footer>
    </div>
</template>