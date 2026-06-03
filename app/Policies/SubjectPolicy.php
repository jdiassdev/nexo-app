<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function manage(User $user, Subject $subject): bool
    {
        return $user->id === $subject->teacher_id;
    }
}
