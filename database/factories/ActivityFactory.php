<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'type' => 'activity',
            'quarter' => fake()->numberBetween(1, 4),
            'title' => fake()->sentence(3),
            'max_grade' => 10.00,
        ];
    }
}
