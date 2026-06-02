<?php

declare(strict_types=1);

namespace App\Http\Requests\Director;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->route('student');

        return $student->school_id === $this->user()->school_id
            && $student->role === 'student';
    }

    public function rules(): array
    {
        $schoolId = $this->user()->school_id;
        $student = $this->route('student');

        return [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['nullable', Password::defaults()],
            'classroom_id' => ['nullable', Rule::exists('classrooms', 'id')->where('school_id', $schoolId)],
        ];
    }
}
