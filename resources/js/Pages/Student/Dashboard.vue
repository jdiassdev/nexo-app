<template>
    <AppLayout title="Minhas Disciplinas" :nav-items="navItems">

        <div class="mb-6">
            <h2 class="text-base font-semibold text-slate-800">Minhas Disciplinas</h2>
            <p class="text-sm text-slate-400 mt-0.5">{{ subjects.length }} {{ subjects.length === 1 ? 'disciplina' : 'disciplinas' }} neste ano letivo</p>
        </div>

        <div v-if="subjects.length === 0" class="page-card">
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                    <i class="pi pi-book text-slate-400 text-xl" />
                </div>
                <p class="text-sm font-medium text-slate-600">Nenhuma disciplina encontrada</p>
                <p class="text-xs text-slate-400 mt-1">Você ainda não está matriculado em nenhuma turma.</p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <div
                v-for="subject in subjects"
                :key="subject.id"
                class="page-card flex flex-col overflow-hidden"
                :class="subject.final !== null && !subject.passing ? 'ring-1 ring-red-200' : ''"
            >
                <!-- Header -->
                <div class="px-5 pt-5 pb-4 border-b border-slate-100">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="min-w-0">
                            <h2 class="font-semibold text-slate-800 leading-tight truncate">{{ subject.name }}</h2>
                            <p class="text-xs text-slate-400 mt-0.5 flex items-center gap-1">
                                <i class="pi pi-user text-[10px]" />
                                {{ subject.teacher }}
                            </p>
                        </div>
                        <span
                            v-if="subject.final !== null"
                            class="shrink-0 inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-semibold"
                            :class="subject.passing ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                        >
                            <i :class="subject.passing ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="text-[10px]" />
                            {{ subject.passing ? 'Aprovado' : 'Reprovado' }}
                        </span>
                    </div>

                    <!-- Schedules -->
                    <div v-if="subject.schedules.length" class="flex flex-wrap gap-1">
                        <span
                            v-for="(s, i) in subject.schedules"
                            :key="i"
                            class="inline-flex items-center gap-1 text-xs bg-slate-50 text-slate-500 border border-slate-200 px-2 py-0.5 rounded-full"
                        >
                            <i class="pi pi-clock text-[9px] text-slate-400" />
                            {{ s.day_label }} {{ s.time_start }}–{{ s.time_end }}
                        </span>
                    </div>
                </div>

                <!-- Stats -->
                <div class="px-5 py-4 flex gap-6 border-b border-slate-100">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Média Final</p>
                        <p class="text-2xl font-bold"
                            :class="subject.final === null ? 'text-slate-300' : subject.passing ? 'text-emerald-600' : 'text-red-500'">
                            {{ subject.final ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Faltas</p>
                        <p class="text-2xl font-bold"
                            :class="subject.max_absences && subject.absences >= subject.max_absences ? 'text-red-500' : 'text-slate-700'">
                            {{ subject.absences }}
                            <span v-if="subject.max_absences" class="text-sm font-normal text-slate-400">/ {{ subject.max_absences }}</span>
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-5 py-3.5 flex gap-2 mt-auto">
                    <Link :href="route('student.subjects.grades', subject.id)" class="flex-1">
                        <Button label="Ver Notas" icon="pi pi-star" size="small" severity="secondary" outlined class="w-full" />
                    </Link>
                    <Link :href="route('student.subjects.attendance', subject.id)" class="flex-1">
                        <Button label="Presenças" icon="pi pi-calendar-clock" size="small" severity="secondary" outlined class="w-full" />
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { studentNav } from '@/nav';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';

defineProps<{
    subjects: {
        id: number;
        name: string;
        teacher: string;
        schedules: { day_of_week: number; day_label: string; time_start: string; time_end: string }[];
        final: number | null;
        passing: boolean;
        absences: number;
        max_absences: number | null;
    }[];
}>();

const navItems = studentNav;
</script>
