<?php

declare(strict_types=1);

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Http\Requests\Director\StoreTeacherRequest;
use App\Http\Requests\Director\UpdateTeacherRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::where('school_id', $request->user()->school_id)
            ->where('role', 'teacher')
            ->withCount('subjects')
            ->orderBy('name');

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        return Inertia::render('Director/Teachers/Index', [
            'teachers' => $query->get(['id', 'name', 'email']),
            'filters' => $request->only('search'),
        ]);
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $request->user()->school->users()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'teacher',
        ]);

        return redirect()->route('director.teachers.index')
            ->with('success', 'Professor criado com sucesso.');
    }

    public function update(UpdateTeacherRequest $request, User $teacher): RedirectResponse
    {
        $data = $request->validated();

        $teacher->update([
            'name' => $data['name'],
            'email' => $data['email'],
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);

        return redirect()->route('director.teachers.index')
            ->with('success', 'Professor atualizado com sucesso.');
    }

    public function destroy(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->school_id === $request->user()->school_id && $teacher->role === 'teacher', 403);

        $subjectCount = $teacher->subjects()->count();

        if ($subjectCount > 0) {
            return back()->with('error', "Não é possível remover: {$teacher->name} possui {$subjectCount} disciplina(s) vinculada(s). Reatribua as disciplinas antes de remover.");
        }

        $teacher->delete();

        return redirect()->route('director.teachers.index')
            ->with('success', 'Professor removido com sucesso.');
    }
}
