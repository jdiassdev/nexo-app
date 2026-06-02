<?php

declare(strict_types=1);

use App\Http\Controllers\Director\ClassroomController;
use App\Http\Controllers\Director\DashboardController;
use App\Http\Controllers\Director\StudentController;
use App\Http\Controllers\Director\SubjectController;
use App\Http\Controllers\Director\TeacherController;
use App\Http\Controllers\God\DashboardController as GodDashboardController;
use App\Http\Controllers\God\SchoolController as GodSchoolController;
use App\Http\Controllers\God\UserController as GodUserController;
use App\Http\Controllers\Teacher\ActivityController;
use App\Http\Controllers\Teacher\AttendanceController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\GradeController;
use App\Http\Controllers\Teacher\RecoveryExamController;
use App\Http\Controllers\Teacher\StudentController as TeacherStudentController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\GradeController as StudentGradeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;

        return redirect()->route(match ($role) {
            'god' => 'god.dashboard',
            'director' => 'director.dashboard',
            'teacher' => 'teacher.dashboard',
            'student' => 'student.dashboard',
            default => 'home',
        });
    }

    return Inertia::render('Landing');
})->name('home');

Route::middleware(['auth', 'role:god'])->prefix('god')->name('god.')->group(function () {
    Route::get('/dashboard', GodDashboardController::class)->name('dashboard');
    Route::resource('schools', GodSchoolController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('users', GodUserController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware(['auth', 'role:director'])->prefix('director')->name('director.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('classrooms', ClassroomController::class)->except(['create', 'edit', 'show']);
    Route::resource('classrooms.subjects', SubjectController::class)->shallow()->except(['create', 'edit', 'show']);

    Route::resource('teachers', TeacherController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('students', StudentController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', TeacherDashboardController::class)->name('dashboard');

    Route::resource('subjects.activities', ActivityController::class)
        ->shallow()
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('activities.grades', GradeController::class)
        ->shallow()
        ->only(['index', 'store']);

    Route::resource('subjects.attendance', AttendanceController::class)
        ->shallow()
        ->only(['index', 'store']);

    Route::get('subjects/{subject}/recovery', [RecoveryExamController::class, 'index'])->name('subjects.recovery.index');
    Route::post('subjects/{subject}/recovery', [RecoveryExamController::class, 'store'])->name('subjects.recovery.store');

    Route::get('/students', TeacherStudentController::class)->name('students.index');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', StudentDashboardController::class)->name('dashboard');
    Route::get('/subjects/{subject}/grades', StudentGradeController::class)->name('subjects.grades');
    Route::get('/subjects/{subject}/attendance', StudentAttendanceController::class)->name('subjects.attendance');
});

require __DIR__.'/auth.php';
