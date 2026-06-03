<?php

declare(strict_types=1);

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

function makeSchool(array $attrs = []): School
{
    return School::factory()->create($attrs);
}

function makeGod(array $attrs = []): User
{
    return User::factory()->god()->create($attrs);
}

function makeDirector(School $school, array $attrs = []): User
{
    return User::factory()->director()->create(['school_id' => $school->id, ...$attrs]);
}

function makeTeacher(School $school, array $attrs = []): User
{
    return User::factory()->teacher()->create(['school_id' => $school->id, ...$attrs]);
}

function makeStudent(School $school, array $attrs = []): User
{
    return User::factory()->student()->create(['school_id' => $school->id, ...$attrs]);
}
