<template>
    <AppLayout title="Usuários" :nav-items="navItems">

        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Usuários</h2>
                <p class="text-sm text-slate-400 mt-0.5">Gerencie diretores, professores e alunos do sistema</p>
            </div>
            <Button :label="`Novo ${roleLabel}`" icon="pi pi-plus" @click="openCreate" />
        </div>

        <!-- Role tabs -->
        <div class="flex gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 mb-5 w-fit">
            <button
                v-for="tab in tabs"
                :key="tab.value"
                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all"
                :class="activeRole === tab.value
                    ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm'
                    : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                @click="switchRole(tab.value)"
            >
                {{ tab.label }}
            </button>
        </div>

        <div class="page-card">
            <DataTable :value="users.data" class="text-sm">
                <Column header="Usuário">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" :class="avatarBg(data.role)">
                                <span class="text-xs font-semibold" :class="avatarText(data.role)">{{ data.name.charAt(0).toUpperCase() }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-slate-100 leading-tight">{{ data.name }}</p>
                                <p class="text-xs text-slate-400 leading-tight mt-0.5">
                                    <span v-if="data.email">{{ data.email }}</span>
                                    <span v-else-if="data.enrollment" class="font-mono">Mat. {{ data.enrollment }}</span>
                                    <span v-else>—</span>
                                </p>
                            </div>
                        </div>
                    </template>
                </Column>
                <Column header="Escola">
                    <template #body="{ data }">
                        <span v-if="data.school" class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                            <i class="pi pi-building text-[10px]" />{{ data.school.name }}
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
                            <i class="pi pi-users text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nenhum {{ roleLabel.toLowerCase() }} encontrado</p>
                    </div>
                </template>
            </DataTable>
            <Pagination :data="users" />
        </div>

        <AppDialog
            v-model="dialogVisible"
            :title="editingId ? `Editar ${roleLabel}` : `Novo ${roleLabel}`"
            :subtitle="editingId ? 'Atualize os dados do usuário.' : 'Preencha os dados para criar o acesso.'"
            :icon="editingId ? 'pi pi-pencil' : 'pi pi-user-plus'"
            width="480px"
        >
            <form id="user-form" @submit.prevent="submit" class="space-y-4">
                <FormField label="Nome completo" :error="form.errors.name" required>
                    <InputText v-model="form.name" class="w-full" autofocus placeholder="Ex: João da Silva" />
                </FormField>

                <FormField v-if="activeRole !== 'student'" label="E-mail" :error="form.errors.email" required hint="Usado para fazer login no sistema.">
                    <InputText v-model="form.email" type="email" class="w-full" placeholder="email@escola.com" />
                </FormField>

                <FormField label="Escola" :error="form.errors.school_id" required>
                    <Select v-model="form.school_id" :options="schools" option-label="name" option-value="id" placeholder="Selecione a escola" class="w-full" />
                </FormField>

                <FormField
                    :label="editingId ? 'Nova senha' : 'Senha'"
                    :error="form.errors.password"
                    :hint="editingId ? 'Deixe em branco para manter a senha atual.' : 'Mínimo 8 caracteres.'"
                    :required="!editingId"
                >
                    <Password v-model="form.password" class="w-full" :feedback="false" toggle-mask input-class="w-full" placeholder="••••••••" />
                </FormField>

                <div v-if="activeRole === 'student'" class="flex items-center gap-2 bg-slate-50 dark:bg-slate-800 rounded-lg px-3 py-2.5 text-xs text-slate-500 dark:text-slate-400">
                    <i class="pi pi-info-circle text-slate-400" />
                    A matrícula será gerada automaticamente pelo sistema.
                </div>
            </form>
            <template #footer>
                <Button type="button" label="Cancelar" severity="secondary" text @click="dialogVisible = false" />
                <Button type="submit" form="user-form" :label="editingId ? 'Salvar alterações' : 'Criar usuário'" icon="pi pi-check" :loading="form.processing" />
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
import { godNav } from '@/nav';
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

type User = { id: number; school_id: number | null; name: string; email: string | null; enrollment: string | null; school: { name: string } | null; role: string };
type Paginated<T> = { data: T[]; current_page: number; last_page: number; from: number; to: number; total: number; links: { url: string | null; label: string; active: boolean }[] };

const props = defineProps<{
    users: Paginated<User>;
    role: string;
    schools: { id: number; name: string }[];
}>();

const navItems = godNav;
const confirm = useConfirm();
const dialogVisible = ref(false);
const editingId = ref<number | null>(null);
const activeRole = ref(props.role);

const tabs = [
    { label: 'Diretores', value: 'director' },
    { label: 'Professores', value: 'teacher' },
    { label: 'Alunos', value: 'student' },
];

const roleLabelMap: Record<string, string> = { director: 'Diretor', teacher: 'Professor', student: 'Aluno' };
const roleLabel = computed(() => roleLabelMap[activeRole.value] ?? activeRole.value);

const avatarColors: Record<string, [string, string]> = {
    director: ['bg-indigo-50 dark:bg-indigo-900/30', 'text-indigo-600 dark:text-indigo-400'],
    teacher: ['bg-violet-50 dark:bg-violet-900/30', 'text-violet-600 dark:text-violet-400'],
    student: ['bg-emerald-50 dark:bg-emerald-900/30', 'text-emerald-600 dark:text-emerald-400'],
};
function avatarBg(role: string) { return avatarColors[role]?.[0] ?? 'bg-slate-100 dark:bg-slate-800'; }
function avatarText(role: string) { return avatarColors[role]?.[1] ?? 'text-slate-500 dark:text-slate-400'; }

const form = useForm({ name: '', email: '', password: '', school_id: null as number | null, role: props.role });

function switchRole(role: string) {
    activeRole.value = role;
    router.get(route('god.users.index'), { role }, { preserveState: false });
}

function openCreate() { form.reset(); form.role = activeRole.value; editingId.value = null; dialogVisible.value = true; }

function openEdit(u: User) {
    form.name = u.name;
    form.email = u.email ?? '';
    form.password = '';
    form.school_id = u.school_id;
    form.role = u.role;
    editingId.value = u.id;
    dialogVisible.value = true;
}

function submit() {
    const opts = { onSuccess: () => { dialogVisible.value = false; } };
    editingId.value
        ? form.put(route('god.users.update', editingId.value), opts)
        : form.post(route('god.users.store'), opts);
}

function confirmDelete(u: User) {
    confirm.require({
        message: `Remover "${u.name}"? Esta ação não pode ser desfeita.`,
        header: 'Remover usuário',
        icon: 'pi pi-exclamation-triangle',
        acceptLabel: 'Sim, remover',
        rejectLabel: 'Cancelar',
        acceptClass: 'p-button-danger',
        accept: () => form.delete(route('god.users.destroy', u.id)),
    });
}
</script>
