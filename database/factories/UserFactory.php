<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'teacher',
        ];
    }

    public function god(): static
    {
        return $this->state(['role' => 'god', 'school_id' => null]);
    }

    public function director(): static
    {
        return $this->state(['role' => 'director']);
    }

    public function teacher(): static
    {
        return $this->state(['role' => 'teacher']);
    }

    public function student(): static
    {
        return $this->state(fn () => [
            'role' => 'student',
            'email' => null,
            'enrollment' => User::generateEnrollment(),
        ]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}
