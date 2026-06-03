<?php

declare(strict_types=1);

use App\Models\Activity;
use App\Models\Classroom;
use App\Models\Subject;

beforeEach(function () {
    $this->school = makeSchool();
    $this->teacher = makeTeacher($this->school);

    $classroom = Classroom::factory()->create(['school_id' => $this->school->id]);

    $this->subject = Subject::factory()->create([
        'classroom_id' => $classroom->id,
        'teacher_id' => $this->teacher->id,
    ]);
});

it('lista atividades do próprio subject', function () {
    Activity::factory()->count(3)->create(['subject_id' => $this->subject->id]);

    $this->actingAs($this->teacher)
        ->get("/teacher/subjects/{$this->subject->id}/activities")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Teacher/Activities/Index')
            ->has('activities', 3)
        );
});

it('bloqueia acesso a subject de outro professor', function () {
    $outroTeacher = makeTeacher($this->school);
    $outroSubject = Subject::factory()->create([
        'classroom_id' => $this->subject->classroom_id,
        'teacher_id' => $outroTeacher->id,
    ]);

    $this->actingAs($this->teacher)
        ->get("/teacher/subjects/{$outroSubject->id}/activities")
        ->assertForbidden();
});

it('cria atividade no próprio subject', function () {
    $this->actingAs($this->teacher)
        ->post("/teacher/subjects/{$this->subject->id}/activities", [
            'title' => 'Prova 1',
            'type' => 'exam',
            'quarter' => 1,
            'max_grade' => 10,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('activities', [
        'subject_id' => $this->subject->id,
        'title' => 'Prova 1',
        'type' => 'exam',
    ]);
});

it('bloqueia criação em subject de outro professor', function () {
    $outroTeacher = makeTeacher($this->school);
    $outroSubject = Subject::factory()->create([
        'classroom_id' => $this->subject->classroom_id,
        'teacher_id' => $outroTeacher->id,
    ]);

    $this->actingAs($this->teacher)
        ->post("/teacher/subjects/{$outroSubject->id}/activities", [
            'title' => 'Tentativa',
            'type' => 'activity',
            'quarter' => 1,
            'max_grade' => 10,
        ])
        ->assertForbidden();
});

it('remove atividade do próprio subject', function () {
    $activity = Activity::factory()->create(['subject_id' => $this->subject->id]);

    $this->actingAs($this->teacher)
        ->delete("/teacher/activities/{$activity->id}")
        ->assertRedirect();

    $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
});

it('bloqueia remoção de atividade de outro professor', function () {
    $outroTeacher = makeTeacher($this->school);
    $outroSubject = Subject::factory()->create([
        'classroom_id' => $this->subject->classroom_id,
        'teacher_id' => $outroTeacher->id,
    ]);
    $activity = Activity::factory()->create(['subject_id' => $outroSubject->id]);

    $this->actingAs($this->teacher)
        ->delete("/teacher/activities/{$activity->id}")
        ->assertForbidden();

    $this->assertDatabaseHas('activities', ['id' => $activity->id]);
});
