<?php

declare(strict_types=1);

ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'httponly' => true,
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/School.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/models/LearningPath.php';
require_once __DIR__ . '/../app/controllers/LearningPathController.php';
require_once __DIR__ . '/../app/models/Practice.php';
require_once __DIR__ . '/../app/controllers/PracticeController.php';
require_once __DIR__ . '/../app/models/Grades.php';
require_once __DIR__ . '/../app/controllers/GradesController.php';
require_once __DIR__ . '/../app/models/Assignments.php';
require_once __DIR__ . '/../app/models/TodoTasks.php';
require_once __DIR__ . '/../app/models/TeacherDashboard.php';
require_once __DIR__ . '/../app/controllers/TeacherDashboardController.php';
require_once __DIR__ . '/../app/models/AdminDashboard.php';

function url(string $path = ''): string
{
    static $baseUrl;

    if ($baseUrl === null) {
        $scriptPath = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $baseUrl = str_contains($scriptPath, '/public/')
            ? dirname(dirname($scriptPath))
            : dirname($scriptPath);
        $baseUrl = $baseUrl === '/' || $baseUrl === '.' ? '' : rtrim($baseUrl, '/');

    }

    return $baseUrl . ($path === '' ? '/' : '/' . ltrim($path, '/'));
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}