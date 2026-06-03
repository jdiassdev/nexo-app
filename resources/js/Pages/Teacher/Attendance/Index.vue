<template>
    <AppLayout :title="`Faltas — ${subject.name}`" :nav-items="navItems">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <Link :href="route('teacher.dashboard')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="pi pi-arrow-left text-sm" />
                </Link>
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ subject.name }}</h2>
                    <p class="text-sm text-slate-400 mt-0.5">
                        {{ subject.classroom.name }} · {{ subject.classroom.school_year }}
                        <span v-if="subject.max_absences" class="ml-2 text-xs text-slate-400">
                            Limite: {{ subject.max_absences }} faltas
                        </span>
                    </p>
                </div>
            </div>
            <DatePicker
                v-model="selectedDate"
                date-format="dd/mm/yy"
                show-button-bar
                @date-select="onDateChange"
                class="w-44"
            />
        </div>

        <!-- Stats bar -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="stat-card flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <i class="pi pi-users text-slate-500 dark:text-slate-400" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 leading-tight">Total</p>
                    <p class="text-lg font-semibold text-slate-800 dark:text-slate-100 leading-tight">{{ rows.length }}</p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <i class="pi pi-check-circle text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 leading-tight">Presentes</p>
                    <p class="text-lg font-semibold text-emerald-600 leading-tight">{{ presentCount }}</p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                    <i class="pi pi-times-circle text-red-400" />
                </div>
                <div>
                    <p class="text-xs text-slate-400 leading-tight">Faltas hoje</p>
                    <p class="text-lg font-semibold text-red-500 leading-tight">{{ absentCount }}</p>
                </div>
            </div>
        </div>

        <!-- Table card -->
        <div class="page-card">

            <!-- Toolbar -->
            <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center gap-3">
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
                    class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                    @click="search = ''"
                >
                    <i class="pi pi-times-circle" /> Limpar
                </button>
                <div class="ml-auto flex gap-2">
                    <Button label="Todos presentes" severity="secondary" outlined size="small" icon="pi pi-check-circle" @click="markAll(true)" />
                    <Button label="Salvar Presença" icon="pi pi-check" size="small" :loading="saving" @click="save" />
                </div>
            </div>

            <DataTable :value="filteredRows" class="text-sm">
                <Column header="Aluno">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
                                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ data.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-slate-100 leading-tight">{{ data.name }}</p>
                                <p class="font-mono text-[11px] text-slate-400 leading-tight mt-0.5">{{ data.enrollment }}</p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Total de Faltas" style="width:150px">
                    <template #body="{ data }">
                        <div class="flex items-center gap-2">
                            <span :class="[
                                'text-sm font-semibold',
                                isOverLimit(data.student_id) ? 'text-red-600' : 'text-slate-700 dark:text-slate-200'
                            ]">
                                {{ summary[data.student_id] ?? 0 }}
                            </span>
                            <span v-if="subject.max_absences" class="text-xs text-slate-300 dark:text-slate-600">/ {{ subject.max_absences }}</span>
                            <span v-if="isOverLimit(data.student_id)" class="text-[10px] bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900 px-1.5 py-0.5 rounded-full font-medium">
                                Excedeu
                            </span>
                        </div>
                    </template>
                </Column>
                <Column header="Situação hoje" style="width:200px">
                    <template #body="{ data }">
                        <div v-if="attendance[data._idx] === null" class="flex gap-2">
                            <button
                                class="text-xs px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors font-medium"
                                @click="attendance[data._idx] = true"
                            >
                                Presente
                            </button>
                            <button
                                class="text-xs px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 transition-colors font-medium"
                                @click="attendance[data._idx] = false"
                            >
                                Falta
                            </button>
                        </div>
                        <div v-else class="flex items-center gap-2.5">
                            <ToggleSwitch v-model="attendance[data._idx]" />
                            <span class="text-xs font-medium" :class="attendance[data._idx] ? 'text-emerald-600' : 'text-red-500'">
                                {{ attendance[data._idx] ? 'Presente' : 'Falta' }}
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
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import InputText from 'primevue/inputtext';
import ToggleSwitch from 'primevue/toggleswitch';
import { computed, ref } from 'vue';
import dayjs from 'dayjs';

type Row = { student_id: number; name: string; enrollment: string; present: boolean | null };

const props = defineProps<{
    subject: { id: number; name: string; max_absences: number | null; classroom: { name: string; school_year: number } };
    date: string;
    rows: Row[];
    summary: Record<number, number>;
    filters: { search: string };
}>();

const navItems = teacherNav;
const saving = ref(false);
const attendance = ref<(boolean | null)[]>(props.rows.map(r => r.present));
const selectedDate = ref(new Date(props.date + 'T12:00:00'));
const search = ref('');

const filteredRows = computed(() => {
    const indexed = props.rows.map((r, i) => ({ ...r, _idx: i }));
    if (!search.value.trim()) return indexed;
    const q = search.value.toLowerCase();
    return indexed.filter(r => r.name.toLowerCase().includes(q) || r.enrollment.toLowerCase().includes(q));
});

const presentCount = computed(() => attendance.value.filter(v => v === true).length);
const absentCount = computed(() => attendance.value.filter(v => v === false).length);

function isOverLimit(studentId: number): boolean {
    if (!props.subject.max_absences) return false;
    return (props.summary[studentId] ?? 0) >= props.subject.max_absences;
}

function onDateChange() {
    router.get(route('teacher.subjects.attendance.index', props.subject.id), {
        date: dayjs(selectedDate.value).format('YYYY-MM-DD'),
    }, { preserveState: false });
}

function markAll(present: boolean) {
    attendance.value = attendance.value.map(() => present);
}

function save() {
    saving.value = true;
    router.post(
        route('teacher.subjects.attendance.store', props.subject.id),
        {
            date: dayjs(selectedDate.value).format('YYYY-MM-DD'),
            attendance: props.rows.map((r, i) => ({
                student_id: r.student_id,
                present: attendance.value[i] ?? true,
            })),
        },
        { onFinish: () => { saving.value = false; } }
    );
}
</script>
