<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$databaseUser = !empty($_SESSION['user_id'])
    ? (new User($pdo))->findById((int) $_SESSION['user_id'])
    : null;
$username = (string) ($databaseUser['username'] ?? '');
$firstName = (string) ($databaseUser['first_name'] ?? $username);
$lastName = (string) ($databaseUser['last_name'] ?? '');
$flashMessage = $_SESSION['flash_message'] ?? null;
unset($_SESSION['flash_message']);
$progress = ['total_lessons' => 0, 'completed_lessons' => 0];
$practiceSummary = ['total_attempts' => 0, 'correct_attempts' => 0, 'streak_days' => 0];
$activities = [];
if (!empty($_SESSION['user_id'])) {
    $userId = (int) $_SESSION['user_id'];
    $learningPath = new LearningPath($pdo);
    $practice = new Practice($pdo);
    $progress = $learningPath->progressSummary($userId);
    $practiceSummary = $practice->summary($userId);
    foreach ($learningPath->recentCompletedLessons($userId) as $activity) {
        $activities[] = [
            'text' => 'درس «' . $activity['title'] . '» را تمام کردی',
            'date' => $activity['updated_at'],
        ];
    }
    foreach ($practice->recentAttempts($userId) as $activity) {
        $activities[] = [
            'text' => $activity['is_correct'] ? 'به سؤال تمرینی پاسخ درست دادی' : 'یک سؤال تمرینی را مرور کردی',
            'date' => $activity['answered_at'],
        ];
    }
    usort($activities, static fn (array $first, array $second): int => strcmp($second['date'], $first['date']));
    $activities = array_slice($activities, 0, 3);
}

echo json_encode([
    'authenticated' => $databaseUser !== null,
    'role' => (string) ($databaseUser['role'] ?? ''),
    'username' => $username,
    'first_name' => $firstName,
    'last_name' => $lastName,
    'name' => trim($firstName . ' ' . $lastName),
    'state' => (string) ($databaseUser['state'] ?? ''),
    'city' => (string) ($databaseUser['city'] ?? ''),
    'education_level' => (string) ($databaseUser['education_level'] ?? ''),
    'favorite_subject' => (string) ($databaseUser['favorite_subject'] ?? ''),
    'profile_completed' => (bool) ($databaseUser['profile_completed'] ?? false),
    'csrf_token' => $_SESSION['csrf_token'],
    'flash_message' => is_string($flashMessage) ? $flashMessage : null,
    'progress' => $progress,
    'practice' => $practiceSummary,
    'activities' => $activities,
], JSON_UNESCAPED_UNICODE);