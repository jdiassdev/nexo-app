<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(Request $request, Subject $subject): Response
    {
        abort_unless($subject->teacher_id === $request->user()->id, 403);

        $date = $request->query('date', now()->toDateString());

        $subject->load('classroom.students:id,name,enrollment');
        $students = $subject->classroom->students;

        $attendance = Attendance::where('subject_id', $subject->id)
            ->where('date', $date)
            ->pluck('present', 'student_id');

        $rows = $students->map(fn ($student) => [
            'student_id' => $student->id,
            'name' => $student->name,
            'enrollment' => $student->enrollment,
            'present' => $attendance->has($student->id) ? (bool) $attendance[$student->id] : null,
        ]);

        $summary = Attendance::where('subject_id', $subject->id)
            ->where('present', false)
            ->selectRaw('student_id, COUNT(*) as total_absences')
            ->groupBy('student_id')
            ->pluck('total_absences', 'student_id');

        return Inertia::render('Teacher/Attendance/Index', [
            'subject' => $subject->only('id', 'name', 'max_absences') + [
                'classroom' => $subject->classroom->only('id', 'name', 'school_year'),
            ],
            'date' => $date,
            'rows' => $rows,
            'summary' => $summary,
            'filters' => ['search' => $request->query('search', '')],
        ]);
    }

    public function store(StoreAttendanceRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();
        $date = Carbon::parse($data['date'])->toDateString();
        $now = now()->toDateTimeString();

        $records = collect($data['attendance'])->map(fn ($item) => [
            'subject_id' => $subject->id,
            'student_id' => $item['student_id'],
            'date' => $date,
            'present' => $item['present'] ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ])->toArray();

        Attendance::upsert(
            $records,
            uniqueBy: ['subject_id', 'student_id', 'date'],
            update: ['present', 'updated_at'],
        );

        return redirect()->route('teacher.subjects.attendance.index', [
            'subject' => $subject->id,
            'date' => $data['date'],
        ])->with('success', 'Presença salva.');
    }
}
