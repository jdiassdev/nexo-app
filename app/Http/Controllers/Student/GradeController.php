<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\RecoveryExam;
use App\Models\Subject;
use App\Services\GradeCalculatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function __invoke(Request $request, Subject $subject, GradeCalculatorService $calculator): Response
    {
        $student = $request->user();

        $enrolled = $student->classrooms()
            ->whereHas('subjects', fn ($q) => $q->where('subjects.id', $subject->id))
            ->exists();

        abort_unless($enrolled, 403);

        $activities = $subject->activities()
            ->with(['grades' => fn ($q) => $q->where('student_id', $student->id)])
            ->orderBy('quarter')
            ->orderBy('due_date')
            ->get();

        $recoveries = RecoveryExam::where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->get();

        $summary = $calculator->summary($student, $subject, $activities, $recoveries);

        $activities = $activities
            ->groupBy('quarter')
            ->map(fn ($group) => $group->map(fn ($activity) => [
                'id' => $activity->id,
                'title' => $activity->title,
                'max_grade' => $activity->max_grade,
                'score' => $activity->grades->first()?->score,
            ]));

        return Inertia::render('Student/Subjects/Grades', [
            'subject' => $subject->only('id', 'name'),
            'activities' => $activities,
            'summary' => $summary,
        ]);
    }
}
