<?php

require_once __DIR__ . '/../config/bootstrap.php';

$message = '';
$isError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است. صفحه را دوباره باز کن.';
        $isError = true;
    } else {
        $result = (new AuthController(new User($pdo)))->register($_POST);
        if (isset($result['error'])) {
            $message = $result['error'];
            $isError = true;
        } else {
            $_SESSION['flash_message'] = 'ثبت‌نام با موفقیت انجام شد؛ خوش آمدی!';
            $destination = ($_SESSION['user_role'] ?? '') === 'teacher' ? 'teacher/dashboard' : './';
            header('Location: ' . url($destination));
            exit;
        }
    }
}

require __DIR__ . '/../app/views/auth/register.php';