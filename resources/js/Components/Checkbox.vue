<script setup lang="ts">
withDefaults(
    defineProps<{
        checked?: boolean;
        value?: string;
    }>(),
    {
        checked: false,
        value: '',
    }
);

defineEmits<{
    (e: 'update:checked', value: boolean): void;
}>();
</script>

<template>
    <input
        :checked="checked"
        :value="value"
        type="checkbox"
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
        @change="$emit('update:checked', ($event.target as HTMLInputElement).checked)"
    />
</template>