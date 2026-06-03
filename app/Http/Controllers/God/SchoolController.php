<?php

declare(strict_types=1);

namespace App\Http\Controllers\God;

use App\Http\Controllers\Controller;
use App\Http\Requests\God\StoreSchoolRequest;
use App\Http\Requests\God\UpdateSchoolRequest;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SchoolController extends Controller
{
    public function index(): Response
    {
        $schools = School::withRoleCounts()->orderBy('name')->get();

        return Inertia::render('God/Schools/Index', ['schools' => $schools]);
    }

    public function store(StoreSchoolRequest $request): RedirectResponse
    {
        School::create($request->validated());

        return back()->with('success', 'Escola criada.');
    }

    public function update(UpdateSchoolRequest $request, School $school): RedirectResponse
    {
        $school->update($request->validated());

        return back()->with('success', 'Escola atualizada.');
    }

    public function destroy(School $school): RedirectResponse
    {
        $school->delete();

        return back()->with('success', 'Escola removida.');
    }
}
