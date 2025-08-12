<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'submit' | 'button' | 'reset';
        disabled?: boolean;
    }>(),
    {
        type: 'submit',
    }
);
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
        :class="{ 'opacity-25': disabled }"
    >
        <slot />
    </button>
</template>