<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'city'])]
class School extends Model
{
    use HasFactory;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    public function subjects(): HasManyThrough
    {
        return $this->hasManyThrough(Subject::class, Classroom::class);
    }

    public function scopeWithRoleCounts(Builder $query): void
    {
        $query->withCount([
            'users as directors_count' => fn ($q) => $q->where('role', 'director'),
            'users as teachers_count' => fn ($q) => $q->where('role', 'teacher'),
            'users as students_count' => fn ($q) => $q->where('role', 'student'),
            'classrooms',
        ]);
    }
}
