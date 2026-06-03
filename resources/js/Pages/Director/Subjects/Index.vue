<template>
    <AppLayout :title="`Disciplinas — ${classroom.name}`" :nav-items="navItems">

        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <Link :href="route('director.classrooms.index')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="pi pi-arrow-left text-sm" />
                </Link>
                <div>
                    <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ classroom.name }}</h2>
                    <p class="text-sm text-slate-400 mt-0.5">Disciplinas · Ano letivo {{ classroom.school_year }}</p>
                </div>
            </div>
            <Button label="Nova Disciplina" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div class="page-card">
            <DataTable :value="subjects.data" class="text-sm">
                <Column header="Disciplina">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                                <i class="pi pi-book text-purple-600 dark:text-purple-400 text-xs" />
                            </div>
                            <p class="font-medium text-slate-800 dark:text-slate-100">{{ data.name }}</p>
                        </div>
                    </template>
                </Column>
                <Column header="Professor" style="width:200px">
                    <template #body="{ data }">
                        <span v-if="data.teacher" class="inline-flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-300">
                            <div class="w-5 h-5 rounded-full bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                                <span class="text-[9px] font-semibold text-violet-600 dark:text-violet-400">{{ data.teacher.name.charAt(0) }}</span>
                            </div>
                            {{ data.teacher.name }}
                        </span>
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">Sem professor</span>
                    </template>
                </Column>
                <Column header="Horários">
                    <template #body="{ data }">
                        <div v-if="data.schedules?.length" class="flex flex-wrap gap-1">
                            <span
                                v-for="s in data.schedules"
                                :key="s.id"
                                class="inline-flex items-center gap-1 text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded-full"
                            >
                                <i class="pi pi-clock text-[9px]" />
                                {{ dayLabel(s.day_of_week) }} {{ s.time_start }}–{{ s.time_end }}
                            </span>
                        </div>
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                    </template>
                </Column>
                <Column header="C.H." style="width:70px">
                    <template #body="{ data }">
                        <span v-if="data.workload" class="text-xs text-slate-500 dark:text-slate-400">{{ data.workload }}h</span>
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                    </template>
                </Column>
                <Column header="Máx. Faltas" style="width:110px">
                    <template #body="{ data }">
                        <span v-if="data.max_absences" class="inline-flex items-center gap-1 text-xs bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">
                            <i class="pi pi-exclamation-circle text-[9px]" />
                            {{ data.max_absences }}
                        </span>
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                    </template>
                </Column>
                <Column header="" style="width:80px">
                    <template #body="{ data }">
                        <div class="flex gap-0.5 justify-end">
                            <Button icon="pi pi-pencil" size="small" severity="secondary" text rounded v-tooltip.left="'Editar'" @click="openEdit(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" text rounded v-tooltip.left="'Remover'" @click="confirmDelete(data)" />
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                            <i class="pi pi-book text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nenhuma disciplina cadastrada</p>
                        <p class="text-xs text-slate-400 mt-1">Clique em "Nova Disciplina" para começar.</p>
                    </div>
                </template>
            </DataTable>
            <Pagination :data="subjects" />
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Disciplina' : 'Nova Disciplina'"
            :subtitle="editingId ? 'Atualize os dados da disciplina.' : 'Defina a disciplina, professor e horários.'"
            icon="pi pi-book"
            width="540px"
        >
            <form id="subject-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome da disciplina" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: Matemática" />
                </FormField>

                <FormField label="Professor responsável" :error="form.errors.teacher_id" required>
                    <Select v-model="form.teacher_id" :options="teachers" option-label="name" option-value="id" placeholder="Selecione o professor" class="w-full" />
                </FormField>

                <div class="grid grid-cols-2 gap-4">
                    <FormField label="Carga horária (h)" :error="form.errors.workload">
                        <InputNumber v-model="form.workload" class="w-full" :min="1" placeholder="Ex: 80" />
                    </FormField>
                    <FormField label="Máx. faltas" :error="form.errors.max_absences">
                        <InputNumber v-model="form.max_absences" class="w-full" :min="1" placeholder="Ex: 20" />
                    </FormField>
                </div>

                <!-- Schedules -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Horários semanais</p>
                        <Button type="button" label="Adicionar" icon="pi pi-plus" size="small" severity="secondary" outlined @click="addSchedule" />
                    </div>
                    <div v-if="form.schedules.length === 0" class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700 py-5 text-center text-xs text-slate-400">
                        Nenhum horário adicionado.
                    </div>
                    <div v-for="(s, i) in form.schedules" :key="i" class="flex items-center gap-2 mb-2">
                        <Select v-model="s.day_of_week" :options="days" option-label="label" option-value="value" placeholder="Dia" class="flex-1" />
                        <InputText v-model="s.time_start" placeholder="08:00" class="w-24" maxlength="5" />
                        <span class="text-slate-300 dark:text-slate-600 text-sm">→</span>
                        <InputText v-model="s.time_end" placeholder="09:30" class="w-24" maxlength="5" />
                        <Button type="button" icon="pi pi-trash" size="small" severity="danger" text rounded @click="removeSchedule(i)" />
                    </div>
                </div>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="subject-form" :label="editingId ? 'Salvar alterações' : 'Criar disciplina'" icon="pi pi-check" :loading="form.processing" />
            </template>
        </AppDialog>

        <ConfirmDialog />
    </AppLayout>
</template>

<script setup lang="ts">
import AppDialog from '@/Components/AppDialog.vue';
import FormField from '@/Components/FormField.vue';
import Pagination from '@/Components/Pagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { directorNav } from '@/nav';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { useConfirm } from 'primevue/useconfirm';
import { ref } from 'vue';

type Schedule = { id?: number; day_of_week: number; time_start: string; time_end: string };
type Subject = { id: number; name: string; teacher_id: number; teacher: { name: string } | null; schedules: Schedule[]; workload: number | null; max_absences: number | null };
type Paginated<T> = { data: T[]; current_page: number; last_page: number; from: number; to: number; total: number; links: { url: string | null; label: string; active: boolean }[] };

const props = defineProps<{
    classroom: { id: number; name: string; school_year: number };
    subjects: Paginated<Subject>;
    teachers: { id: number; name: string }[];
}>();

const navItems = directorNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);

const days = [
    { label: 'Segunda', value: 1 }, { label: 'Terça', value: 2 }, { label: 'Quarta', value: 3 },
    { label: 'Quinta', value: 4 }, { label: 'Sexta', value: 5 },
];

function dayLabel(d: number) { return days.find(x => x.value === d)?.label ?? '—'; }

const form = useForm({
    name: '', teacher_id: null as number | null,
    workload: null as number | null, max_absences: null as number | null,
    schedules: [] as { day_of_week: number; time_start: string; time_end: string }[],
});

function openCreate() { form.reset(); form.schedules = []; editingId.value = null; dialogVisible.value = true; }
function openEdit(s: Subject) {
    form.name = s.name; form.teacher_id = s.teacher_id;
    form.workload = s.workload; form.max_absences = s.max_absences;
    form.schedules = s.schedules.map(sc => ({ day_of_week: sc.day_of_week, time_start: sc.time_start, time_end: sc.time_end }));
    editingId.value = s.id; dialogVisible.value = true;
}
function addSchedule() { form.schedules.push({ day_of_week: 1, time_start: '', time_end: '' }); }
function removeSchedule(i: number) { form.schedules.splice(i, 1); }

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('director.subjects.update', editingId.value), opts)
        : form.post(route('director.classrooms.subjects.store', props.classroom.id), opts);
}

function confirmDelete(s: Subject) {
    confirm.require({
        message: `Remover a disciplina "${s.name}"?`,
        header: 'Confirmar remoção',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('director.subjects.destroy', s.id)),
    });
}
</script>
