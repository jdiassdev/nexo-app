<?php

declare(strict_types=1);

namespace App\Http\Controllers\God;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\School;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('God/Dashboard', [
            'stats' => [
                'schools' => School::count(),
                'directors' => User::where('role', 'director')->count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'students' => User::where('role', 'student')->count(),
                'classrooms' => Classroom::count(),
            ],
            'schools' => School::withCount([
                'users as directors_count' => fn ($q) => $q->where('role', 'director'),
                'users as teachers_count' => fn ($q) => $q->where('role', 'teacher'),
                'users as students_count' => fn ($q) => $q->where('role', 'student'),
                'classrooms',
            ])->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }
}
