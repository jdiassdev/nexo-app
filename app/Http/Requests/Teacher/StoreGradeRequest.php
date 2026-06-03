<?php

declare(strict_types=1);

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', $this->route('activity')->subject);
    }

    public function rules(): array
    {
        $maxGrade = $this->route('activity')->max_grade;

        return [
            'grades' => ['required', 'array'],
            'grades.*.student_id' => ['required', 'exists:users,id'],
            'grades.*.score' => ['nullable', 'numeric', 'min:0', "max:{$maxGrade}"],
        ];
    }
}
