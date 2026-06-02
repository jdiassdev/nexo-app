<template>
    <AppLayout title="Turmas" :nav-items="navItems">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Turmas</h2>
                <p class="text-sm text-slate-400 mt-0.5">Gerencie as turmas e acesse suas disciplinas</p>
            </div>
            <Button label="Nova Turma" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div class="page-card">
            <DataTable :value="classrooms" class="text-sm">
                <Column header="Turma">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                                <i class="pi pi-objects-column text-blue-600 text-sm" />
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 leading-tight">{{ data.name }}</p>
                                <p class="text-xs text-slate-400 leading-tight mt-0.5">Ano letivo {{ data.school_year }}</p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Alunos" style="width:110px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-50 text-emerald-700">
                            <i class="pi pi-users text-[10px]" />{{ data.students_count }}
                        </span>
                    </template>
                </Column>
                <Column header="Disciplinas" style="width:120px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-purple-50 text-purple-700">
                            <i class="pi pi-book text-[10px]" />{{ data.subjects_count }}
                        </span>
                    </template>
                </Column>
                <Column header="" style="width:110px">
                    <template #body="{ data }">
                        <div class="flex gap-0.5 justify-end">
                            <Link :href="route('director.classrooms.subjects.index', data.id)">
                                <Button icon="pi pi-book" size="small" severity="secondary" text rounded v-tooltip.left="'Ver Disciplinas'" />
                            </Link>
                            <Button icon="pi pi-pencil" size="small" severity="secondary" text rounded v-tooltip.left="'Editar'" @click="openEdit(data)" />
                            <Button icon="pi pi-trash" size="small" severity="danger" text rounded v-tooltip.left="'Remover'" @click="confirmDelete(data)" />
                        </div>
                    </template>
                </Column>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                            <i class="pi pi-objects-column text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600">Nenhuma turma cadastrada</p>
                        <p class="text-xs text-slate-400 mt-1">Clique em "Nova Turma" para começar.</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Turma' : 'Nova Turma'"
            :subtitle="editingId ? 'Atualize os dados da turma.' : 'Crie uma nova turma para a escola.'"
            icon="pi pi-objects-column"
            width="420px"
        >
            <form id="classroom-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome da turma" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: 9º Ano A" />
                </FormField>
                <FormField label="Ano letivo" :error="form.errors.school_year" required>
                    <InputNumber v-model="form.school_year" class="w-full" :use-grouping="false" placeholder="Ex: 2026" />
                </FormField>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="classroom-form" :label="editingId ? 'Salvar alterações' : 'Criar turma'" icon="pi pi-check" :loading="form.processing" />
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
import { Link, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputNumber from 'primevue/inputnumber';
import InputText from 'primevue/inputtext';
import { useConfirm } from 'primevue/useconfirm';
import { ref } from 'vue';

defineProps<{
    classrooms: { id: number; name: string; school_year: number; students_count: number; subjects_count: number }[];
}>();

const navItems = directorNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ name: '', school_year: new Date().getFullYear() });

function openCreate() { form.reset(); editingId.value = null; dialogVisible.value = true; }
function openEdit(c: { id: number; name: string; school_year: number }) {
    form.name = c.name; form.school_year = c.school_year; editingId.value = c.id; dialogVisible.value = true;
}

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('director.classrooms.update', editingId.value), opts)
        : form.post(route('director.classrooms.store'), opts);
}

function confirmDelete(c: { id: number; name: string }) {
    confirm.require({
        message: `Remover a turma "${c.name}"? Todas as disciplinas e registros serão excluídos.`,
        header: 'Remover turma',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('director.classrooms.destroy', c.id)),
    });
}
</script>
