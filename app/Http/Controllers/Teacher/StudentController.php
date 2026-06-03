<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $teacher = $request->user();

        $classroomIds = $teacher->subjects()->distinct()->pluck('classroom_id');

        $query = User::where('role', 'student')
            ->whereHas('classrooms', fn($q) => $q->whereIn('classrooms.id', $classroomIds))
            ->with(['classrooms' => fn($q) => $q->whereIn('classrooms.id', $classroomIds)->select('classrooms.id', 'name', 'school_year')])
            ->orderBy('name');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('enrollment', 'like', "%{$search}%");
            });
        }

        if ($classroomId = $request->query('classroom_id')) {
            $query->whereHas('classrooms', fn($q) => $q->where('classrooms.id', $classroomId));
        }

        $classrooms = Classroom::whereIn('id', $classroomIds)
            ->orderBy('school_year', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'school_year']);

        return Inertia::render('Teacher/Students/Index', [
            'students' => $query->get(['id', 'name', 'enrollment']),
            'classrooms' => $classrooms,
            'filters' => $request->only('search', 'classroom_id'),
        ]);
    }
}
