<template>
    <Dialog
        v-model:visible="model"
        modal
        :closable="false"
        :style="{ width, maxWidth: '95vw' }"
        :pt="{
            root: { class: 'rounded-2xl shadow-2xl border border-slate-200 overflow-hidden' },
            mask: { class: 'backdrop-blur-sm' },
            header: { class: 'hidden' },
            content: { class: 'p-0' },
        }"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-6 pt-5 pb-4">
            <div class="flex items-center gap-3">
                <div v-if="icon" class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
                    <i :class="[icon, 'text-indigo-600 text-[15px]']" />
                </div>
                <div>
                    <h3 class="text-[15px] font-semibold text-slate-800 leading-tight">{{ title }}</h3>
                    <p v-if="subtitle" class="text-xs text-slate-400 mt-0.5">{{ subtitle }}</p>
                </div>
            </div>
            <button
                type="button"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                @click="model = false"
            >
                <i class="pi pi-times text-sm" />
            </button>
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-100" />

        <!-- Body -->
        <div class="px-6 py-5">
            <slot />
        </div>

        <!-- Footer -->
        <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-end gap-2">
            <slot name="footer" />
        </div>
    </Dialog>
</template>

<script setup lang="ts">
import Dialog from 'primevue/dialog';

const model = defineModel<boolean>({ required: true });

withDefaults(defineProps<{
    title: string;
    subtitle?: string;
    icon?: string;
    width?: string;
}>(), {
    width: '460px',
});
</script>
