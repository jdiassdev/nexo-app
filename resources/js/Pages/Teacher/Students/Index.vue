<template>
    <AppLayout title="Alunos" :nav-items="navItems">

        <div class="mb-6">
            <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">Meus Alunos</h2>
            <p class="text-sm text-slate-400 mt-0.5">Alunos das turmas em que você leciona</p>
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
                        <span v-else class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                    </template>
                </Column>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                            <i class="pi pi-users text-slate-400 text-lg" />
                        </div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-300">Nenhum aluno encontrado</p>
                        <p class="text-xs text-slate-400 mt-1">{{ hasFilters ? 'Tente ajustar os filtros de busca.' : 'Nenhum aluno está matriculado nas suas turmas.' }}</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { teacherNav } from '@/nav';
import { router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import { computed, ref } from 'vue';

const props = defineProps<{
    students: { id: number; name: string; enrollment: string; classrooms: { id: number; name: string; school_year: number }[] }[];
    classrooms: { id: number; name: string; school_year: number }[];
    filters: { search?: string; classroom_id?: string | number };
}>();

const navItems = teacherNav;
const search = ref(props.filters.search ?? '');
const classroomId = ref<number | null>(props.filters.classroom_id ? Number(props.filters.classroom_id) : null);
const hasFilters = computed(() => !!search.value || !!classroomId.value);

function applyFilters() {
    router.get(route('teacher.students.index'), {
        search: search.value || undefined,
        classroom_id: classroomId.value || undefined,
    }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value = '';
    classroomId.value = null;
    router.get(route('teacher.students.index'), {}, { preserveState: true, replace: true });
}
</script>
