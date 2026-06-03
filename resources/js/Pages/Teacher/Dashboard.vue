<template>
    <AppLayout title="Minhas Disciplinas" :nav-items="navItems">

        <div class="mb-6">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Minhas Disciplinas</h2>
            <p class="text-sm text-slate-400 mt-0.5">{{ subjects.length }} {{ subjects.length === 1 ? 'disciplina atribuída' : 'disciplinas atribuídas' }}</p>
        </div>

        <div v-if="subjects.length === 0" class="page-card">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <i class="pi pi-book text-slate-400 text-xl" />
                </div>
                <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nenhuma disciplina atribuída</p>
                <p class="text-xs text-slate-400 mt-1">Entre em contato com o diretor para ser vinculado a uma turma.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div
                v-for="subject in subjects"
                :key="subject.id"
                class="page-card overflow-hidden"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                            <i class="pi pi-book text-indigo-600 dark:text-indigo-400 text-sm" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-800 dark:text-slate-100 leading-tight">{{ subject.name }}</h2>
                            <span class="inline-flex items-center gap-1 text-xs text-slate-400 mt-0.5">
                                <i class="pi pi-objects-column text-[10px]" />
                                {{ subject.classroom.name }} · {{ subject.classroom.school_year }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Link :href="route('teacher.subjects.activities.index', subject.id)">
                            <Button label="Atividades" icon="pi pi-list" size="small" severity="secondary" outlined />
                        </Link>
                        <Link :href="route('teacher.subjects.attendance.index', subject.id)">
                            <Button label="Faltas" icon="pi pi-calendar-clock" size="small" severity="secondary" outlined />
                        </Link>
                        <Link :href="route('teacher.subjects.recovery.index', subject.id)">
                            <Button label="Recuperações" icon="pi pi-refresh" size="small" severity="secondary" outlined />
                        </Link>
                    </div>
                </div>

                <!-- Stats strip -->
                <div class="grid grid-cols-4 divide-x divide-slate-100 dark:divide-slate-800">
                    <div class="px-5 py-3.5">
                        <p class="text-xs text-slate-400 mb-1">Atividades</p>
                        <p class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ subject.activities_count }}</p>
                    </div>
                    <div class="px-5 py-3.5">
                        <p class="text-xs text-slate-400 mb-1">Provas</p>
                        <p class="text-xl font-bold text-slate-700 dark:text-slate-200">{{ subject.exams_count }}</p>
                    </div>
                    <div class="px-5 py-3.5">
                        <p class="text-xs text-slate-400 mb-1">Média da turma</p>
                        <p class="text-xl font-bold"
                            :class="subject.avg_score === null ? 'text-slate-300 dark:text-slate-600' : subject.avg_score >= passingGrade ? 'text-emerald-600' : 'text-red-500'"
                        >
                            {{ subject.avg_score !== null ? subject.avg_score.toFixed(1) : '—' }}
                        </p>
                    </div>
                    <div class="px-5 py-3.5">
                        <p class="text-xs text-slate-400 mb-1">Faltas registradas</p>
                        <p class="text-xl font-bold"
                            :class="subject.max_absences && subject.absences >= subject.max_absences ? 'text-red-500' : 'text-slate-700 dark:text-slate-200'"
                        >
                            {{ subject.absences }}
                            <span v-if="subject.max_absences" class="text-sm font-normal text-slate-400">/ {{ subject.max_absences }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { teacherNav } from '@/nav';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps<{
    subjects: {
        id: number;
        name: string;
        activities_count: number;
        exams_count: number;
        absences: number;
        max_absences: number | null;
        avg_score: number | null;
        total_grades: number;
        classroom: { id: number; name: string; school_year: number };
    }[];
}>();

const navItems = teacherNav;
const passingGrade = Number(import.meta.env.VITE_PASSING_GRADE ?? 5);
</script>
