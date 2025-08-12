<script setup lang="ts">
withDefaults(
    defineProps<{
        message?: string;
    }>(),
    {
        message: '',
    }
);
</script>

<template>
    <div v-show="message">
        <p class="text-sm text-red-600">{{ message }}</p>
    </div>
</template>