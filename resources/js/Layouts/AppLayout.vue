<template>
    <div class="flex h-screen bg-slate-50 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-60 flex flex-col bg-white border-r border-slate-200 shrink-0">
            <div class="h-14 flex items-center px-5 border-b border-slate-100">
                <span class="text-lg font-bold tracking-tight text-indigo-600">Nexo</span>
                <span class="ml-1.5 text-[10px] text-slate-400 font-semibold uppercase tracking-[0.2em]">Escolar</span>
            </div>

            <nav class="flex-1 overflow-y-auto py-3 px-2">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors mb-0.5"
                    :class="isActive(item.route) ? 'bg-indigo-50 text-indigo-700 font-medium' : ''"
                >
                    <i :class="[item.icon, 'text-base', isActive(item.route) ? 'text-indigo-600' : 'text-slate-400']" />
                    {{ item.label }}
                </Link>
            </nav>

            <div class="border-t border-slate-100 p-3">
                <div class="flex items-center gap-2.5 px-2 py-1.5 mb-1">
                    <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                        <span class="text-xs font-semibold text-indigo-700">{{ user.name.charAt(0).toUpperCase() }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate leading-tight">{{ user.name }}</p>
                        <p class="text-xs text-slate-400 leading-tight">{{ roleLabel }}</p>
                    </div>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                >
                    <i class="pi pi-sign-out text-sm" />
                    Sair
                </Link>
            </div>
        </aside>

        <!-- Main -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-14 bg-white border-b border-slate-200 flex items-center px-6 shrink-0">
                <h1 class="text-sm font-semibold text-slate-800">{{ title }}</h1>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <slot />
            </main>
        </div>
    </div>
    <Toast position="bottom-right" />
</template>

<script setup lang="ts">
import type { PageProps } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { computed, watch } from 'vue';

defineProps<{
    title: string;
    navItems: { label: string; route: string; icon: string }[];
}>();

const page = usePage<PageProps>();
const user = computed(() => page.props.auth.user);
const toast = useToast();

const roleLabels: Record<string, string> = {
    god: 'Administrador',
    director: 'Diretor',
    teacher: 'Professor',
    student: 'Aluno',
};

const roleLabel = computed(() => roleLabels[user.value.role] ?? user.value.role);

function isActive(routeName: string): boolean {
    return route().current(routeName) ?? false;
}

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.add({ severity: 'success', summary: 'Sucesso', detail: flash.success, life: 3500 });
        }
        if (flash?.error) {
            toast.add({ severity: 'error', summary: 'Erro', detail: flash.error, life: 5000 });
        }
    },
    { deep: true, immediate: true },
);
</script>
