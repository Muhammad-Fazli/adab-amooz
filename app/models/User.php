<?php

declare(strict_types=1);

final class User
{
    public function __construct(private PDO $database)
    {
    }

    public function create(string $username, string $password, string $role = 'student', ?int $teacherId = null): int
    {
        $statement = $this->database->prepare(
            'INSERT INTO users (username, first_name, last_name, email, password_hash, role, teacher_id) VALUES (:username, :first_name, :last_name, NULL, :password_hash, :role, :teacher_id)'
        );
        $statement->execute([
            'username' => $username,
            'first_name' => $username,
            'last_name' => '',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'teacher_id' => $teacherId,
        ]);

        return (int) $this->database->lastInsertId();
    }

    public function findByUsername(string $username): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $statement->execute(['username' => $username]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function findById(int $userId): ?array
    {
        $statement = $this->database->prepare('SELECT id, username, first_name, last_name, role, teacher_id, state, city, education_level, favorite_subject, profile_completed FROM users WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $userId]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $allowed = ['first_name', 'last_name', 'state', 'city', 'education_level', 'favorite_subject'];
        $updates = [];
        $params = ['id' => $userId];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $updates[] = 'profile_completed = 1';
        $statement = $this->database->prepare('UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = :id');
        return $statement->execute($params);
    }
}