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
                'directors' => User::directors()->count(),
                'teachers' => User::teachers()->count(),
                'students' => User::students()->count(),
                'classrooms' => Classroom::count(),
            ],
            'schools' => School::withRoleCounts()->orderBy('name')->get(['id', 'name', 'city']),
        ]);
    }
}
