<?php

declare(strict_types=1);

final class TodoTasks
{
    public function __construct(private PDO $database)
    {
    }

    public function forTeacher(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT id, title, is_completed, created_at
             FROM teacher_todos
             WHERE teacher_id = :teacher_id
             ORDER BY is_completed, created_at DESC, id DESC'
        );
        $statement->execute(['teacher_id' => $teacherId]);
        return $statement->fetchAll();
    }

    public function create(int $teacherId, string $title): bool
    {
        if (trim($title) === '' || mb_strlen(trim($title)) > 180) {
            return false;
        }
        $statement = $this->database->prepare(
            'INSERT INTO teacher_todos (teacher_id, title) VALUES (:teacher_id, :title)'
        );
        return $statement->execute(['teacher_id' => $teacherId, 'title' => trim($title)]);
    }

    public function setCompleted(int $teacherId, int $todoId, bool $completed): bool
    {
        $statement = $this->database->prepare(
            'UPDATE teacher_todos
             SET is_completed = :is_completed
             WHERE id = :id AND teacher_id = :teacher_id'
        );
        $statement->execute([
            'is_completed' => $completed ? 1 : 0,
            'id' => $todoId,
            'teacher_id' => $teacherId,
        ]);
        return $statement->rowCount() > 0;
    }
}
