<?php

declare(strict_types=1);

namespace App\Http\Requests\God;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')],
            'password' => ['required', Password::defaults()],
            'role' => ['required', 'in:director,teacher,student'],
            'school_id' => ['required', 'exists:schools,id'],
        ];
    }
}
