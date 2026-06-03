<template>
    <AppLayout :title="`Presenças — ${subject.name}`" :nav-items="navItems">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <Link :href="route('student.dashboard')" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <i class="pi pi-arrow-left text-sm" />
            </Link>
            <div>
                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">{{ subject.name }}</h2>
                <p class="text-sm text-slate-400 mt-0.5">Histórico de presenças e faltas</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-6 lg:grid-cols-4">
            <div class="stat-card flex items-center gap-3" :class="overLimit ? 'ring-1 ring-red-200 dark:ring-red-900' : ''">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0" :class="overLimit ? 'bg-red-50 dark:bg-red-900/30' : 'bg-slate-100 dark:bg-slate-800'">
                    <i class="pi pi-times-circle text-base" :class="overLimit ? 'text-red-500' : 'text-slate-400'" />
                </div>
                <div>
                    <p class="text-xs text-slate-400">Total de Faltas</p>
                    <p class="text-xl font-bold" :class="overLimit ? 'text-red-600' : 'text-slate-800 dark:text-slate-100'">
                        {{ total_absences }}
                        <span v-if="subject.max_absences" class="text-sm font-normal text-slate-400">/ {{ subject.max_absences }}</span>
                    </p>
                </div>
            </div>
            <div class="stat-card flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <i class="pi pi-check-circle text-emerald-500 text-base" />
                </div>
                <div>
                    <p class="text-xs text-slate-400">Presenças</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-slate-100">{{ records.filter(r => r.present).length }}</p>
                </div>
            </div>
            <div v-if="overLimit" class="lg:col-span-2 flex items-center gap-2.5 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-900 rounded-xl px-4 py-3 text-red-700 dark:text-red-400 text-sm">
                <i class="pi pi-exclamation-triangle text-red-500" />
                <span>Limite de faltas atingido. Risco de reprovação por frequência.</span>
            </div>
        </div>

        <!-- Records table -->
        <div class="page-card">
            <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Registros de Frequência</p>
            </div>
            <DataTable :value="records" class="text-sm" :rows="20" paginator>
                <Column header="Data" style="width:160px">
                    <template #body="{ data }">
                        <span class="text-slate-600 dark:text-slate-300">{{ formatDate(data.date) }}</span>
                    </template>
                </Column>
                <Column header="Situação" style="width:140px">
                    <template #body="{ data }">
                        <span
                            class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-medium"
                            :class="data.present ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400'"
                        >
                            <i :class="data.present ? 'pi pi-check-circle' : 'pi pi-times-circle'" class="text-[10px]" />
                            {{ data.present ? 'Presente' : 'Falta' }}
                        </span>
                    </template>
                </Column>
                <template #empty>
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-2">
                            <i class="pi pi-calendar text-slate-400" />
                        </div>
                        <p class="text-sm text-slate-400">Nenhum registro de presença.</p>
                    </div>
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { studentNav } from '@/nav';
import { formatDate } from '@/utils';
import { Link } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import { computed } from 'vue';

const props = defineProps<{
    subject: { id: number; name: string; max_absences: number | null };
    records: { date: string; present: boolean }[];
    total_absences: number;
}>();

const navItems = studentNav;
const overLimit = computed(() => props.subject.max_absences !== null && props.total_absences >= props.subject.max_absences);
</script>
