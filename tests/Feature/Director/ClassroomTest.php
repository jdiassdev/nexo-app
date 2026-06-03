<?php

declare(strict_types=1);

use App\Models\Classroom;

beforeEach(function () {
    $this->school = makeSchool();
    $this->director = makeDirector($this->school);
});

it('lista apenas turmas da própria escola', function () {
    Classroom::factory()->count(2)->create(['school_id' => $this->school->id]);
    Classroom::factory()->create(); // outra escola

    $this->actingAs($this->director)
        ->get('/director/classrooms')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Director/Classrooms/Index')
            ->has('classrooms', 2)
        );
});

it('cria turma na escola do diretor', function () {
    $this->actingAs($this->director)
        ->post('/director/classrooms', ['name' => '1A', 'school_year' => 2025])
        ->assertRedirect(route('director.classrooms.index'));

    $this->assertDatabaseHas('classrooms', [
        'school_id' => $this->school->id,
        'name' => '1A',
    ]);
});

it('não cria turma sem nome', function () {
    $this->actingAs($this->director)
        ->post('/director/classrooms', ['name' => '', 'school_year' => 2025])
        ->assertSessionHasErrors('name');
});

it('atualiza turma da própria escola', function () {
    $classroom = Classroom::factory()->create(['school_id' => $this->school->id, 'name' => 'Antiga']);

    $this->actingAs($this->director)
        ->put("/director/classrooms/{$classroom->id}", ['name' => 'Nova', 'school_year' => 2025])
        ->assertRedirect(route('director.classrooms.index'));

    $this->assertDatabaseHas('classrooms', ['id' => $classroom->id, 'name' => 'Nova']);
});

it('proíbe remover turma de outra escola', function () {
    $outraEscola = makeSchool();
    $classroom = Classroom::factory()->create(['school_id' => $outraEscola->id]);

    $this->actingAs($this->director)
        ->delete("/director/classrooms/{$classroom->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('classrooms', ['id' => $classroom->id]);
});

it('remove turma da própria escola', function () {
    $classroom = Classroom::factory()->create(['school_id' => $this->school->id]);

    $this->actingAs($this->director)
        ->delete("/director/classrooms/{$classroom->id}")
        ->assertRedirect(route('director.classrooms.index'));

    $this->assertDatabaseMissing('classrooms', ['id' => $classroom->id]);
});
