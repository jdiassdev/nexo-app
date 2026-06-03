<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreClassroomRequest;
use App\Http\Requests\Director\UpdateClassroomRequest;
use App\Models\Classroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassroomController extends Controller
{
    public function index(Request $request): Response
    {
        $classrooms = Classroom::where('school_id', $request->user()->school_id)
            ->withCount(['students', 'subjects'])
            ->orderBy('school_year', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('Director/Classrooms/Index', [
            'classrooms' => $classrooms,
        ]);
    }

    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        $request->user()->school->classrooms()->create($request->validated());

        return redirect()->route('director.classrooms.index')
            ->with('success', 'Turma criada com sucesso.');
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $classroom->update($request->validated());

        return redirect()->route('director.classrooms.index')
            ->with('success', 'Turma atualizada com sucesso.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $this->authorize('delete', $classroom);

        $classroom->delete();

        return redirect()->route('director.classrooms.index')
            ->with('success', 'Turma removida com sucesso.');
    }
}
