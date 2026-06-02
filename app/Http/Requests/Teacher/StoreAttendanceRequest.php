<?php

declare(strict_types=1);

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('subject')->teacher_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date'],
            'attendance' => ['required', 'array'],
            'attendance.*.student_id' => ['required', 'exists:users,id'],
            'attendance.*.present' => ['required', 'boolean'],
        ];
    }
}
