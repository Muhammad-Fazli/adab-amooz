<?php

declare(strict_types=1);

$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
$basePath = trim(dirname(dirname($scriptName)), '/');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestPath = str_replace('\\', '/', $requestPath);

if ($basePath !== '' && str_starts_with($requestPath, '/' . $basePath . '/')) {
    $route = trim(substr($requestPath, strlen('/' . $basePath)), '/');
} elseif ($basePath !== '' && $requestPath === '/' . $basePath) {
    $route = '';
} else {
    $route = trim($requestPath, '/');
}

$routes = [
    '' => __DIR__ . '/../index.html',
    'login' => __DIR__ . '/login.php',
    'register' => __DIR__ . '/register.php',
    'logout' => __DIR__ . '/logout.php',
    'lessons' => __DIR__ . '/lessons.php',
    'practice' => __DIR__ . '/practice.php',
    'grades' => __DIR__ . '/grades.php',
    'assignments' => __DIR__ . '/assignments.php',
    'teacher' => __DIR__ . '/teacher-dashboard.php',
    'teacher/dashboard' => __DIR__ . '/teacher-dashboard.php',
    'teacher/school' => __DIR__ . '/teacher-school.php',
    'profile' => __DIR__ . '/profile-complete.php',
    'profile-complete' => __DIR__ . '/profile-complete.php',
    'school' => __DIR__ . '/student-school.php',
    'library' => __DIR__ . '/../library.html',
    'about' => __DIR__ . '/about.php',
    'poets' => __DIR__ . '/../shayeran.html',
    'shayeran' => __DIR__ . '/../shayeran.html',
    'admin' => __DIR__ . '/admin.php',
    'admin/login' => __DIR__ . '/admin-login.php',
    'admin/users' => __DIR__ . '/admin-section.php',
    'admin/teachers' => __DIR__ . '/admin-section.php',
    'admin/lessons' => __DIR__ . '/admin-section.php',
    'admin/classes' => __DIR__ . '/admin-section.php',
    'admin/poets' => __DIR__ . '/admin-poets.php',
    'admin/books' => __DIR__ . '/admin-books.php',
    'api/me' => __DIR__ . '/me.php',
    'api/home-content' => __DIR__ . '/home-content.php',
    'api/lesson-progress' => __DIR__ . '/lesson-progress.php',
];

if (str_starts_with($route, 'lessons/')) {
    $slug = substr($route, strlen('lessons/'));
    if ($slug !== '') {
        $_GET['slug'] = $slug;
        require __DIR__ . '/lesson.php';
        return;
    }
}

if (preg_match('#^school/grade/(7|8|9|10|11|12)$#', $route, $matches)) {
    $_GET['grade'] = $matches[1];
    require __DIR__ . '/grade-lessons.php';
    return;
}

if (!isset($routes[$route])) {
    http_response_code(404);
    exit('صفحه موردنظر پیدا نشد.');
}

require $routes[$route];