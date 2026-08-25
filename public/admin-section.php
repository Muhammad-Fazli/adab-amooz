<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ' . url('admin/login'));
    exit;
}

$admin = (new User($pdo))->findById((int) $_SESSION['user_id']);
if ($admin === null || $admin['role'] !== 'admin') {
    http_response_code(403);
    exit('دسترسی به مدیریت مجاز نیست.');
}

$requestedSection = (string) ($_GET['section'] ?? 'users');
$section = in_array($requestedSection, ['users', 'teachers', 'lessons', 'classes'], true) ? $requestedSection : 'users';
$requestedFilter = (string) ($_GET['filter'] ?? 'all');
$userFilter = in_array($requestedFilter, ['all', 'student', 'teacher'], true) ? $requestedFilter : 'all';
$dashboard = new AdminDashboard($pdo);
$adminName = trim((string) ($admin['first_name'] ?? '')) ?: (string) $admin['username'];
$items = match ($section) {
    'teachers' => $dashboard->teachers(),
    'lessons' => $dashboard->lessons(),
    'classes' => $dashboard->classrooms(),
    default => $dashboard->users($userFilter === 'all' ? null : $userFilter),
};

require __DIR__ . '/../app/views/admin/section.php';