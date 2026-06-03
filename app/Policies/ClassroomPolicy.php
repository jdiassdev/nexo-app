<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Classroom;
use App\Models\User;

class ClassroomPolicy
{
    public function update(User $user, Classroom $classroom): bool
    {
        return $user->school_id === $classroom->school_id;
    }

    public function delete(User $user, Classroom $classroom): bool
    {
        return $user->school_id === $classroom->school_id;
    }
}
