<script setup lang="ts">
import { onMounted, ref } from 'vue';

withDefaults(
    defineProps<{
        modelValue?: string;
        type?: string;
        placeholder?: string;
        disabled?: boolean;
    }>(),
    {
        type: 'text',
        modelValue: '',
    }
);

defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'input', event: Event): void;
}>();

const input = ref<HTMLInputElement>();

const focus = (): void => {
    input.value?.focus();
};

defineExpose({ focus });

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        ref="input"
        :value="modelValue"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        @input="($event) => {
            $emit('update:modelValue', ($event.target as HTMLInputElement).value);
            $emit('input', $event);
        }"
    />
</template>