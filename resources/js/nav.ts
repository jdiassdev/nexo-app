export const godNav = [
    { label: 'Dashboard', route: 'god.dashboard', icon: 'pi pi-home' },
    { label: 'Escolas', route: 'god.schools.index', icon: 'pi pi-building' },
    { label: 'Usuários', route: 'god.users.index', icon: 'pi pi-users' },
];

export const directorNav = [
    { label: 'Dashboard', route: 'director.dashboard', icon: 'pi pi-home' },
    { label: 'Turmas', route: 'director.classrooms.index', icon: 'pi pi-objects-column' },
    { label: 'Professores', route: 'director.teachers.index', icon: 'pi pi-user' },
    { label: 'Alunos', route: 'director.students.index', icon: 'pi pi-users' },
];

export const teacherNav = [
    { label: 'Minhas Disciplinas', route: 'teacher.dashboard', icon: 'pi pi-book' },
    { label: 'Alunos', route: 'teacher.students.index', icon: 'pi pi-users' },
];

export const studentNav = [
    { label: 'Minhas Disciplinas', route: 'student.dashboard', icon: 'pi pi-book' },
];
