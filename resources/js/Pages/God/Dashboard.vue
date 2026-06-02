<template>
    <AppLayout title="Dashboard" :nav-items="navItems">

        <div class="mb-6">
            <h2 class="text-base font-semibold text-slate-800">Visão Geral do Sistema</h2>
            <p class="text-sm text-slate-400 mt-0.5">Panorama completo de todas as escolas e usuários</p>
        </div>

        <div class="grid grid-cols-2 gap-4 lg:grid-cols-5 mb-8">
            <div v-for="card in statCards" :key="card.label" class="stat-card">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="card.bg">
                        <i :class="[card.icon, card.color, 'text-base']" />
                    </div>
                    <p class="text-xs text-slate-400 font-medium">{{ card.label }}</p>
                </div>
                <p class="text-3xl font-bold text-slate-800 tracking-tight">{{ card.value }}</p>
            </div>
        </div>

        <div class="page-card">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Escolas cadastradas</h3>
            </div>
            <DataTable :value="schools" class="text-sm">
                <Column header="Escola" field="name">
                    <template #body="{ data }">
                        <div class="flex items-center gap-3 py-0.5">
                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
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
                        <CountBadge :value="data.classrooms_count" icon="pi pi-objects-column" color="blue" />
                    </template>
                </Column>
                <Column header="Professores" style="width:110px">
                    <template #body="{ data }">
                        <CountBadge :value="data.teachers_count" icon="pi pi-user" color="violet" />
                    </template>
                </Column>
                <Column header="Alunos" style="width:90px">
                    <template #body="{ data }">
                        <CountBadge :value="data.students_count" icon="pi pi-users" color="indigo" />
                    </template>
                </Column>
                <template #empty>
                    <EmptyState icon="pi pi-building" message="Nenhuma escola cadastrada." />
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { godNav } from '@/nav';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { computed, defineComponent, h } from 'vue';

const props = defineProps<{
    stats: { schools: number; directors: number; teachers: number; students: number; classrooms: number };
    schools: { id: number; name: string; city: string | null; classrooms_count: number; teachers_count: number; students_count: number }[];
}>();

const navItems = godNav;

const statCards = computed(() => [
    { label: 'Escolas', value: props.stats.schools, icon: 'pi pi-building', color: 'text-slate-600', bg: 'bg-slate-100' },
    { label: 'Turmas', value: props.stats.classrooms, icon: 'pi pi-objects-column', color: 'text-blue-600', bg: 'bg-blue-50' },
    { label: 'Diretores', value: props.stats.directors, icon: 'pi pi-shield', color: 'text-indigo-600', bg: 'bg-indigo-50' },
    { label: 'Professores', value: props.stats.teachers, icon: 'pi pi-user', color: 'text-violet-600', bg: 'bg-violet-50' },
    { label: 'Alunos', value: props.stats.students, icon: 'pi pi-users', color: 'text-emerald-600', bg: 'bg-emerald-50' },
]);

const colorMap: Record<string, string> = {
    blue: 'bg-blue-50 text-blue-700',
    violet: 'bg-violet-50 text-violet-700',
    indigo: 'bg-indigo-50 text-indigo-700',
    emerald: 'bg-emerald-50 text-emerald-700',
};

const CountBadge = defineComponent({
    props: { value: Number, icon: String, color: String },
    setup(p) {
        return () => h('span', {
            class: `inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium ${colorMap[p.color ?? 'indigo']}`,
        }, [
            h('i', { class: `${p.icon} text-[10px]` }),
            String(p.value ?? 0),
        ]);
    },
});

const EmptyState = defineComponent({
    props: { icon: String, message: String },
    setup(p) {
        return () => h('div', { class: 'flex flex-col items-center justify-center py-10 text-center' }, [
            h('div', { class: 'w-10 h-10 rounded-2xl bg-slate-100 flex items-center justify-center mb-2' }, [
                h('i', { class: `${p.icon} text-slate-400` }),
            ]),
            h('p', { class: 'text-sm text-slate-400' }, p.message),
        ]);
    },
});
</script>
