<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RecoveryExam;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Collection;

class GradeCalculatorService
{
    public readonly float $passingGrade;

    public function __construct()
    {
        $this->passingGrade = (float) config('app.passing_grade', 5.0);
    }

    public function summary(User $student, Subject $subject, ?Collection $activities = null, ?Collection $recoveries = null): array
    {
        $activities ??= $subject->activities()
            ->with(['grades' => fn ($q) => $q->where('student_id', $student->id)])
            ->get();

        $recoveries = ($recoveries ?? RecoveryExam::where('subject_id', $subject->id)
            ->where('student_id', $student->id)
            ->get())->keyBy('type');

        $quarters = [];
        foreach ([1, 2, 3, 4] as $q) {
            $quarters[$q] = $this->quarterAverage($activities->where('quarter', $q));
        }

        $sem1 = $this->semesterAverage($quarters[1], $quarters[2], $recoveries->get('recovery_1')?->score);
        $sem2 = $this->semesterAverage($quarters[3], $quarters[4], $recoveries->get('recovery_2')?->score);

        $final = $this->finalAverage($sem1, $sem2, $recoveries->get('final')?->score);

        return [
            'quarters' => $quarters,
            'recovery_1' => $recoveries->get('recovery_1')?->score,
            'recovery_2' => $recoveries->get('recovery_2')?->score,
            'final_exam' => $recoveries->get('final')?->score,
            'semester_1' => $sem1,
            'semester_2' => $sem2,
            'final' => $final,
            'passing' => $final !== null && $final >= $this->passingGrade,
        ];
    }

    /**
     * Média simples das atividades de um bimestre (ignora notas nulas).
     */
    private function quarterAverage(Collection $activities): float|null
    {
        $scores = $activities->flatMap(fn ($a) => $a->grades)->pluck('score')->filter(fn ($s) => $s !== null);

        return $scores->isNotEmpty() ? round($scores->avg(), 2) : null;
    }

    /**
     * Média semestral com recuperação opcional.
     * Se o aluno fez recuperação, a nota final do semestre é a média entre a média semestral e a recuperação.
     */
    private function semesterAverage(float|null $q1, float|null $q2, float|null $recovery): float|null
    {
        $scores = array_filter([$q1, $q2], fn ($v) => $v !== null);

        if (empty($scores)) {
            return null;
        }

        $avg = array_sum($scores) / count($scores);

        if ($recovery !== null) {
            $avg = round(($avg + $recovery) / 2, 2);
        }

        return round($avg, 2);
    }

    /**
     * Média final anual. Se o aluno fez prova final, a média é (média_anual + final) / 2.
     */
    private function finalAverage(float|null $sem1, float|null $sem2, float|null $finalExam): float|null
    {
        $scores = array_filter([$sem1, $sem2], fn ($v) => $v !== null);

        if (empty($scores)) {
            return null;
        }

        $avg = round(array_sum($scores) / count($scores), 2);

        if ($finalExam !== null) {
            $avg = round(($avg + $finalExam) / 2, 2);
        }

        return $avg;
    }
}
