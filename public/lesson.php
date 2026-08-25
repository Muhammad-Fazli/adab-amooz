<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$learningPathModel = new LearningPath($pdo);
$slug = trim((string) ($_GET['slug'] ?? ''));
$learningPath = $learningPathModel->findPublishedBySlug($slug);

if ($learningPath === null) {
    http_response_code(404);
    echo 'صفحه موردنظر پیدا نشد.';
    exit;
}

$lessons = $learningPathModel->publishedLessons((int) $learningPath['id'], !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null);
require __DIR__ . '/../app/views/learning-paths/show.php';