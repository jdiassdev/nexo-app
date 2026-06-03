<template>
    <AppLayout title="Dashboard" :nav-items="navItems">

        <div class="mb-6">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Visão Geral da Escola</h2>
            <p class="text-sm text-slate-400 mt-0.5">Resumo de turmas, disciplinas, professores e alunos</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div v-for="card in statCards" :key="card.label" class="stat-card">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="card.bg">
                        <i :class="[card.icon, card.color, 'text-base']" />
                    </div>
                    <p class="text-xs text-slate-400 font-medium">{{ card.label }}</p>
                </div>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">{{ card.value }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { directorNav } from '@/nav';
import { computed } from 'vue';

const props = defineProps<{
    stats: { classrooms: number; teachers: number; students: number; subjects: number };
}>();

const navItems = directorNav;

const statCards = computed(() => [
    { label: 'Turmas', value: props.stats.classrooms, icon: 'pi pi-objects-column', color: 'text-blue-600', bg: 'bg-blue-50 dark:bg-blue-900/30' },
    { label: 'Disciplinas', value: props.stats.subjects, icon: 'pi pi-book', color: 'text-purple-600', bg: 'bg-purple-50 dark:bg-purple-900/30' },
    { label: 'Professores', value: props.stats.teachers, icon: 'pi pi-user', color: 'text-violet-600', bg: 'bg-violet-50 dark:bg-violet-900/30' },
    { label: 'Alunos', value: props.stats.students, icon: 'pi pi-users', color: 'text-emerald-600', bg: 'bg-emerald-50 dark:bg-emerald-900/30' },
]);
</script>
