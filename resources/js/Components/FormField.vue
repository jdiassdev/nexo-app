<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-slate-700 mb-1.5">
            {{ label }}
            <span v-if="required" class="text-red-400 ml-0.5">*</span>
        </label>
        <slot />
        <p v-if="error" class="flex items-center gap-1 text-red-500 text-xs mt-1.5">
            <i class="pi pi-exclamation-circle text-xs" />
            {{ error }}
        </p>
        <p v-else-if="hint" class="text-slate-400 text-xs mt-1.5">{{ hint }}</p>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    label?: string;
    error?: string;
    hint?: string;
    required?: boolean;
}>();
</script>
