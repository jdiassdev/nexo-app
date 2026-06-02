<template>
    <Head title="Nexo Escolar — Gestão escolar simples e eficiente" />

    <div class="min-h-screen bg-white antialiased">

        <!-- Nav -->
        <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-xl font-bold text-indigo-600 tracking-tight">Nexo</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-[0.2em]">Escolar</span>
                </div>
                <nav class="hidden md:flex items-center gap-8 text-sm text-slate-500">
                    <a href="#funcionalidades" class="hover:text-slate-800 transition-colors">Funcionalidades</a>
                    <a href="#perfis" class="hover:text-slate-800 transition-colors">Perfis</a>
                    <a href="#como-funciona" class="hover:text-slate-800 transition-colors">Como funciona</a>
                </nav>
                <Link :href="route('login')" class="flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <i class="pi pi-sign-in text-xs" /> Entrar
                </Link>
            </div>
        </header>

        <!-- Hero -->
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[900px] h-[600px] bg-indigo-50 rounded-full opacity-50 blur-3xl" />
            </div>

            <div class="relative max-w-4xl mx-auto px-6 pt-24 pb-16 text-center">
                <span class="inline-flex items-center gap-2 bg-white border border-indigo-100 text-indigo-600 text-xs font-semibold px-4 py-1.5 rounded-full shadow-sm mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse" />
                    Plataforma de gestão escolar
                </span>

                <h1 class="text-[56px] leading-[1.1] font-bold text-slate-900 tracking-tight">
                    Gestão escolar<br/>
                    <span class="text-indigo-600">simples e eficiente</span>
                </h1>

                <p class="mt-6 text-xl text-slate-500 leading-relaxed max-w-2xl mx-auto">
                    Notas, frequência, turmas e comunicação — tudo organizado para diretores, professores e alunos em um só lugar.
                </p>

                <div class="mt-10 flex items-center justify-center gap-3 flex-wrap">
                    <Link :href="route('login')" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-7 py-3.5 rounded-xl transition-colors text-sm shadow-lg shadow-indigo-200/60">
                        Acessar o sistema <i class="pi pi-arrow-right text-xs" />
                    </Link>
                    <a href="#funcionalidades" class="inline-flex items-center gap-2 bg-white border border-slate-200 hover:border-slate-300 text-slate-600 hover:text-slate-800 font-medium px-7 py-3.5 rounded-xl transition-colors text-sm">
                        Ver funcionalidades
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="relative max-w-3xl mx-auto px-6 pb-20">
                <div class="grid grid-cols-4 gap-4">
                    <div v-for="s in heroStats" :key="s.label" class="bg-white border border-slate-200 rounded-2xl p-5 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-9 h-9 rounded-xl mx-auto mb-3 flex items-center justify-center" :class="s.bg">
                            <i :class="[s.icon, s.color, 'text-sm']" />
                        </div>
                        <p class="text-2xl font-bold text-slate-800">{{ s.value }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ s.label }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Divider -->
        <div class="max-w-6xl mx-auto px-6">
            <div class="border-t border-slate-100" />
        </div>

        <!-- Features -->
        <section id="funcionalidades" class="py-24 bg-white">
            <div class="max-w-6xl mx-auto px-6">
                <div class="max-w-2xl mx-auto text-center mb-16">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-4">Funcionalidades</p>
                    <h2 class="text-4xl font-bold text-slate-900 tracking-tight leading-tight">
                        Tudo que uma escola precisa
                    </h2>
                    <p class="mt-4 text-slate-500 text-lg leading-relaxed">
                        Do cadastro de turmas ao cálculo de médias, cada fluxo foi pensado para funcionar sem complicação.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="feat in features"
                        :key="feat.title"
                        class="group bg-white border border-slate-200 rounded-2xl p-7 hover:border-indigo-200 hover:shadow-lg hover:shadow-indigo-50 transition-all duration-200"
                    >
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-5" :class="feat.bg">
                            <i :class="[feat.icon, feat.color, 'text-base']" />
                        </div>
                        <h3 class="font-semibold text-slate-800 mb-2 text-[15px]">{{ feat.title }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ feat.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profiles -->
        <section id="perfis" class="py-24 bg-slate-50">
            <div class="max-w-6xl mx-auto px-6">
                <div class="max-w-2xl mx-auto text-center mb-16">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-4">Perfis de acesso</p>
                    <h2 class="text-4xl font-bold text-slate-900 tracking-tight leading-tight">
                        Feito para cada papel na escola
                    </h2>
                    <p class="mt-4 text-slate-500 text-lg">
                        Cada usuário acessa exatamente o que precisa.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div
                        v-for="role in roles"
                        :key="role.title"
                        class="rounded-2xl border p-8 relative overflow-hidden"
                        :class="role.cardClass"
                    >
                        <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full opacity-[0.07]" :class="role.circle" />
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center mb-6" :class="role.iconBg">
                            <i :class="[role.icon, role.iconColor, 'text-lg']" />
                        </div>
                        <h3 class="text-lg font-bold mb-2" :class="role.title2">{{ role.title }}</h3>
                        <p class="text-sm mb-6 leading-relaxed" :class="role.desc">{{ role.description }}</p>
                        <ul class="space-y-3">
                            <li v-for="item in role.features" :key="item" class="flex items-start gap-2.5 text-sm" :class="role.desc">
                                <i class="pi pi-check-circle text-xs mt-0.5 shrink-0 text-indigo-500" />
                                {{ item }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section id="como-funciona" class="py-24 bg-white">
            <div class="max-w-5xl mx-auto px-6">
                <div class="max-w-2xl mx-auto text-center mb-16">
                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-4">Como funciona</p>
                    <h2 class="text-4xl font-bold text-slate-900 tracking-tight leading-tight">
                        Simples do início ao fim
                    </h2>
                </div>

                <div class="grid md:grid-cols-3 gap-10">
                    <div v-for="(step, i) in steps" :key="step.title" class="relative text-center">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white text-xl font-bold flex items-center justify-center mx-auto mb-6 shadow-lg shadow-indigo-200">
                            {{ i + 1 }}
                        </div>
                        <h3 class="font-semibold text-slate-800 mb-3 text-[15px]">{{ step.title }}</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ step.description }}</p>
                        <!-- Connector -->
                        <div v-if="i < 2" class="hidden md:block absolute top-7 left-[calc(50%+3rem)] w-[calc(100%-6rem)] h-px bg-slate-200" />
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="py-16 px-6">
            <div class="max-w-3xl mx-auto">
                <div class="bg-indigo-600 rounded-3xl px-10 py-16 text-center relative overflow-hidden">
                    <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full bg-white/5" />
                    <div class="absolute -bottom-20 -left-20 w-64 h-64 rounded-full bg-white/5" />
                    <div class="relative">
                        <h2 class="text-3xl font-bold text-white mb-4">Pronto para começar?</h2>
                        <p class="text-indigo-200 mb-10 max-w-sm mx-auto leading-relaxed">
                            Acesse o sistema e gerencie sua escola de forma simples e eficiente.
                        </p>
                        <Link :href="route('login')" class="inline-flex items-center gap-2 bg-white text-indigo-600 hover:bg-indigo-50 font-semibold px-8 py-3.5 rounded-xl transition-colors text-sm">
                            Acessar o sistema <i class="pi pi-arrow-right" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-100 py-8">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-indigo-600">Nexo</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-[0.2em]">Escolar</span>
                </div>
                <p class="text-xs text-slate-400">© {{ new Date().getFullYear() }} Nexo Escolar. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>
</template>

<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

const heroStats = [
    { label: 'Turmas ilimitadas', value: '∞', icon: 'pi pi-objects-column', bg: 'bg-indigo-50', color: 'text-indigo-600' },
    { label: 'Bimestres', value: '4', icon: 'pi pi-calendar', bg: 'bg-blue-50', color: 'text-blue-600' },
    { label: 'Perfis de acesso', value: '3', icon: 'pi pi-users', bg: 'bg-violet-50', color: 'text-violet-600' },
    { label: 'Média automática', value: '✓', icon: 'pi pi-calculator', bg: 'bg-green-50', color: 'text-green-600' },
];

const features = [
    {
        title: 'Turmas por ano letivo',
        description: 'Crie turmas com disciplinas, professores e horários semanais. Controle total por ano letivo.',
        icon: 'pi pi-objects-column', bg: 'bg-indigo-50', color: 'text-indigo-600',
    },
    {
        title: 'Notas e médias automáticas',
        description: 'Lance atividades e provas por bimestre. O sistema calcula médias semestrais e nota final.',
        icon: 'pi pi-star', bg: 'bg-amber-50', color: 'text-amber-600',
    },
    {
        title: 'Controle de frequência',
        description: 'Registre presença por data e disciplina. Alerta automático ao atingir o limite de faltas.',
        icon: 'pi pi-calendar-clock', bg: 'bg-green-50', color: 'text-green-600',
    },
    {
        title: 'Recuperações e prova final',
        description: 'Suporte completo a recuperações semestrais e prova final com recálculo automático.',
        icon: 'pi pi-refresh', bg: 'bg-blue-50', color: 'text-blue-600',
    },
    {
        title: 'Horários por disciplina',
        description: 'Configure dias e horários de início e fim para cada disciplina de cada turma.',
        icon: 'pi pi-clock', bg: 'bg-violet-50', color: 'text-violet-600',
    },
    {
        title: 'Acesso por matrícula ou e-mail',
        description: 'Alunos entram com matrícula. Professores e diretores com e-mail. Login único e seguro.',
        icon: 'pi pi-lock', bg: 'bg-rose-50', color: 'text-rose-600',
    },
];

const roles = [
    {
        title: 'Diretor',
        description: 'Visão completa e controle total da escola.',
        icon: 'pi pi-shield',
        cardClass: 'border-indigo-200 bg-indigo-50',
        circle: 'bg-indigo-600',
        iconBg: 'bg-indigo-100',
        iconColor: 'text-indigo-700',
        title2: 'text-indigo-900',
        desc: 'text-indigo-700',
        features: ['Gerenciar professores e alunos', 'Criar turmas e disciplinas', 'Definir horários', 'Dashboard com indicadores'],
    },
    {
        title: 'Professor',
        description: 'Gestão das suas disciplinas com foco no pedagógico.',
        icon: 'pi pi-user',
        cardClass: 'border-slate-200 bg-white',
        circle: 'bg-slate-600',
        iconBg: 'bg-slate-100',
        iconColor: 'text-slate-700',
        title2: 'text-slate-900',
        desc: 'text-slate-600',
        features: ['Criar atividades e provas', 'Lançar notas por bimestre', 'Registrar presença', 'Acompanhar a turma'],
    },
    {
        title: 'Aluno',
        description: 'Acompanhamento do próprio desempenho acadêmico.',
        icon: 'pi pi-graduation-cap',
        cardClass: 'border-slate-200 bg-white',
        circle: 'bg-slate-600',
        iconBg: 'bg-slate-100',
        iconColor: 'text-slate-700',
        title2: 'text-slate-900',
        desc: 'text-slate-600',
        features: ['Ver notas por bimestre', 'Histórico de presenças', 'Situação: aprovado/reprovado', 'Horários das disciplinas'],
    },
];

const steps = [
    {
        title: 'Diretor configura a escola',
        description: 'Cadastra professores, cria turmas, define disciplinas com horários e matricula alunos.',
    },
    {
        title: 'Professor gerencia suas aulas',
        description: 'Lança atividades, registra notas e controla a frequência dos seus alunos por data.',
    },
    {
        title: 'Aluno acompanha o progresso',
        description: 'Acessa notas, médias, histórico de faltas e situação de aprovação em tempo real.',
    },
];
</script>
