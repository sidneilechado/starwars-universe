<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        href?: string;
        as?: string;
        method?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    }>(),
    {
        href: '#',
        as: 'a',
        method: 'get',
    }
);
</script>

<template>
    <div>
        <Link
            v-if="as === 'button'"
            :href="href"
            :method="method"
            as="button"
            class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        >
            <slot />
        </Link>

        <Link
            v-else
            :href="href"
            class="block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        >
            <slot />
        </Link>
    </div>
</template>