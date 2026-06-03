<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function deleteTeacher(User $user, User $teacher): bool
    {
        return $user->school_id === $teacher->school_id && $teacher->isTeacher();
    }

    public function deleteStudent(User $user, User $student): bool
    {
        return $user->school_id === $student->school_id && $student->isStudent();
    }
}
