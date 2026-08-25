<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$content = (new AdminDashboard($pdo))->content();
$lessonCounts = (new AdminDashboard($pdo))->publishedLessonCounts();
$studentCount = (new AdminDashboard($pdo))->studentCount();
$teacherCount = (new AdminDashboard($pdo))->teacherCount();
$publishedPoets = (new AdminDashboard($pdo))->publishedPoets();
$publishedBooks = (new AdminDashboard($pdo))->publishedBooks();
$publicLessons = (new LearningPath($pdo))->publicRecentLessons();
$resolveImage = static function (array $entry): array {
    $entry['image_url'] = asset_url($entry['image_url'] ?? null);
    return $entry;
};
$publishedPoets = array_map($resolveImage, $publishedPoets);
$publishedBooks = array_map($resolveImage, $publishedBooks);
echo json_encode([
    'hero_title' => $content['hero_title'] ?? '',
    'hero_description' => $content['hero_description'] ?? '',
    'lesson_counts' => $lessonCounts,
    'student_count' => $studentCount,
    'teacher_count' => $teacherCount,
    'published_poets' => $publishedPoets,
    'published_books' => $publishedBooks,
    'public_lessons' => $publicLessons,
], JSON_UNESCAPED_UNICODE);
