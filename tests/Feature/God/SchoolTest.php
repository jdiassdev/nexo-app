<?php

declare(strict_types=1);

use App\Models\School;

beforeEach(function () {
    $this->god = makeGod();
});

it('lista escolas', function () {
    School::factory()->count(3)->create();

    $this->actingAs($this->god)
        ->get('/god/schools')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('God/Schools/Index')
            ->has('schools', 3)
        );
});

it('cria escola', function () {
    $this->actingAs($this->god)
        ->post('/god/schools', ['name' => 'Escola Nova', 'city' => 'Curitiba'])
        ->assertRedirect();

    $this->assertDatabaseHas('schools', ['name' => 'Escola Nova', 'city' => 'Curitiba']);
});

it('não cria escola sem nome', function () {
    $this->actingAs($this->god)
        ->post('/god/schools', ['name' => '', 'city' => 'Curitiba'])
        ->assertSessionHasErrors('name');
});

it('atualiza escola', function () {
    $school = makeSchool(['name' => 'Antiga']);

    $this->actingAs($this->god)
        ->put("/god/schools/{$school->id}", ['name' => 'Nova', 'city' => 'SP'])
        ->assertRedirect();

    $this->assertDatabaseHas('schools', ['id' => $school->id, 'name' => 'Nova']);
});

it('remove escola', function () {
    $school = makeSchool();

    $this->actingAs($this->god)
        ->delete("/god/schools/{$school->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('schools', ['id' => $school->id]);
});

it('diretor não acessa escolas', function () {
    $this->actingAs(makeDirector(makeSchool()))
        ->get('/god/schools')
        ->assertForbidden();
});
