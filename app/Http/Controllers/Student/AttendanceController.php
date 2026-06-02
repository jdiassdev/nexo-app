<?php

declare(strict_types=1);

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function __invoke(Request $request, Subject $subject): Response
    {
        $student = $request->user();

        $enrolled = $student->classrooms()
            ->whereHas('subjects', fn ($q) => $q->where('subjects.id', $subject->id))
            ->exists();

        abort_unless($enrolled, 403);

        $records = Attendance::where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->get(['date', 'present']);

        $totalAbsences = $records->where('present', false)->count();

        return Inertia::render('Student/Subjects/Attendance', [
            'subject' => $subject->only('id', 'name', 'max_absences'),
            'records' => $records,
            'total_absences' => $totalAbsences,
        ]);
    }
}
