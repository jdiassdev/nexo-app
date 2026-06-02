<template>
    <AppLayout title="Professores" :nav-items="navItems">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-slate-800">Gerenciar Professores</h2>
                <p class="text-sm text-slate-400 mt-0.5">Acesso e vínculo de professores às disciplinas</p>
            </div>
            <Button label="Novo Professor" icon="pi pi-plus" @click="openCreate" />
        </div>

        <div class="page-card">

            <!-- Filter toolbar -->
            <div class="px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="relative flex-1 max-w-xs">
                        <i class="pi pi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none" />
                        <InputText
                            v-model="search"
                            placeholder="Buscar por nome…"
                            class="w-full !pl-9 text-sm"
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    <Button label="Buscar" icon="pi pi-search" size="small" @click="applyFilters" />
                    <button
                        v-if="search"
                        class="flex items-center gap-1.5 text-xs text-slate-400 hover:text-slate-600 transition-colors ml-1"
                        @click="clearFilters"
                    >
                        <i class="pi pi-times-circle" /> Limpar
                    </button>
                    <span class="ml-auto text-xs text-slate-400">
                        {{ teachers.length }} {{ teachers.length === 1 ? 'professor' : 'professores' }}
                    </span>
                </div>
            </div>

            <!-- Table -->
            <DataTable :value="teachers" class="text-sm">
                <Column header="Professor" field="name">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-8 h-8 rounded-full bg-violet-50 flex items-center justify-center shrink-0">
                                <span class="text-xs font-semibold text-violet-600">{{ data.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 leading-tight">{{ data.name }}</p>
                                <p class="text-xs text-slate-400 leading-tight mt-0.5">{{ data.email }}</p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Disciplinas" style="width:130px">
                    <template #body="{ data }">
                        <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium"
                            :class="data.subjects_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400'"
                        >
                            <i class="pi pi-book text-[10px]" />
                            {{ data.subjects_count }} {{ data.subjects_count === 1 ? 'disciplina' : 'disciplinas' }}
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
                            <i class="pi pi-user text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600">Nenhum professor encontrado</p>
                        <p class="text-xs text-slate-400 mt-1">{{ search ? 'Tente ajustar o filtro de busca.' : 'Clique em "Novo Professor" para começar.' }}</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Dialog -->
        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? 'Editar Professor' : 'Novo Professor'"
            :subtitle="editingId ? 'Atualize os dados do professor.' : 'Crie o acesso para um novo professor.'"
            icon="pi pi-user"
            width="440px"
        >
            <form id="teacher-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome completo" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: Maria Souza" />
                </FormField>
                <FormField label="E-mail" :error="form.errors.email" required hint="Usado para fazer login no sistema.">
                    <InputText v-model="form.email" type="email" class="w-full" placeholder="professor@escola.com" />
                </FormField>
                <FormField
                    :label="editingId ? 'Nova senha' : 'Senha'"
                    :error="form.errors.password"
                    :hint="editingId ? 'Deixe em branco para manter a senha atual.' : 'Mínimo 8 caracteres.'"
                    :required="!editingId"
                >
                    <Password v-model="form.password" class="w-full" :feedback="false" toggle-mask input-class="w-full" placeholder="••••••••" />
                </FormField>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="teacher-form" :label="editingId ? 'Salvar alterações' : 'Criar professor'" icon="pi pi-check" :loading="form.processing" />
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
import { useConfirm } from 'primevue/useconfirm';
import { ref } from 'vue';

defineProps<{
    teachers: { id: number; name: string; email: string; subjects_count: number }[];
    filters: { search?: string };
}>();

const navItems = directorNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);
const form = useForm({ name: '', email: '', password: '' });
const search = ref('');

function applyFilters() {
    router.get(route('director.teachers.index'), {
        search: search.value || undefined,
    }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value = '';
    router.get(route('director.teachers.index'), {}, { preserveState: true, replace: true });
}

function openCreate() { form.reset(); editingId.value = null; dialogVisible.value = true; }
function openEdit(t: { id: number; name: string; email: string }) {
    form.name = t.name; form.email = t.email; form.password = ''; editingId.value = t.id; dialogVisible.value = true;
}

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('director.teachers.update', editingId.value), opts)
        : form.post(route('director.teachers.store'), opts);
}

function confirmDelete(t: { id: number; name: string }) {
    confirm.require({
        message: `Remover o professor "${t.name}"?`,
        header: 'Remover professor',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('director.teachers.destroy', t.id)),
    });
}
</script>
