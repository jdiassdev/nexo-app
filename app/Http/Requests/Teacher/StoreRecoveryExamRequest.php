<?php

declare(strict_types=1);

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecoveryExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('subject')->teacher_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'exams' => ['required', 'array'],
            'exams.*.student_id' => ['required', 'exists:users,id'],
            'exams.*.recovery_1' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'exams.*.recovery_2' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'exams.*.final_exam' => ['nullable', 'numeric', 'min:0', 'max:10'],
        ];
    }
}
