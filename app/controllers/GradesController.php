<?php

declare(strict_types=1);

final class GradesController
{
    public function __construct(private Grades $grades)
    {
    }

    public function student(int $studentId): array
    {
        return $this->grades->forStudent($studentId);
    }

    public function teacher(int $teacherId): array
    {
        return [
            'exams' => $this->grades->examsForTeacher($teacherId),
            'grades' => $this->grades->gradesForTeacher($teacherId),
        ];
    }

    public function save(
        int $teacherId,
        int $studentId,
        int $examId,
        ?float $score,
        string $feedback
    ): bool {
        return $this->grades->saveForTeacher($teacherId, $studentId, $examId, $score, $feedback);
    }

    public function createExam(int $teacherId, string $title, string $description, float $maxScore): bool
    {
        return $this->grades->createExam($teacherId, $title, $description, $maxScore);
    }
}
