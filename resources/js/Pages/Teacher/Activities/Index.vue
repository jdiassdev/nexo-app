<template>
    <AppLayout :title="`Atividades — ${subject.name}`" :nav-items="navItems">
        <div class="flex items-center justify-between mb-4">
            <Link :href="route('teacher.dashboard')" class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center gap-1.5">
                <i class="pi pi-arrow-left text-xs" /> Dashboard
            </Link>
            <Button label="Nova Atividade" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div v-for="q in [1, 2, 3, 4]" :key="q" class="mb-5">
            <p class="section-title mb-2">{{ q }}º Bimestre</p>
            <div class="page-card">
                <DataTable :value="byQuarter[q] ?? []" class="text-sm">
                    <Column header="Tipo" style="width:100px">
                        <template #body="{ data }">
                            <span
                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full font-medium"
                                :class="data.type === 'exam' ? 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'"
                            >
                                <i :class="data.type === 'exam' ? 'pi pi-file-edit' : 'pi pi-paperclip'" class="text-[10px]" />
                                {{ data.type === 'exam' ? 'Prova' : 'Atividade' }}
                            </span>
                        </template>
                    </Column>
                    <Column field="title" header="Título" />
                    <Column header="Data" style="width:130px">
                        <template #body="{ data }">
                            <span v-if="data.due_date" class="text-slate-500 dark:text-slate-400">
                                {{ formatDate(data.due_date) }}
                            </span>
                            <span v-else class="text-slate-300 dark:text-slate-600">—</span>
                        </template>
                    </Column>
                    <Column field="max_grade" header="Nota Máx." style="width:100px" />
                    <Column field="grades_count" header="Notas" style="width:80px" />
                    <Column header="Ações" style="width:110px">
                        <template #body="{ data }">
                            <div class="flex gap-1">
                                <Link :href="route('teacher.activities.grades.index', data.id)">
                                    <Button icon="pi pi-star" size="small" severity="secondary" text v-tooltip="'Lançar notas'" />
                                </Link>
                                <Button icon="pi pi-pencil" size="small" severity="secondary" text v-tooltip="'Editar'" @click="openEdit(data)" />
                                <Button icon="pi pi-trash" size="small" severity="danger" text v-tooltip="'Remover'" @click="confirmDelete(data)" />
                            </div>
                        </template>
                    </Column>
                    <template #empty>
                        <p class="text-center text-slate-400 py-3 text-xs">Nenhuma atividade neste bimestre.</p>
                    </template>
                </DataTable>
            </div>
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Atividade' : 'Nova Atividade'"
            :subtitle="editingId ? 'Atualize os dados da atividade.' : 'Crie uma atividade ou prova para a turma.'"
            :icon="form.type === 'exam' ? 'pi pi-file-edit' : 'pi pi-paperclip'"
            width="500px"
        >
            <form id="activity-form" @submit.prevent="submit" class="space-y-4">
                <!-- Tipo -->
                <div class="flex gap-2">
                    <button
                        v-for="t in types"
                        :key="t.value"
                        type="button"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg border text-sm font-medium transition-colors"
                        :class="form.type === t.value
                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400'
                            : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600'"
                        @click="form.type = t.value as 'activity' | 'exam'"
                    >
                        <i :class="t.icon" />
                        {{ t.label }}
                    </button>
                </div>

                <FormField label="Título" :error="form.errors.title" required>
                    <InputText v-model="form.title" class="w-full" autofocus :placeholder="form.type === 'exam' ? 'Ex: Prova Bimestral 1' : 'Ex: Lista de Exercícios 1'" />
                </FormField>

                <div class="grid grid-cols-2 gap-4">
                    <FormField label="Bimestre" :error="form.errors.quarter" required>
                        <Select v-model="form.quarter" :options="quarters" option-label="label" option-value="value" class="w-full" />
                    </FormField>
                    <FormField label="Nota Máxima" :error="form.errors.max_grade" required>
                        <InputNumber v-model="form.max_grade" class="w-full" fluid :min="0" :max="100" :max-fraction-digits="2" />
                    </FormField>
                </div>

                <FormField
                    :label="form.type === 'exam' ? 'Data de Realização' : 'Data de Entrega'"
                    :error="form.errors.due_date"
                    :hint="form.type === 'exam' ? 'Quando a prova será aplicada.' : 'Prazo para entrega da atividade.'"
                >
                    <DatePicker v-model="form.due_date" date-format="dd/mm/yy" class="w-full" show-button-bar />
                </FormField>

                <FormField label="Descrição" :error="form.errors.description">
                    <Textarea v-model="form.description" class="w-full" rows="2" :placeholder="form.type === 'exam' ? 'Conteúdo cobrado, instruções...' : 'Enunciado, instruções...'" auto-resize />
                </FormField>
            </form>

            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button
                    type="submit"
                    form="activity-form"
                    :label="editingId ? 'Salvar alterações' : (form.type === 'exam' ? 'Criar prova' : 'Criar atividade')"
                    icon="pi pi-check"
                    :loading="form.processing"
                />
            </template>
        </AppDialog>

        <ConfirmDialog />
    </AppLayout>
</template>

<script setup lang="ts">
import AppDialog from '@/Components/AppDialog.vue';
import FormField from '@/Components/FormField.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { teacherNav } from '@/nav';
import { formatDate } from '@/utils';
import dayjs from 'dayjs';
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import { useConfirm } from 'primevue/useconfirm';
import { computed, ref } from 'vue';

type Activity = {
    id: number; title: string; type: 'activity' | 'exam'; quarter: number;
    description: string | null; due_date: string | null; max_grade: number; grades_count: number;
};

const props = defineProps<{
    subject: { id: number; name: string; classroom: { id: number; name: string; school_year: number } };
    activities: Activity[];
}>();

const navItems = teacherNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);

const types = [
    { label: 'Atividade', value: 'activity', icon: 'pi pi-paperclip' },
    { label: 'Prova', value: 'exam', icon: 'pi pi-file-edit' },
];

const quarters = [1, 2, 3, 4].map(q => ({ label: `${q}º Bimestre`, value: q }));

const byQuarter = computed(() =>
    props.activities.reduce((acc, a) => {
        (acc[a.quarter] ??= []).push(a);
        return acc;
    }, {} as Record<number, Activity[]>)
);

const form = useForm({
    title: '',
    type: 'activity' as 'activity' | 'exam',
    quarter: 1,
    description: '',
    due_date: null as Date | null,
    max_grade: 10,
});

function openCreate() {
    form.reset();
    editingId.value = null;
    dialogVisible.value = true;
}

function openEdit(a: Activity) {
    form.title = a.title;
    form.type = a.type;
    form.quarter = a.quarter;
    form.description = a.description ?? '';
    form.due_date = a.due_date ? new Date(a.due_date + 'T12:00:00') : null;
    form.max_grade = a.max_grade;
    editingId.value = a.id;
    dialogVisible.value = true;
}

function submit() {
    const payload = {
        ...form.data(),
        due_date: form.due_date ? dayjs(form.due_date).format('YYYY-MM-DD') : null,
    };
    const opts = { onSuccess: () => { dialogVisible.value = false; } };

    editingId.value
        ? form.transform(() => payload).put(route('teacher.activities.update', editingId.value!), opts)
        : form.transform(() => payload).post(route('teacher.subjects.activities.store', props.subject.id), opts);
}

function confirmDelete(a: Activity) {
    confirm.require({
        message: `Remover "${a.title}"? As notas associadas também serão excluídas.`,
        header: `Remover ${a.type === 'exam' ? 'prova' : 'atividade'}`,
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('teacher.activities.destroy', a.id)),
    });
}
</script>
