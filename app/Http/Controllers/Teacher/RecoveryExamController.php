<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreRecoveryExamRequest;
use App\Models\RecoveryExam;
use App\Models\Subject;
use App\Services\GradeCalculatorService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RecoveryExamController extends Controller
{
    public function index(Subject $subject, GradeCalculatorService $calculator): Response
    {
        $this->authorize('manage', $subject);

        $subject->load('classroom.students:id,name,enrollment');
        $students = $subject->classroom->students;

        $existing = RecoveryExam::where('subject_id', $subject->id)
            ->get()
            ->groupBy('student_id');

        $rows = $students->map(function ($student) use ($existing, $calculator, $subject) {
            $studentExams = $existing[$student->id] ?? collect();
            $summary = $calculator->summary($student, $subject);

            return [
                'student_id' => $student->id,
                'name' => $student->name,
                'enrollment' => $student->enrollment,
                'semester_1' => $summary['semester_1'],
                'semester_2' => $summary['semester_2'],
                'final_avg' => $summary['final'],
                'passing' => $summary['passing'],
                'recovery_1' => $studentExams->firstWhere('type', 'recovery_1')?->score,
                'recovery_2' => $studentExams->firstWhere('type', 'recovery_2')?->score,
                'final_exam' => $studentExams->firstWhere('type', 'final')?->score,
            ];
        });

        return Inertia::render('Teacher/Recovery/Index', [
            'subject' => $subject->only('id', 'name', 'max_absences') + [
                'classroom' => $subject->classroom->only('id', 'name', 'school_year'),
            ],
            'rows' => $rows,
            'passing_grade' => $calculator->passingGrade,
        ]);
    }

    public function store(StoreRecoveryExamRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();
        $now = now()->toDateTimeString();

        $typeMap = [
            'recovery_1' => 'recovery_1',
            'recovery_2' => 'recovery_2',
            'final_exam' => 'final',
        ];

        $records = [];

        foreach ($data['exams'] as $item) {
            foreach ($typeMap as $field => $type) {
                if (array_key_exists($field, $item)) {
                    $records[] = [
                        'subject_id' => $subject->id,
                        'student_id' => $item['student_id'],
                        'type' => $type,
                        'score' => $item[$field] !== null ? (float) $item[$field] : null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        if (!empty($records)) {
            RecoveryExam::upsert(
                $records,
                uniqueBy: ['subject_id', 'student_id', 'type'],
                update: ['score', 'updated_at'],
            );
        }

        return redirect()->route('teacher.subjects.recovery.index', $subject)
            ->with('success', 'Recuperações salvas.');
    }
}
