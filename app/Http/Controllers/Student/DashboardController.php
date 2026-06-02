<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\RecoveryExam;
use App\Models\SubjectSchedule;
use App\Services\GradeCalculatorService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, GradeCalculatorService $calculator): Response
    {
        $student = $request->user();

        $subjects = $student->classrooms()
            ->with(['subjects.teacher:id,name', 'subjects.schedules'])
            ->get()
            ->pluck('subjects')
            ->flatten();

        $subjectIds = $subjects->pluck('id');

        $activitiesBySubject = Activity::whereIn('subject_id', $subjectIds)
            ->with(['grades' => fn ($q) => $q->where('student_id', $student->id)])
            ->get()
            ->groupBy('subject_id');

        $recoveryBySubject = RecoveryExam::whereIn('subject_id', $subjectIds)
            ->where('student_id', $student->id)
            ->get()
            ->groupBy('subject_id');

        $absenceCounts = Attendance::where('student_id', $student->id)
            ->where('present', false)
            ->selectRaw('subject_id, COUNT(*) as total')
            ->groupBy('subject_id')
            ->pluck('total', 'subject_id');

        $rows = $subjects->map(function ($subject) use ($student, $calculator, $absenceCounts, $activitiesBySubject, $recoveryBySubject) {
            $summary = $calculator->summary(
                $student,
                $subject,
                $activitiesBySubject[$subject->id] ?? collect(),
                $recoveryBySubject[$subject->id] ?? collect(),
            );

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'teacher' => $subject->teacher->name,
                'schedules' => $subject->schedules->map(fn ($s) => [
                    'day_of_week' => $s->day_of_week,
                    'day_label' => SubjectSchedule::dayLabel($s->day_of_week),
                    'time_start' => $s->time_start,
                    'time_end' => $s->time_end,
                ]),
                'final' => $summary['final'],
                'passing' => $summary['passing'],
                'absences' => $absenceCounts[$subject->id] ?? 0,
                'max_absences' => $subject->max_absences,
            ];
        });

        return Inertia::render('Student/Dashboard', [
            'subjects' => $rows,
        ]);
    }
}
