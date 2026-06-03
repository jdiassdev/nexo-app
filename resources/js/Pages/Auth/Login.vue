<template>
    <Head title="Entrar — Nexo Escolar" />

    <div class="min-h-screen flex">
        <!-- Left panel (indigo — não muda no dark) -->
        <div class="hidden lg:flex lg:w-1/2 bg-indigo-600 flex-col justify-between p-12 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white" />
                <div class="absolute -bottom-32 -right-16 w-[500px] h-[500px] rounded-full bg-white" />
                <div class="absolute top-1/2 left-1/3 w-64 h-64 rounded-full bg-white" />
            </div>

            <div class="relative">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-2xl font-bold text-white tracking-tight">Nexo</span>
                    <span class="text-xs text-indigo-200 font-medium uppercase tracking-widest mt-1">Escolar</span>
                </div>
                <p class="text-indigo-200 text-sm">Gestão escolar simples e eficiente</p>
            </div>

            <div class="relative space-y-8">
                <div>
                    <h1 class="text-3xl font-bold text-white leading-tight">
                        Tudo que sua escola precisa,<br/>em um só lugar.
                    </h1>
                    <p class="mt-3 text-indigo-200 text-base leading-relaxed">
                        Notas, frequência, turmas e comunicação — organizados para diretores, professores e alunos.
                    </p>
                </div>

                <div class="space-y-4">
                    <div v-for="item in features" :key="item.text" class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <i :class="[item.icon, 'text-white text-sm']" />
                        </div>
                        <span class="text-indigo-100 text-sm">{{ item.text }}</span>
                    </div>
                </div>
            </div>

            <div class="relative">
                <p class="text-indigo-300 text-xs">© {{ new Date().getFullYear() }} Nexo Escolar. Todos os direitos reservados.</p>
            </div>
        </div>

        <!-- Right panel -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-slate-50 dark:bg-slate-950 relative">

            <!-- Theme toggle -->
            <button
                class="absolute top-5 right-5 w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                @click="toggle"
            >
                <i :class="isDark ? 'pi pi-sun' : 'pi pi-moon'" class="text-sm" />
            </button>

            <div class="w-full max-w-md">
                <!-- Mobile logo -->
                <div class="flex items-center gap-2 mb-10 lg:hidden">
                    <span class="text-xl font-bold text-indigo-600">Nexo</span>
                    <span class="text-xs text-slate-400 font-medium uppercase tracking-widest mt-0.5">Escolar</span>
                </div>

                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Bem-vindo de volta</h2>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Entre com seu e-mail ou matrícula para continuar.</p>
                </div>

                <div v-if="status" class="mb-6 flex items-center gap-2 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 text-sm px-4 py-3 rounded-lg">
                    <i class="pi pi-check-circle text-green-600 dark:text-green-400" />
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1.5">
                            E-mail ou Matrícula
                        </label>
                        <input
                            id="login"
                            v-model="form.login"
                            type="text"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="email@escola.com ou matrícula"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                            :class="{ 'border-red-400 focus:ring-red-400': form.errors.login }"
                        />
                        <p v-if="form.errors.login" class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <i class="pi pi-exclamation-circle" /> {{ form.errors.login }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1.5">Senha</label>
                        <div class="relative">
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full px-3.5 py-2.5 pr-10 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                :class="{ 'border-red-400 focus:ring-red-400': form.errors.password }"
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                                @click="showPassword = !showPassword"
                            >
                                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'" class="text-sm" />
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                            <i class="pi pi-exclamation-circle" /> {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                v-model="form.remember"
                                type="checkbox"
                                class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-indigo-600 focus:ring-indigo-500"
                            />
                            <span class="text-sm text-slate-600 dark:text-slate-300">Lembrar de mim</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                    >
                        <i v-if="form.processing" class="pi pi-spinner pi-spin text-sm" />
                        {{ form.processing ? 'Entrando...' : 'Entrar' }}
                    </button>
                </form>

                <p class="mt-8 text-center text-xs text-slate-400">
                    Problemas para acessar? Fale com o administrador da sua escola.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useTheme } from '@/composables/useTheme';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const { isDark, toggle } = useTheme();
const showPassword = ref(false);

const form = useForm({
    login: '',
    password: '',
    remember: false,
});

const features = [
    { icon: 'pi pi-objects-column', text: 'Turmas e disciplinas organizadas por ano letivo' },
    { icon: 'pi pi-star', text: 'Lançamento de notas por bimestre com média automática' },
    { icon: 'pi pi-calendar-clock', text: 'Controle de frequência por disciplina e data' },
    { icon: 'pi pi-users', text: 'Acesso separado para diretor, professor e aluno' },
];

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
