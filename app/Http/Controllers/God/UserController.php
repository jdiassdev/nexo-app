<?php

declare(strict_types=1);

namespace App\Http\Controllers\God;

use App\Http\Controllers\Controller;
use App\Http\Requests\God\StoreUserRequest;
use App\Http\Requests\God\UpdateUserRequest;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $role = $request->query('role', 'director');

        $users = User::where('role', $role)
            ->with('school:id,name')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('God/Users/Index', [
            'users' => $users,
            'role' => $role,
            'schools' => School::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            ...($data['role'] === 'student' ? ['enrollment' => User::generateEnrollment()] : []),
        ]);

        return back()->with('success', 'Usuário criado.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'school_id' => $data['school_id'],
            ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
        ]);

        return back()->with('success', 'Usuário atualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('success', 'Usuário removido.');
    }
}
