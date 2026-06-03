<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $school = $request->user()->school;

        return Inertia::render('Director/Dashboard', [
            'stats' => [
                'classrooms' => $school->classrooms()->count(),
                'teachers' => $school->users()->teachers()->count(),
                'students' => $school->users()->students()->count(),
                'subjects' => $school->subjects()->count(),
            ],
        ]);
    }
}
