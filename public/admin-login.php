<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: ' . url('admin'));
    exit;
}

$message = '';
$isError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است. صفحه را دوباره باز کن.';
        $isError = true;
    } else {
        $result = (new AuthController(new User($pdo)))->loginAdmin($_POST);
        if (isset($result['error'])) {
            $message = $result['error'];
            $isError = true;
        } else {
            header('Location: ' . url('admin'));
            exit;
        }
    }
}

require __DIR__ . '/../app/views/auth/admin-login.php';