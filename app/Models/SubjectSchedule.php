<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subject_id', 'day_of_week', 'time_start', 'time_end'])]
class SubjectSchedule extends Model
{
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    protected function timeStart(): Attribute
    {
        return Attribute::get(fn (string $value) => substr($value, 0, 5));
    }

    protected function timeEnd(): Attribute
    {
        return Attribute::get(fn (string $value) => substr($value, 0, 5));
    }

    public static function dayLabel(int $day): string
    {
        return match ($day) {
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            default => '—',
        };
    }
}
