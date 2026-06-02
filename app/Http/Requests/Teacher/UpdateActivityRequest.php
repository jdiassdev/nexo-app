<?php

declare(strict_types=1);

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('activity')->subject->teacher_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:activity,exam'],
            'quarter' => ['required', 'integer', 'between:1,4'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'max_grade' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }
}
