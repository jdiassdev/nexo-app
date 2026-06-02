<template>
    <AppLayout :title="`Recuperações — ${subject.name}`" :nav-items="navItems">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <Link :href="route('teacher.dashboard')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <i class="pi pi-arrow-left text-sm" />
                </Link>
                <div>
                    <h2 class="text-base font-semibold text-slate-800">{{ subject.name }}</h2>
                    <p class="text-sm text-slate-400 mt-0.5">Recuperações · {{ subject.classroom.name }} · {{ subject.classroom.school_year }}</p>
                </div>
            </div>
            <Button label="Salvar Recuperações" icon="pi pi-check" :loading="saving" @click="save" />
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4 bg-indigo-50 border border-indigo-100 rounded-xl px-5 py-3.5 mb-5 text-xs text-indigo-700">
            <span class="flex items-center gap-1.5"><i class="pi pi-info-circle text-indigo-400" /> <strong>Rec. 1</strong> — após bimestres 1 e 2</span>
            <span class="text-indigo-200">·</span>
            <span><strong>Rec. 2</strong> — após bimestres 3 e 4</span>
            <span class="text-indigo-200">·</span>
            <span><strong>Prova Final</strong> — alunos ainda reprovados após recuperações</span>
            <span class="text-indigo-200">·</span>
            <span>Nota máxima: <strong>10.0</strong></span>
        </div>

        <!-- Table -->
        <div class="page-card overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-28">Matrícula</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Aluno</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-24">Sem. 1</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-28">
                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full">Rec. 1</span>
                        </th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-24">Sem. 2</th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-28">
                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full">Rec. 2</span>
                        </th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-28">
                            <span class="inline-flex items-center gap-1 bg-orange-50 text-orange-700 px-2 py-0.5 rounded-full">Prova Final</span>
                        </th>
                        <th class="text-center px-3 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide w-24">Final</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(row, i) in rows"
                        :key="row.student_id"
                        class="border-b border-slate-50 last:border-0 transition-colors"
                        :class="row.passing === false ? 'bg-red-50/30' : 'hover:bg-slate-50/50'"
                    >
                        <td class="px-4 py-3">
                            <span class="font-mono text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">{{ row.enrollment }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                    <span class="text-[9px] font-semibold text-slate-500">{{ row.name.charAt(0) }}</span>
                                </div>
                                <span class="font-medium text-slate-700">{{ row.name }}</span>
                            </div>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span :class="avgClass(row.semester_1)">{{ row.semester_1 ?? '—' }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <input
                                v-model.number="exams[i].recovery_1"
                                type="number" step="0.1" min="0" max="10" placeholder="—"
                                class="w-20 text-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/50 focus:border-indigo-300 transition-colors"
                            />
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span :class="avgClass(row.semester_2)">{{ row.semester_2 ?? '—' }}</span>
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <input
                                v-model.number="exams[i].recovery_2"
                                type="number" step="0.1" min="0" max="10" placeholder="—"
                                class="w-20 text-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/50 focus:border-indigo-300 transition-colors"
                            />
                        </td>
                        <td class="px-3 py-2.5 text-center">
                            <input
                                v-model.number="exams[i].final_exam"
                                type="number" step="0.1" min="0" max="10" placeholder="—"
                                class="w-20 text-center px-2 py-1.5 rounded-lg border border-slate-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400/50 focus:border-indigo-300 transition-colors"
                            />
                        </td>
                        <td class="px-3 py-3 text-center">
                            <span
                                class="inline-flex items-center justify-center w-14 h-7 rounded-lg text-xs font-semibold"
                                :class="row.final_avg === null
                                    ? 'text-slate-400 bg-slate-50'
                                    : row.passing ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50'"
                            >
                                {{ row.final_avg ?? '—' }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { teacherNav } from '@/nav';
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { ref } from 'vue';

type Row = {
    student_id: number; name: string; enrollment: string;
    semester_1: number | null; semester_2: number | null;
    final_avg: number | null; passing: boolean;
    recovery_1: number | null; recovery_2: number | null; final_exam: number | null;
};

const props = defineProps<{
    subject: { id: number; name: string; classroom: { name: string; school_year: number } };
    rows: Row[];
    passing_grade: number;
}>();

const navItems = teacherNav;
const saving = ref(false);

const exams = ref(props.rows.map(r => ({
    student_id: r.student_id,
    recovery_1: r.recovery_1,
    recovery_2: r.recovery_2,
    final_exam: r.final_exam,
})));

function avgClass(val: number | null): string {
    if (val === null) return 'text-slate-300';
    return val >= props.passing_grade ? 'text-emerald-600 font-semibold' : 'text-red-500 font-semibold';
}

function save() {
    saving.value = true;
    router.post(
        route('teacher.subjects.recovery.store', props.subject.id),
        { exams: exams.value },
        { onFinish: () => { saving.value = false; } },
    );
}
</script>
