<template>
    <div v-if="data.last_page > 1" class="flex items-center justify-between px-4 py-3 border-t border-slate-100 dark:border-slate-800">
        <p class="text-xs text-slate-400">
            {{ data.from }}–{{ data.to }} de {{ data.total }}
        </p>
        <div class="flex items-center gap-1">
            <Link
                v-for="link in visibleLinks"
                :key="link.label"
                :href="link.url ?? '#'"
                :class="[
                    'min-w-[32px] h-8 px-2 flex items-center justify-center rounded-lg text-xs transition-colors',
                    link.active ? 'bg-indigo-600 text-white font-semibold' : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800',
                    !link.url ? 'opacity-30 pointer-events-none' : '',
                ]"
                preserve-scroll
                v-html="link.label"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    data: {
        current_page: number;
        last_page: number;
        from: number;
        to: number;
        total: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
}>();

const visibleLinks = computed(() => {
    const links = props.data.links;
    if (links.length <= 7) return links;

    const current = props.data.current_page;
    const last = props.data.last_page;
    const result = [];

    result.push(links[0]);

    if (current > 3) result.push({ url: null, label: '...', active: false });

    for (let i = Math.max(2, current - 1); i <= Math.min(last - 1, current + 1); i++) {
        result.push(links[i]);
    }

    if (current < last - 2) result.push({ url: null, label: '...', active: false });

    result.push(links[last + 1]);

    return result;
});
</script>
