# Nexo Escolar

> Projeto de aprendizado sistema de gestão escolar construído com Laravel 12, Inertia.js e Vue 3, pensado em cima de um problema real.

---

> **Contexto**
>
> O Nexo nasceu como estudo de uma stack moderna, mas partindo de um problema concreto: escolas que ainda controlam notas e frequência em planilhas, sem integração entre professores, direção e alunos. O fluxo foi desenhado como se fosse um sistema real com múltiplos papéis, isolamento de dados e uma interface que qualquer pessoa consiga usar. Pode conter escolhas simplificadas intencionalmente. O objetivo é aprender fazendo, com qualidade mas sem over-engineering.

---

## Screenshots

### Landing Page

![Landing Page](/docs/screenshots/imagelp.png)

### Login

![Login](/docs/screenshots/imagelogin.png)

---

## Stack

| Camada   | Tecnologias                               |
| -------- | ----------------------------------------- |
| Backend  | PHP 8.3, Laravel 13, Inertia.js           |
| Frontend | Vue 3, TypeScript, PrimeVue, Tailwind CSS |
| Banco    | SQLite (dev) / MySQL (prod)               |
| Tooling  | Vite, Laravel Pint, ESLint, Prettier      |

---

## Papéis

| Papel      | Acesso                                                         |
| ---------- | -------------------------------------------------------------- |
| `god`      | Sistema inteiro — escolas e usuários                           |
| `director` | Sua escola — turmas, disciplinas, professores, alunos          |
| `teacher`  | Suas disciplinas — atividades, notas, frequência, recuperações |
| `student`  | Seus próprios dados — notas e frequência                       |

---

## Instalação

**Pré-requisitos:** PHP 8.3+, Composer 2.x, Node.js 20+

```bash
git clone https://github.com/seu-usuario/nexo-app.git
cd nexo-app
composer setup
```

O `composer setup` faz tudo: instala dependências, gera `.env`, roda migrations e faz o build.

**Servidor de desenvolvimento:**

```bash
composer dev
```

Sobe Laravel, Vite (HMR), log em tempo real e fila de jobs em paralelo. Acesse **http://127.0.0.1:8000**.

---

## Variáveis de Ambiente

```env
APP_NAME="Nexo Escolar"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=sqlite

# Média mínima para aprovação (backend + frontend)
PASSING_GRADE=5.0
VITE_PASSING_GRADE=5.0
```

---

## Fluxo de Avaliação

```
Bimestre 1 ──┐
Bimestre 2 ──┴──► Média Sem. 1 ──► (+ Rec. 1?) ──┐
                                                    ├──► Média Anual ──► (+ Prova Final?) ──► Média Final
Bimestre 3 ──┐                                      │
Bimestre 4 ──┴──► Média Sem. 2 ──► (+ Rec. 2?) ──┘
```

Aprovação: `média_final >= PASSING_GRADE` (padrão 5.0, configurável via `.env`).

---

## Licença

MIT
