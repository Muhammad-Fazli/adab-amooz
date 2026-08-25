<?php

declare(strict_types=1);

final class AdminDashboard
{
    public function __construct(private PDO $database)
    {
    }

    public function students(): array
    {
        $statement = $this->database->query(
            "SELECT username, created_at FROM users WHERE role = 'student' ORDER BY created_at DESC"
        );

        return $statement->fetchAll();
    }

    public function users(?string $role = null): array
    {
        if ($role === 'student' || $role === 'teacher') {
            $statement = $this->database->prepare(
                'SELECT username, first_name, last_name, role, created_at
                 FROM users WHERE role = :role ORDER BY created_at DESC'
            );
            $statement->execute(['role' => $role]);
        } else {
            $statement = $this->database->query(
                "SELECT username, first_name, last_name, role, created_at FROM users WHERE role <> 'admin' ORDER BY created_at DESC"
            );
        }

        return $statement->fetchAll();
    }

    public function teachers(): array
    {
        $statement = $this->database->query(
            "SELECT username, first_name, last_name, created_at FROM users WHERE role = 'teacher' ORDER BY created_at DESC"
        );

        return $statement->fetchAll();
    }

    public function classrooms(): array
    {
        $statement = $this->database->query(
            "SELECT c.title, c.grade, u.username AS teacher_username, c.created_at
             FROM classrooms c INNER JOIN users u ON u.id = c.teacher_id
             ORDER BY c.created_at DESC"
        );

        return $statement->fetchAll();
    }

    public function studentCount(): int
    {
        return (int) $this->database->query(
            "SELECT COUNT(*) FROM users WHERE role = 'student'"
        )->fetchColumn();
    }

    public function teacherCount(): int
    {
        return (int) $this->database->query(
            "SELECT COUNT(*) FROM users WHERE role = 'teacher'"
        )->fetchColumn();
    }

    public function publishedPoets(int $limit = 3): array
    {
        $limit = max(1, min(12, $limit));
        return $this->database->query(
            'SELECT name, period, biography, image_url, category
             FROM poets
             WHERE is_published = 1
             ORDER BY created_at DESC, id DESC
             LIMIT ' . $limit
        )->fetchAll();
    }

    public function publishedBooks(int $limit = 20): array
    {
        $limit = max(1, min(12, $limit));
        return $this->database->query(
            'SELECT name, description, image_url, category
             FROM books WHERE is_published = 1
             ORDER BY created_at DESC, id DESC LIMIT ' . $limit
        )->fetchAll();
    }

    public function content(): array
    {
        $statement = $this->database->query('SELECT content_key, content_value FROM site_content');
        $content = [];
        foreach ($statement->fetchAll() as $row) {
            $content[$row['content_key']] = $row['content_value'];
        }

        return $content;
    }

    public function saveContent(array $content): void
    {
        $statement = $this->database->prepare(
            'INSERT INTO site_content (content_key, content_value) VALUES (:content_key, :content_value)
             ON DUPLICATE KEY UPDATE content_value = VALUES(content_value)'
        );
        foreach ($content as $key => $value) {
            $statement->execute(['content_key' => $key, 'content_value' => trim((string) $value)]);
        }
    }

    public function learningPaths(): array
    {
        return $this->database->query(
            'SELECT id, title FROM learning_paths ORDER BY display_order, id'
        )->fetchAll();
    }

    public function publishedLessonCounts(): array
    {
        $statement = $this->database->query(
            'SELECT lp.slug, COUNT(l.id) AS lesson_count
             FROM learning_paths lp
             LEFT JOIN lessons l ON l.learning_path_id = lp.id AND l.is_published = 1
             WHERE lp.is_published = 1
             GROUP BY lp.id, lp.slug'
        );
        $counts = [];
        foreach ($statement->fetchAll() as $row) {
            $counts[$row['slug']] = (int) $row['lesson_count'];
        }

        return $counts;
    }

    public function lessons(): array
    {
        return $this->database->query(
            'SELECT l.title, l.summary, l.is_published, lp.title AS path_title
             FROM lessons l INNER JOIN learning_paths lp ON lp.id = l.learning_path_id
             ORDER BY l.created_at DESC'
        )->fetchAll();
    }

    public function createLesson(int $pathId, string $title, string $summary, string $body, bool $published): bool
    {
        if ($pathId < 1 || trim($title) === '' || trim($body) === '') {
            return false;
        }

        $path = $this->database->prepare('SELECT 1 FROM learning_paths WHERE id = :path_id LIMIT 1');
        $path->execute(['path_id' => $pathId]);
        if (!$path->fetchColumn()) {
            return false;
        }

        $slug = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $title), '-'));
        if ($slug === '') {
            $slug = 'lesson-' . bin2hex(random_bytes(4));
        }

        $statement = $this->database->prepare(
            'INSERT INTO lessons (learning_path_id, title, slug, summary, content, is_published)
             VALUES (:path_id, :title, :slug, :summary, :content, :published)'
        );
        return $statement->execute([
            'path_id' => $pathId,
            'title' => trim($title),
            'slug' => $slug . '-' . bin2hex(random_bytes(3)),
            'summary' => trim($summary),
            'content' => trim($body),
            'published' => $published ? 1 : 0,
        ]);
    }
}