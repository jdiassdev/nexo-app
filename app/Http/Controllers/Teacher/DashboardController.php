<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Grade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $teacher = $request->user();

        $subjects = $teacher->subjects()
            ->with('classroom:id,name,school_year')
            ->withCount([
                'activities',
                'activities as exams_count' => fn ($q) => $q->where('type', 'exam'),
            ])
            ->get(['id', 'name', 'classroom_id', 'max_absences']);

        $subjectIds = $subjects->pluck('id');

        $absencesBySubject = Attendance::whereIn('subject_id', $subjectIds)
            ->where('present', false)
            ->selectRaw('subject_id, COUNT(*) as total')
            ->groupBy('subject_id')
            ->pluck('total', 'subject_id');

        $gradesBySubject = Grade::whereHas('activity', fn ($q) => $q->whereIn('subject_id', $subjectIds))
            ->join('activities', 'grades.activity_id', '=', 'activities.id')
            ->selectRaw('activities.subject_id, AVG(grades.score) as avg_score, COUNT(grades.id) as total_grades')
            ->whereNotNull('grades.score')
            ->groupBy('activities.subject_id')
            ->get()
            ->keyBy('subject_id');

        $rows = $subjects->map(function ($subject) use ($absencesBySubject, $gradesBySubject) {
            $gradeData = $gradesBySubject[$subject->id] ?? null;

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'classroom' => $subject->classroom->only('id', 'name', 'school_year'),
                'activities_count' => $subject->activities_count,
                'exams_count' => $subject->exams_count,
                'absences' => (int) ($absencesBySubject[$subject->id] ?? 0),
                'max_absences' => $subject->max_absences,
                'avg_score' => $gradeData ? round((float) $gradeData->avg_score, 1) : null,
                'total_grades' => $gradeData ? (int) $gradeData->total_grades : 0,
            ];
        });

        return Inertia::render('Teacher/Dashboard', [
            'subjects' => $rows,
        ]);
    }
}
