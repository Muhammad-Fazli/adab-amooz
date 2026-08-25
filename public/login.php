<?php

require_once __DIR__ . '/../config/bootstrap.php';

$message = isset($_GET['registered']) ? 'حساب ساخته شد؛ حالا وارد شو.' : '';
$isError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است. صفحه را دوباره باز کن.';
        $isError = true;
    } else {
        $result = (new AuthController(new User($pdo)))->login($_POST);
        if (isset($result['error'])) {
            $message = $result['error'];
            $isError = true;
        } else {
            $destination = match ($_SESSION['user_role'] ?? '') {
                'admin' => 'admin',
                'teacher' => 'teacher/dashboard',
                default => './',
            };
            header('Location: ' . url($destination));
            exit;
        }
    }
}

require __DIR__ . '/../app/views/auth/login.php';