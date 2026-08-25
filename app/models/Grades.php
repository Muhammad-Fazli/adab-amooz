<?php

declare(strict_types=1);

final class Grades
{
    public function __construct(private PDO $database)
    {
    }

    public function forStudent(int $studentId): array
    {
        $statement = $this->database->prepare(
            'SELECT e.id, e.title, e.description, e.max_score,
                    eg.score, eg.feedback, eg.updated_at,
                    CONCAT(t.first_name, " ", t.last_name) AS teacher_name
             FROM exams e
             LEFT JOIN exam_grades eg ON eg.exam_id = e.id AND eg.student_id = :student_id
             LEFT JOIN users t ON t.id = eg.teacher_id
                         WHERE e.is_published = 1
                             AND (e.teacher_id IS NULL OR e.teacher_id = (SELECT teacher_id FROM users WHERE id = :student_owner))
             ORDER BY e.display_order, e.id'
        );
        $statement->execute(['student_id' => $studentId, 'student_owner' => $studentId]);

        return $statement->fetchAll();
    }

    public function examsForTeacher(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT id, title, max_score
             FROM exams
             WHERE is_published = 1 AND (teacher_id = :teacher_id OR teacher_id IS NULL)
             ORDER BY display_order, id'
        );
        $statement->execute(['teacher_id' => $teacherId]);

        return $statement->fetchAll();
    }

    public function gradesForTeacher(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT eg.exam_id, eg.student_id, eg.score, eg.feedback
             FROM exam_grades eg
             INNER JOIN exams e ON e.id = eg.exam_id
             INNER JOIN users student ON student.id = eg.student_id AND student.teacher_id = :student_teacher_id
             WHERE eg.teacher_id = :grade_teacher_id
             ORDER BY eg.updated_at DESC'
        );
        $statement->execute(['student_teacher_id' => $teacherId, 'grade_teacher_id' => $teacherId]);

        return $statement->fetchAll();
    }

    public function createExam(int $teacherId, string $title, string $description, float $maxScore): bool
    {
        if (trim($title) === '' || $maxScore <= 0 || $maxScore > 100) {
            return false;
        }
        try {
            $statement = $this->database->prepare(
                'INSERT INTO exams (teacher_id, title, description, max_score, display_order, is_published)
                 VALUES (:teacher_id, :title, :description, :max_score, 0, 1)'
            );
            return $statement->execute([
                'teacher_id' => $teacherId,
                'title' => trim($title),
                'description' => trim($description) !== '' ? trim($description) : null,
                'max_score' => $maxScore,
            ]);
        } catch (PDOException) {
            return false;
        }
    }

    public function saveForTeacher(
        int $teacherId,
        int $studentId,
        int $examId,
        ?float $score,
        string $feedback
    ): bool {
        $access = $this->database->prepare(
            'SELECT 1
             FROM users student
             WHERE student.id = :student_id AND student.teacher_id = :teacher_id
             LIMIT 1'
        );
        $access->execute(['student_id' => $studentId, 'teacher_id' => $teacherId]);
        if (!$access->fetchColumn()) {
            return false;
        }

                $exam = $this->database->prepare(
                        'SELECT max_score FROM exams
                         WHERE id = :exam_id AND is_published = 1
                             AND (teacher_id = :teacher_id OR teacher_id IS NULL)'
                );
                $exam->execute(['exam_id' => $examId, 'teacher_id' => $teacherId]);
        $maxScore = $exam->fetchColumn();
        if ($maxScore === false || ($score !== null && ($score < 0 || $score > (float) $maxScore))) {
            return false;
        }

        $statement = $this->database->prepare(
            'INSERT INTO exam_grades (exam_id, student_id, teacher_id, score, feedback)
             VALUES (:exam_id, :student_id, :teacher_id, :score, :feedback)
             ON DUPLICATE KEY UPDATE
                teacher_id = VALUES(teacher_id), score = VALUES(score), feedback = VALUES(feedback), updated_at = CURRENT_TIMESTAMP'
        );
        $statement->execute([
            'exam_id' => $examId,
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'score' => $score,
            'feedback' => trim($feedback) !== '' ? trim($feedback) : null,
        ]);

        return true;
    }
}
