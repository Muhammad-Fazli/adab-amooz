<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'برای ثبت مطالعه ابتدا وارد حساب شو.'], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
    http_response_code(419);
    echo json_encode(['error' => 'درخواست نامعتبر است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$completed = (new LearningPath($pdo))->markLessonCompleted((int) $_SESSION['user_id'], (int) ($_POST['lesson_id'] ?? 0));
if (!$completed) {
    http_response_code(404);
    echo json_encode(['error' => 'درس معتبر یا منتشرشده‌ای پیدا نشد.'], JSON_UNESCAPED_UNICODE);
    exit;
}
echo json_encode(['completed' => true], JSON_UNESCAPED_UNICODE);