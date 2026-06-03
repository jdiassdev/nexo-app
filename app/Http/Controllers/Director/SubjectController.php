<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreSubjectRequest;
use App\Http\Requests\Director\UpdateSubjectRequest;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\SubjectSchedule;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    public function index(Request $request, Classroom $classroom): Response
    {
        abort_unless($classroom->school_id === $request->user()->school_id, 403);

        $subjects = $classroom->subjects()
            ->with(['teacher:id,name', 'schedules'])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Director/Subjects/Index', [
            'classroom' => $classroom,
            'subjects' => $subjects,
            'teachers' => $this->teachers($request),
        ]);
    }

    public function store(StoreSubjectRequest $request, Classroom $classroom): RedirectResponse
    {
        $data = $request->validated();

        $subject = $classroom->subjects()->create([
            'name' => $data['name'],
            'teacher_id' => $data['teacher_id'],
            'workload' => $data['workload'] ?? null,
            'max_absences' => $data['max_absences'] ?? null,
        ]);

        $this->syncSchedules($subject, $data['schedules'] ?? []);

        return redirect()->route('director.classrooms.subjects.index', $classroom)
            ->with('success', 'Disciplina criada.');
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $data = $request->validated();

        $subject->update([
            'name' => $data['name'],
            'teacher_id' => $data['teacher_id'],
            'workload' => $data['workload'] ?? null,
            'max_absences' => $data['max_absences'] ?? null,
        ]);

        $this->syncSchedules($subject, $data['schedules'] ?? []);

        return redirect()->route('director.classrooms.subjects.index', $subject->classroom_id)
            ->with('success', 'Disciplina atualizada.');
    }

    public function destroy(Request $request, Subject $subject): RedirectResponse
    {
        abort_unless($subject->classroom->school_id === $request->user()->school_id, 403);

        $classroomId = $subject->classroom_id;
        $subject->delete();

        return redirect()->route('director.classrooms.subjects.index', $classroomId)
            ->with('success', 'Disciplina removida.');
    }

    private function syncSchedules(Subject $subject, array $schedules): void
    {
        $subject->schedules()->delete();

        if (empty($schedules)) {
            return;
        }

        $now = now()->toDateTimeString();

        SubjectSchedule::insert(array_map(fn ($s) => [
            'subject_id' => $subject->id,
            'day_of_week' => $s['day_of_week'],
            'time_start' => $s['time_start'],
            'time_end' => $s['time_end'],
            'created_at' => $now,
            'updated_at' => $now,
        ], $schedules));
    }

    private function teachers(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('school_id', $request->user()->school_id)
            ->teachers()
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
