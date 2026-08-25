<?php

declare(strict_types=1);

final class LearningPath
{
    public function __construct(private PDO $database)
    {
    }

    public function published(): array
    {
        $statement = $this->database->query(
            'SELECT id, title, slug, description, level FROM learning_paths WHERE is_published = 1 ORDER BY display_order, id'
        );

        return $statement->fetchAll();
    }

    public function findPublishedBySlug(string $slug): ?array
    {
        $statement = $this->database->prepare(
            'SELECT id, title, slug, description, level FROM learning_paths
             WHERE slug = :slug AND is_published = 1 LIMIT 1'
        );
        $statement->execute(['slug' => $slug]);
        $path = $statement->fetch();

        return $path ?: null;
    }

    public function publishedLessons(int $pathId, ?int $userId = null): array
    {
        $statement = $this->database->prepare(
              'SELECT l.id, l.title, l.slug, l.summary, l.content,
                    COALESCE(ulp.status, "not_started") AS progress_status
               FROM lessons l
               LEFT JOIN user_lesson_progress ulp ON ulp.lesson_id = l.id AND ulp.user_id = :user_id
               WHERE l.learning_path_id = :path_id AND l.is_published = 1
             ORDER BY display_order, id'
        );
           $statement->execute(['path_id' => $pathId, 'user_id' => $userId ?? 0]);

        return $statement->fetchAll();
    }

    public function markLessonCompleted(int $userId, int $lessonId): bool
    {
        $lesson = $this->database->prepare('SELECT 1 FROM lessons WHERE id = :lesson_id AND is_published = 1 LIMIT 1');
        $lesson->execute(['lesson_id' => $lessonId]);
        if (!$lesson->fetchColumn()) {
            return false;
        }

        $statement = $this->database->prepare(
            'INSERT INTO user_lesson_progress (user_id, lesson_id, status, completed_at)
             VALUES (:user_id, :lesson_id, "completed", NOW())
             ON DUPLICATE KEY UPDATE status = "completed", completed_at = NOW()'
        );
        $statement->execute(['user_id' => $userId, 'lesson_id' => $lessonId]);
        return true;
    }

    public function progressSummary(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(l.id) AS total_lessons,
                    COUNT(CASE WHEN ulp.status = "completed" THEN 1 END) AS completed_lessons
             FROM lessons l
             LEFT JOIN user_lesson_progress ulp ON ulp.lesson_id = l.id AND ulp.user_id = :user_id
             WHERE l.is_published = 1'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetch() ?: ['total_lessons' => 0, 'completed_lessons' => 0];
    }

    public function recentCompletedLessons(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT l.title, ulp.updated_at
             FROM user_lesson_progress ulp
             INNER JOIN lessons l ON l.id = ulp.lesson_id
             WHERE ulp.user_id = :user_id AND ulp.status = "completed"
             ORDER BY ulp.updated_at DESC
             LIMIT 3'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }

    public function createLesson(int $teacherId, int $pathId, string $title, string $summary, string $content): bool
    {
        if ($pathId < 1 || trim($title) === '' || trim($content) === '') {
            return false;
        }
        $slug = trim(preg_replace('/[^a-z0-9]+/i', '-', strtolower($title)), '-');
        $slug = $slug !== '' ? $slug . '-' . time() : 'lesson-' . time();
        $statement = $this->database->prepare(
            'INSERT INTO lessons (learning_path_id, teacher_id, title, slug, summary, content, display_order, is_published)
             VALUES (:path_id, :teacher_id, :title, :slug, :summary, :content, 0, 1)'
        );
        return $statement->execute([
            'path_id' => $pathId,
            'teacher_id' => $teacherId,
            'title' => trim($title),
            'slug' => $slug,
            'summary' => trim($summary) !== '' ? trim($summary) : null,
            'content' => trim($content),
        ]);
    }

    public function publicRecentLessons(int $limit = 6): array
    {
        $limit = max(1, min(20, $limit));
        $statement = $this->database->query(
            'SELECT l.title, l.summary, p.slug AS path_slug, p.title AS path_title,
                    CONCAT(u.first_name, " ", u.last_name) AS teacher_name
             FROM lessons l
             INNER JOIN learning_paths p ON p.id = l.learning_path_id
             LEFT JOIN users u ON u.id = l.teacher_id
             WHERE l.is_published = 1
             ORDER BY l.created_at DESC, l.id DESC
             LIMIT ' . $limit
        );
        return $statement->fetchAll();
    }
}