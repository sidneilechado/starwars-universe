<script setup lang="ts">
interface Props {
    size?: 'sm' | 'md' | 'lg';
    text?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 'md',
    text: 'Searching the galaxy...',
});

const sizeClasses = {
    sm: 'w-8 h-8',
    md: 'w-12 h-12',
    lg: 'w-16 h-16',
};

const { size, text } = props;
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12">
        <!-- Spinner -->
        <div class="relative">
            <!-- Outer ring -->
            <div
                :class="sizeClasses[size]"
                class="animate-pulse rounded-full border-4 border-gray-200"
            ></div>
            <!-- Inner spinning ring -->
            <div
                :class="sizeClasses[size]"
                class="absolute inset-0 animate-spin rounded-full border-4 border-transparent border-r-blue-600 border-t-blue-600"
            ></div>
            <!-- Center dot -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div
                    class="h-2 w-2 animate-pulse rounded-full bg-blue-600"
                ></div>
            </div>
        </div>

        <!-- Loading text -->
        <div class="mt-4 text-center">
            <p class="text-sm font-medium text-gray-600">{{ text }}</p>
            <div class="mt-2 flex justify-center space-x-1">
                <div
                    class="h-2 w-2 animate-bounce rounded-full bg-blue-600"
                    style="animation-delay: 0s"
                ></div>
                <div
                    class="h-2 w-2 animate-bounce rounded-full bg-blue-600"
                    style="animation-delay: 0.1s"
                ></div>
                <div
                    class="h-2 w-2 animate-bounce rounded-full bg-blue-600"
                    style="animation-delay: 0.2s"
                ></div>
            </div>
        </div>

        <!-- Star Wars themed elements -->
        <div class="mt-6 max-w-xs text-center text-xs text-gray-400">
            <p>
                May the Force be with you while we search through the archives
                of the Jedi Temple...
            </p>
        </div>
    </div>
</template>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-bounce {
    animation: bounce 1.4s infinite;
}

@keyframes bounce {
    0%,
    80%,
    100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
}
</style>
