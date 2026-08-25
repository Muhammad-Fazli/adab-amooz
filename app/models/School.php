<?php

declare(strict_types=1);

final class School
{
    public function __construct(private PDO $database)
    {
    }

    public function forTeacher(int $teacherId): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, name, address, created_at, updated_at
             FROM schools WHERE teacher_id = :teacher_id
             ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $statement->execute(['teacher_id' => $teacherId]);
        $school = $statement->fetch();

        return $school ?: null;
    }

    public function listForTeacher(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT id, name, address, created_at, updated_at
             FROM schools WHERE teacher_id = :teacher_id ORDER BY created_at DESC'
        );
        $statement->execute(['teacher_id' => $teacherId]);

        return $statement->fetchAll();
    }

    public function classesForSchool(int $teacherId, int $schoolId): array
    {
        $statement = $this->database->prepare(
            'SELECT id, title, grade, created_at
             FROM classrooms
             WHERE teacher_id = :teacher_id
               AND (school_id = :school_id OR school_id IS NULL)
             ORDER BY created_at DESC, id DESC'
        );
        $statement->execute(['teacher_id' => $teacherId, 'school_id' => $schoolId]);

        return $statement->fetchAll();
    }

    public function saveForTeacher(int $teacherId, string $name, string $address): bool
    {
        $name = trim($name);
        $address = trim($address);
        if ($name === '' || mb_strlen($name) > 180 || mb_strlen($address) > 255) {
            return false;
        }

        $statement = $this->database->prepare(
            'INSERT INTO schools (teacher_id, name, address)
             VALUES (:teacher_id, :name, :address)'
        );
        return $statement->execute([
            'teacher_id' => $teacherId,
            'name' => $name,
            'address' => $address !== '' ? $address : null,
        ]);
    }
}