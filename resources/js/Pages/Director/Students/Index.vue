<template>
    <AppLayout title="Alunos" :nav-items="navItems">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Gerenciar Alunos</h2>
                <p class="text-sm text-slate-400 mt-0.5">Cadastro e matrícula de alunos da escola</p>
            </div>
            <Button label="Novo Aluno" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div class="page-card">

            <!-- Filter toolbar -->
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex-1 max-w-xs">
                        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none" />
                        <InputText
                            v-model="search"
                            placeholder="Nome ou matrícula…"
                            class="w-full !pl-9 text-sm"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <Select
                        v-model="classroomId"
                        :options="classrooms"
                        :option-label="(c) => `${c.name} · ${c.school_year}`"
                        option-value="id"
                        placeholder="Todas as turmas"
                        show-clear
                        class="w-52 text-sm"
                    />
                    <Button label="Buscar" icon="pi pi-search" size="small" @click="applyFilters" />
                    <button
                        v-if="hasFilters"
                        class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors ml-1"
                        @click="clearFilters"
                    >
                        <i class="pi pi-times-circle" /> Limpar
                    </button>
                    <span class="ml-auto text-xs text-slate-400">
                        {{ students.length }} {{ students.length === 1 ? 'aluno' : 'alunos' }}
                    </span>
                </div>
            </div>

            <!-- Table -->
            <DataTable :value="students" class="text-sm">
                <Column header="Aluno" field="name">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">{{ data.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <span class="font-medium text-slate-800 dark:text-slate-100">{{ data.name }}</span>
                        </div>
                    </template>
                </Column>
                <Column header="Matrícula" style="width:140px">
                    <template #body="{ data }">
                        <span class="font-mono text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-2 py-1 rounded-md tracking-wide">
                            {{ data.enrollment }}
                        </span>
                    </template>
                </Column>
                <Column header="Turma" style="width:200px">
                    <template #body="{ data }">
                        <span v-if="data.classrooms?.[0]" class="inline-flex items-center gap-1.5 text-xs bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 px-2.5 py-1 rounded-full font-medium">
                            <i class="pi pi-objects-column text-[10px]" />
                            {{ data.classrooms[0].name }} · {{ data.classrooms[0].school_year }}
                        </span>
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">Sem turma</span>
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
                            <i class="pi pi-users text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nenhum aluno encontrado</p>
                        <p class="text-xs text-slate-400 mt-1">{{ hasFilters ? 'Tente ajustar os filtros de busca.' : 'Clique em "Novo Aluno" para começar.' }}</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Aluno' : 'Novo Aluno'"
            :subtitle="editingId ? 'Atualize os dados do aluno.' : 'Preencha os dados para cadastrar um novo aluno.'"
            icon="pi pi-user-plus"
            width="460px"
        >
            <form id="student-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome completo" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: Pedro Alves" />
                </FormField>
                <FormField label="Turma" :error="form.errors.classroom_id">
                    <Select
                        v-model="form.classroom_id"
                        :options="classrooms"
                        :option-label="(c) => `${c.name} (${c.school_year})`"
                        option-value="id"
                        placeholder="Selecione uma turma"
                        show-clear
                        class="w-full"
                    />
                </FormField>
                <FormField
                    :label="editingId ? 'Nova senha' : 'Senha'"
                    :error="form.errors.password"
                    :hint="editingId ? 'Deixe em branco para manter a senha atual.' : 'Mínimo 8 caracteres.'"
                    :required="!editingId"
                >
                    <Password v-model="form.password" class="w-full" :feedback="false" toggle-mask input-class="w-full" placeholder="••••••••" />
                </FormField>
                <div v-if="editingId" class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2.5 text-xs text-slate-500 dark:text-slate-400">
                    <i class="pi pi-info-circle text-slate-400" />
                    A matrícula é gerada automaticamente e não pode ser alterada.
                </div>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="student-form" :label="editingId ? 'Salvar alterações' : 'Criar aluno'" icon="pi pi-check" :loading="form.processing" />
            </template>
        </AppDialog>

        <ConfirmDialog />
    </AppLayout>
</template>

<script setup lang="ts">
import AppDialog from '@/Components/AppDialog.vue';
import FormField from '@/Components/FormField.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { directorNav } from '@/nav';
import { router, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Select from 'primevue/select';
import { useConfirm } from 'primevue/useconfirm';
import { computed, ref } from 'vue';

type Student = { id: number; name: string; enrollment: string; classrooms: { id: number; name: string; school_year: number }[] };

const props = defineProps<{
    students: Student[];
    classrooms: { id: number; name: string; school_year: number }[];
    filters: { search?: string; classroom_id?: string | number };
}>();

const navItems = directorNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ name: '', password: '', classroom_id: null as number | null });

const search = ref(props.filters.search ?? '');
const classroomId = ref<number | null>(props.filters.classroom_id ? Number(props.filters.classroom_id) : null);
const hasFilters = computed(() => !!search.value || !!classroomId.value);

function applyFilters() {
    router.get(route('director.students.index'), {
        search: search.value || undefined,
        classroom_id: classroomId.value || undefined,
    }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value = '';
    classroomId.value = null;
    router.get(route('director.students.index'), {}, { preserveState: true, replace: true });
}

function openCreate() { form.reset(); editingId.value = null; dialogVisible.value = true; }
function openEdit(s: Student) {
    form.name = s.name;
    form.password = '';
    form.classroom_id = s.classrooms?.[0]?.id ?? null;
    editingId.value = s.id;
    dialogVisible.value = true;
}

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('director.students.update', editingId.value), opts)
        : form.post(route('director.students.store'), opts);
}

function confirmDelete(s: { id: number; name: string }) {
    confirm.require({
        message: `Remover o aluno "${s.name}"? Esta ação não pode ser desfeita.`,
        header: 'Remover aluno',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('director.students.destroy', s.id)),
    });
}
</script>
