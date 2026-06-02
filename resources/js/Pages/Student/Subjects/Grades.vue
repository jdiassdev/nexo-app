<template>
    <AppLayout :title="`Notas — ${subject.name}`" :nav-items="navItems">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('student.dashboard')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <i class="pi pi-arrow-left text-sm" />
            </Link>
            <div>
                <h2 class="text-base font-semibold text-slate-800">{{ subject.name }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">Histórico de notas por bimestre</p>
            </div>
        </div>

        <!-- Bimestre cards -->
        <div class="grid gap-4 lg:grid-cols-2 mb-5">
            <div v-for="q in [1, 2, 3, 4]" :key="q" class="page-card">
                <div class="px-4 py-3.5 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-700">{{ q }}º Bimestre</span>
                    <span
                        class="text-sm font-bold px-2.5 py-0.5 rounded-full"
                        :class="summary.quarters[q] === null
                            ? 'text-slate-400'
                            : summary.quarters[q]! >= passingGrade ? 'text-emerald-700 bg-emerald-50' : 'text-red-600 bg-red-50'"
                    >
                        {{ summary.quarters[q] !== null ? `Média: ${summary.quarters[q]}` : '—' }}
                    </span>
                </div>
                <DataTable :value="activities[q] ?? []" class="text-sm">
                    <Column field="title" header="Atividade" />
                    <Column header="Nota" style="width:130px">
                        <template #body="{ data }">
                            <span v-if="data.score !== null" class="flex items-center gap-1.5">
                                <span :class="scoreClass(data.score, passingGrade)" class="font-semibold">{{ data.score }}</span>
                                <span class="text-slate-300 text-xs">/ {{ data.max_grade }}</span>
                            </span>
                            <span v-else class="text-slate-300">—</span>
                        </template>
                    </Column>
                    <template #empty>
                        <p class="text-xs text-center text-slate-400 py-3">Sem atividades neste bimestre.</p>
                    </template>
                </DataTable>
            </div>
        </div>

        <!-- Annual summary -->
        <div class="page-card p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Resumo do Ano</h3>
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 mb-4">
                <div v-for="item in summaryItems" :key="item.label">
                    <p class="text-xs text-slate-400 mb-1">{{ item.label }}</p>
                    <p class="text-xl font-bold" :class="item.cls">{{ item.value ?? '—' }}</p>
                </div>
            </div>

            <div v-if="summary.final !== null" class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold"
                        :class="summary.passing ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                    >
                        <i :class="summary.passing ? 'pi pi-check-circle' : 'pi pi-times-circle'" />
                        {{ summary.passing ? 'Aprovado' : 'Reprovado' }}
                    </span>
                    <span class="text-slate-500 text-sm">Média Final: <strong class="text-slate-800">{{ summary.final }}</strong></span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { studentNav } from '@/nav';
import { avgClass, scoreClass } from '@/utils';
import { Link } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { computed } from 'vue';

const props = defineProps<{
    subject: { id: number; name: string };
    activities: Record<number, { id: number; title: string; max_grade: number; score: number | null }[]>;
    summary: {
        quarters: Record<number, number | null>;
        recovery_1: number | null; recovery_2: number | null;
        final_exam: number | null; semester_1: number | null;
        semester_2: number | null; final: number | null; passing: boolean;
    };
}>();

const navItems = studentNav;
const passingGrade = Number(import.meta.env.VITE_PASSING_GRADE ?? 5);

const summaryItems = computed(() => [
    { label: '1º Semestre', value: props.summary.semester_1, cls: avgClass(props.summary.semester_1, passingGrade) },
    { label: 'Recuperação 1', value: props.summary.recovery_1, cls: props.summary.recovery_1 !== null ? 'text-amber-600' : 'text-slate-300' },
    { label: '2º Semestre', value: props.summary.semester_2, cls: avgClass(props.summary.semester_2, passingGrade) },
    { label: 'Recuperação 2', value: props.summary.recovery_2, cls: props.summary.recovery_2 !== null ? 'text-amber-600' : 'text-slate-300' },
]);
</script>
