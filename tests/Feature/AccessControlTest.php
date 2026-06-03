<?php

declare(strict_types=1);

use App\Models\User;

it('redireciona visitante para login', function (string $url) {
    $this->get($url)->assertRedirect('/login');
})->with([
    '/god/dashboard',
    '/director/dashboard',
    '/teacher/dashboard',
    '/student/dashboard',
]);

it('bloqueia papel errado nas rotas god', function (string $role) {
    $school = makeSchool();
    $user = User::factory()->create(['role' => $role, 'school_id' => $school->id]);

    $this->actingAs($user)->get('/god/dashboard')->assertForbidden();
})->with(['director', 'teacher', 'student']);

it('bloqueia papel errado nas rotas director', function (string $role) {
    $school = makeSchool();
    $user = match ($role) {
        'god' => makeGod(),
        'teacher' => makeTeacher($school),
        'student' => makeStudent($school),
    };

    $this->actingAs($user)->get('/director/dashboard')->assertForbidden();
})->with(['god', 'teacher', 'student']);

it('bloqueia papel errado nas rotas teacher', function (string $role) {
    $school = makeSchool();
    $user = match ($role) {
        'god' => makeGod(),
        'director' => makeDirector($school),
        'student' => makeStudent($school),
    };

    $this->actingAs($user)->get('/teacher/dashboard')->assertForbidden();
})->with(['god', 'director', 'student']);

it('bloqueia papel errado nas rotas student', function (string $role) {
    $school = makeSchool();
    $user = match ($role) {
        'god' => makeGod(),
        'director' => makeDirector($school),
        'teacher' => makeTeacher($school),
    };

    $this->actingAs($user)->get('/student/dashboard')->assertForbidden();
})->with(['god', 'director', 'teacher']);

it('permite cada papel acessar sua própria área', function (string $role, string $url) {
    $school = makeSchool();
    $user = match ($role) {
        'god' => makeGod(),
        'director' => makeDirector($school),
        'teacher' => makeTeacher($school),
        'student' => makeStudent($school),
    };

    $this->actingAs($user)->get($url)->assertOk();
})->with([
    ['god', '/god/dashboard'],
    ['director', '/director/dashboard'],
    ['teacher', '/teacher/dashboard'],
    ['student', '/student/dashboard'],
]);
