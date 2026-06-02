<template>
    <AppLayout title="Escolas" :nav-items="navItems">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Escolas</h2>
                <p class="text-sm text-slate-400 mt-0.5">Gerencie todas as escolas do sistema</p>
            </div>
            <Button label="Nova Escola" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div class="page-card">
            <DataTable :value="schools" class="text-sm">
                <Column header="Escola">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                                <i class="pi pi-building text-slate-500 text-sm" />
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 leading-tight">{{ data.name }}</p>
                                <p class="text-xs text-slate-400 leading-tight mt-0.5">{{ data.city ?? 'Cidade não informada' }}</p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Turmas" style="width:100px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-blue-50 text-blue-700">
                            <i class="pi pi-objects-column text-[10px]" />{{ data.classrooms_count }}
                        </span>
                    </template>
                </Column>
                <Column header="Diretores" style="width:100px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-indigo-50 text-indigo-700">
                            <i class="pi pi-shield text-[10px]" />{{ data.directors_count }}
                        </span>
                    </template>
                </Column>
                <Column header="Professores" style="width:110px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-violet-50 text-violet-700">
                            <i class="pi pi-user text-[10px]" />{{ data.teachers_count }}
                        </span>
                    </template>
                </Column>
                <Column header="Alunos" style="width:90px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium bg-emerald-50 text-emerald-700">
                            <i class="pi pi-users text-[10px]" />{{ data.students_count }}
                        </span>
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
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center mb-3">
                            <i class="pi pi-building text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600">Nenhuma escola cadastrada</p>
                        <p class="text-xs text-slate-400 mt-1">Clique em "Nova Escola" para começar.</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Escola' : 'Nova Escola'"
            :subtitle="editingId ? 'Atualize os dados da escola.' : 'Preencha os dados para cadastrar a escola.'"
            icon="pi pi-building"
        >
            <form id="school-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome da escola" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: Escola Estadual São José" />
                </FormField>
                <FormField label="Cidade" :error="form.errors.city">
                    <InputText v-model="form.city" class="w-full" placeholder="Ex: São Paulo" />
                </FormField>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="school-form" :label="editingId ? 'Salvar alterações' : 'Criar escola'" icon="pi pi-check" :loading="form.processing" />
            </template>
        </AppDialog>

        <ConfirmDialog />
    </AppLayout>
</template>

<script setup lang="ts">
import AppDialog from '@/Components/AppDialog.vue';
import FormField from '@/Components/FormField.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { godNav } from '@/nav';
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import { useConfirm } from 'primevue/useconfirm';
import { ref } from 'vue';

type School = { id: number; name: string; city: string | null; classrooms_count: number; directors_count: number; teachers_count: number; students_count: number };

defineProps<{ schools: School[] }>();

const navItems = godNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ name: '', city: '' });

function openCreate() { form.reset(); editingId.value = null; dialogVisible.value = true; }
function openEdit(s: School) { form.name = s.name; form.city = s.city ?? ''; editingId.value = s.id; dialogVisible.value = true; }

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('god.schools.update', editingId.value), opts)
        : form.post(route('god.schools.store'), opts);
}

function confirmDelete(s: School) {
    confirm.require({
        message: `Remover "${s.name}"? Todos os dados vinculados serão excluídos permanentemente.`,
        header: 'Remover escola',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('god.schools.destroy', s.id)),
    });
}
</script>
