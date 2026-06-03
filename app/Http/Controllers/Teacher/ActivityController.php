<?php

declare(strict_types=1);

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreActivityRequest;
use App\Http\Requests\Teacher\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ActivityController extends Controller
{
    public function index(Request $request, Subject $subject): Response
    {
        $this->authorize('manage', $subject);

        $activities = $subject->activities()
            ->withCount('grades')
            ->orderBy('quarter')
            ->orderBy('due_date')
            ->get();

        return Inertia::render('Teacher/Activities/Index', [
            'subject' => $subject->load('classroom:id,name,school_year'),
            'activities' => $activities,
        ]);
    }

    public function store(StoreActivityRequest $request, Subject $subject): RedirectResponse
    {
        $subject->activities()->create($request->validated());

        return redirect()->route('teacher.subjects.activities.index', $subject)
            ->with('success', 'Atividade criada.');
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($request->validated());

        return redirect()->route('teacher.subjects.activities.index', $activity->subject_id)
            ->with('success', 'Atividade atualizada.');
    }

    public function destroy(Request $request, Activity $activity): RedirectResponse
    {
        $this->authorize('manage', $activity->subject);

        $subjectId = $activity->subject_id;
        $activity->delete();

        return redirect()->route('teacher.subjects.activities.index', $subjectId)
            ->with('success', 'Atividade removida.');
    }
}
