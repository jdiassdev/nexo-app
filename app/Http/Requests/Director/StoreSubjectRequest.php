<?php

declare(strict_types=1);

namespace App\Http\Requests\Director;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('classroom')->school_id === $this->user()->school_id;
    }

    public function rules(): array
    {
        $schoolId = $this->user()->school_id;

        return [
            'name' => ['required', 'string', 'max:100'],
            'teacher_id' => ['required', Rule::exists('users', 'id')->where('school_id', $schoolId)->where('role', 'teacher')],
            'workload' => ['nullable', 'integer', 'min:1'],
            'max_absences' => ['nullable', 'integer', 'min:1'],
            'schedules' => ['nullable', 'array'],
            'schedules.*.day_of_week' => ['required', 'integer', 'between:1,5'],
            'schedules.*.time_start' => ['required', 'date_format:H:i'],
            'schedules.*.time_end' => ['required', 'date_format:H:i', 'after:schedules.*.time_start'],
        ];
    }
}
