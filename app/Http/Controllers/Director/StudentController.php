<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreStudentRequest;
use App\Http\Requests\Director\UpdateStudentRequest;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::where('school_id', $request->user()->school_id)
            ->students()
            ->with('classrooms:id,name,school_year')
            ->orderBy('name');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('enrollment', 'like', "%{$search}%");
            });
        }

        if ($classroomId = $request->query('classroom_id')) {
            $query->whereHas('classrooms', fn ($q) => $q->where('classrooms.id', $classroomId));
        }

        return Inertia::render('Director/Students/Index', [
            'students' => $query->get(['id', 'name', 'enrollment']),
            'classrooms' => $this->classrooms($request),
            'filters' => $request->only('search', 'classroom_id'),
        ]);
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $student = $request->user()->school->users()->create([
            'name' => $data['name'],
            'enrollment' => User::generateEnrollment(),
            'password' => Hash::make($data['password']),
            'role' => 'student',
        ]);

        if ($data['classroom_id']) {
            $student->classrooms()->attach($data['classroom_id']);
        }

        return redirect()->route('director.students.index')
            ->with('success', 'Aluno criado com sucesso.');
    }

    public function update(UpdateStudentRequest $request, User $student): RedirectResponse
    {
        $data = $request->validated();

        $student->update([
            'name' => $data['name'],
            ...($data['password'] ?? null ? ['password' => Hash::make($data['password'])] : []),
        ]);

        $student->classrooms()->sync($data['classroom_id'] ? [$data['classroom_id']] : []);

        return redirect()->route('director.students.index')
            ->with('success', 'Aluno atualizado com sucesso.');
    }

    public function destroy(User $student): RedirectResponse
    {
        $this->authorize('deleteStudent', $student);

        $student->delete();

        return redirect()->route('director.students.index')
            ->with('success', 'Aluno removido com sucesso.');
    }

    private function classrooms(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        return Classroom::where('school_id', $request->user()->school_id)
            ->orderBy('school_year', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'school_year']);
    }
}
