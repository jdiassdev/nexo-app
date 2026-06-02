<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['name' => 'Escola Nexo'],
            ['name' => 'Escola Nexo', 'city' => 'São Paulo']
        );

        $director = User::firstOrCreate(
            ['email' => 'diretor@nexo.app'],
            [
                'school_id' => $school->id,
                'name' => 'Diretor',
                'email' => 'diretor@nexo.app',
                'password' => Hash::make('1234567'),
                'role' => 'director',
            ]
        );

        $teacher = User::firstOrCreate(
            ['email' => 'professor@nexo.app'],
            [
                'school_id' => $school->id,
                'name' => 'Professor Silva',
                'email' => 'professor@nexo.app',
                'password' => Hash::make('1234567'),
                'role' => 'teacher',
            ]
        );

        $students = [];
        for ($i = 1; $i <= 5; $i++) {
            $students[] = User::firstOrCreate(
                ['enrollment' => "2026{$i}"],
                [
                    'school_id' => $school->id,
                    'name' => "Aluno {$i}",
                    'enrollment' => "2026{$i}",
                    'password' => Hash::make('1234567'),
                    'role' => 'student',
                ]
            );
        }

        $classroom = Classroom::firstOrCreate(
            ['school_id' => $school->id, 'name' => '9º A', 'school_year' => 2026],
            ['school_id' => $school->id, 'name' => '9º A', 'school_year' => 2026]
        );

        $classroom->students()->syncWithoutDetaching(
            collect($students)->pluck('id')->toArray()
        );

        Subject::firstOrCreate(
            ['classroom_id' => $classroom->id, 'name' => 'Matemática'],
            [
                'classroom_id' => $classroom->id,
                'teacher_id' => $teacher->id,
                'name' => 'Matemática',
                'workload' => 80,
                'max_absences' => 20,
            ]
        );
    }
}
