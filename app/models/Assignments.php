<?php

declare(strict_types=1);

final class Assignments
{
    public function __construct(private PDO $database)
    {
    }

    public function createForTeacher(int $teacherId, string $title, string $description, ?string $dueAt): bool
    {
        if (trim($title) === '') {
            return false;
        }
        if ($dueAt !== null && trim($dueAt) !== '') {
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i', $dueAt);
            if (!$date || $date->format('Y-m-d H:i') !== $dueAt) {
                return false;
            }
        }

        $classroom = $this->database->prepare('SELECT id FROM classrooms WHERE teacher_id = :teacher_id ORDER BY id LIMIT 1');
        $classroom->execute(['teacher_id' => $teacherId]);
        $classroomId = (int) $classroom->fetchColumn();
        if ($classroomId === 0) {
            $createClassroom = $this->database->prepare('INSERT INTO classrooms (teacher_id, title) VALUES (:teacher_id, :title)');
            $createClassroom->execute(['teacher_id' => $teacherId, 'title' => 'کلاس ادب آموز']);
            $classroomId = (int) $this->database->lastInsertId();
        }

        $statement = $this->database->prepare(
            'INSERT INTO assignments (classroom_id, teacher_id, title, description, due_at)
             VALUES (:classroom_id, :teacher_id, :title, :description, :due_at)'
        );
        return $statement->execute([
            'classroom_id' => $classroomId,
            'teacher_id' => $teacherId,
            'title' => trim($title),
            'description' => trim($description) !== '' ? trim($description) : null,
            'due_at' => trim((string) $dueAt) !== '' ? $dueAt : null,
        ]);
    }

    public function forTeacher(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT a.id, a.title, a.description, a.due_at, a.created_at,
                    COUNT(s.id) AS submission_count,
                    SUM(CASE WHEN s.status = "pending" THEN 1 ELSE 0 END) AS pending_count
             FROM assignments a
             LEFT JOIN assignment_submissions s ON s.assignment_id = a.id
             WHERE a.teacher_id = :teacher_id
             GROUP BY a.id
             ORDER BY a.created_at DESC'
        );
        $statement->execute(['teacher_id' => $teacherId]);
        return $statement->fetchAll();
    }

    public function forStudent(int $studentId): array
    {
        $statement = $this->database->prepare(
            'SELECT a.id, a.title, a.description, a.due_at, a.created_at,
                    u.first_name AS teacher_first_name, u.last_name AS teacher_last_name
             FROM assignments a
             INNER JOIN users u ON u.id = a.teacher_id
             INNER JOIN users student ON student.id = :student_id AND student.teacher_id = a.teacher_id
             ORDER BY a.created_at DESC'
        );
        $statement->execute(['student_id' => $studentId]);
        return $statement->fetchAll();
    }
}
