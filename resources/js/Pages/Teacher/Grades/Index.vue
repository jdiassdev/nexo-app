<template>
    <AppLayout :title="`Notas — ${activity.title}`" :nav-items="navItems">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <Link :href="route('teacher.subjects.activities.index', subject.id)" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                    <i class="pi pi-arrow-left text-sm" />
                </Link>
                <div>
                    <h2 class="text-base font-semibold text-slate-800">{{ activity.title }}</h2>
                    <p class="text-sm text-slate-400 mt-0.5">
                        {{ subject.name }} · {{ activity.quarter }}º Bimestre
                        <span class="ml-2 text-xs text-slate-400">Nota máxima: {{ activity.max_grade }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div v-if="gradedCount > 0" class="text-right">
                    <p class="text-xs text-slate-400">Média da turma</p>
                    <p class="text-lg font-semibold" :class="classAvg !== null && classAvg >= passingGrade ? 'text-emerald-600' : 'text-red-500'">
                        {{ classAvg !== null ? classAvg.toFixed(1) : '—' }}
                    </p>
                </div>
                <Button label="Salvar Notas" icon="pi pi-check" :loading="saving" @click="save" />
            </div>
        </div>

        <!-- Table card -->
        <div class="page-card">

            <!-- Toolbar -->
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center gap-3">
                <div class="relative flex-1 max-w-xs">
                    <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none" />
                    <InputText
                        v-model="search"
                        placeholder="Filtrar alunos…"
                        class="w-full !pl-9 text-sm"
                    />
                </div>
                <button
                    v-if="search"
                    class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 transition-colors"
                    @click="search = ''"
                >
                    <i class="pi pi-times-circle" /> Limpar
                </button>
                <span class="ml-auto text-xs text-slate-400">
                    {{ gradedCount }} de {{ rows.length }} {{ rows.length === 1 ? 'nota lançada' : 'notas lançadas' }}
                </span>
            </div>

            <DataTable :value="filteredRows" class="text-sm">
                <Column header="Aluno">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                                <span class="text-xs font-semibold text-slate-500">{{ data.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 leading-tight">{{ data.name }}</p>
                                <p class="font-mono text-[11px] text-slate-400 leading-tight mt-0.5">{{ data.enrollment }}</p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Nota" style="width:180px">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2.5">
                            <InputNumber
                                v-model="grades[data._idx]"
                                :min="0"
                                :max="activity.max_grade"
                                :max-fraction-digits="2"
                                placeholder="—"
                                fluid
                                class="w-28"
                                :input-class="scoreClass(grades[data._idx], passingGrade)"
                            />
                            <span v-if="grades[data._idx] !== null && grades[data._idx] !== undefined"
                                class="text-xs font-medium"
                                :class="(grades[data._idx] ?? 0) >= passingGrade ? 'text-emerald-600' : 'text-red-500'"
                            >
                                {{ (grades[data._idx] ?? 0) >= passingGrade ? 'Aprovado' : 'Reprovado' }}
                            </span>
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { teacherNav } from '@/nav';
import { scoreClass } from '@/utils';
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import { computed, ref } from 'vue';

type Row = { student_id: number; name: string; enrollment: string; score: number | null };

const props = defineProps<{
    activity: { id: number; title: string; quarter: number; max_grade: number; subject_id: number };
    subject: { id: number; name: string; classroom: { name: string; school_year: number } };
    rows: Row[];
    filters: { search: string };
}>();

const navItems = teacherNav;
const saving = ref(false);
const passingGrade = Number(import.meta.env.VITE_PASSING_GRADE ?? 5);
const grades = ref<(number | null)[]>(props.rows.map(r => r.score));
const search = ref('');

const filteredRows = computed(() => {
    const indexed = props.rows.map((r, i) => ({ ...r, _idx: i }));
    if (!search.value.trim()) return indexed;
    const q = search.value.toLowerCase();
    return indexed.filter(r => r.name.toLowerCase().includes(q) || r.enrollment.toLowerCase().includes(q));
});

const gradedCount = computed(() => grades.value.filter(g => g !== null).length);

const classAvg = computed(() => {
    const scored = grades.value.filter((g): g is number => g !== null);
    if (!scored.length) return null;
    return scored.reduce((a, b) => a + b, 0) / scored.length;
});

function save() {
    saving.value = true;
    router.post(
        route('teacher.activities.grades.store', props.activity.id),
        {
            grades: props.rows.map((r, i) => ({
                student_id: r.student_id,
                score: grades.value[i],
            })),
        },
        { onFinish: () => { saving.value = false; } },
    );
}
</script>
