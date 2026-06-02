<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreGradeRequest;
use App\Models\Activity;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function index(Request $request, Activity $activity): Response
    {
        $subject = $activity->subject()->with('classroom.students:id,name,enrollment')->first();

        abort_unless($subject->teacher_id === $request->user()->id, 403);

        $grades = $activity->grades()->pluck('score', 'student_id');

        $rows = $subject->classroom->students->map(fn ($student) => [
            'student_id' => $student->id,
            'name' => $student->name,
            'enrollment' => $student->enrollment,
            'score' => $grades[$student->id] ?? null,
        ]);

        return Inertia::render('Teacher/Grades/Index', [
            'activity' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'quarter' => $activity->quarter,
                'max_grade' => $activity->max_grade,
                'subject_id' => $activity->subject_id,
            ],
            'subject' => [
                'id' => $subject->id,
                'name' => $subject->name,
                'classroom' => $subject->classroom->only('id', 'name', 'school_year'),
            ],
            'rows' => $rows,
            'filters' => ['search' => $request->query('search', '')],
        ]);
    }

    public function store(StoreGradeRequest $request, Activity $activity): RedirectResponse
    {
        $now = now()->toDateTimeString();

        $records = collect($request->validated()['grades'])->map(fn ($item) => [
            'activity_id' => $activity->id,
            'student_id' => $item['student_id'],
            'score' => isset($item['score']) ? (float) $item['score'] : null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        Grade::upsert(
            $records,
            uniqueBy: ['activity_id', 'student_id'],
            update: ['score', 'updated_at'],
        );

        return redirect()->route('teacher.activities.grades.index', $activity)
            ->with('success', 'Notas salvas.');
    }
}
