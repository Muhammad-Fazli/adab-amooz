<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('login'));
    exit;
}

$user = (new User($pdo))->findById((int) $_SESSION['user_id']);
$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است.';
        $isError = true;
    } else {
        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));
        $state = trim((string) ($_POST['state'] ?? ''));
        $city = trim((string) ($_POST['city'] ?? ''));
        $educationLevel = trim((string) ($_POST['education_level'] ?? ''));
        $educationLevel = strtr($educationLevel, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);
        $favoriteSubject = trim((string) ($_POST['favorite_subject'] ?? ''));

        $allowedEducationLevels = ['7', '8', '9', '10', '11', '12'];
        if ($firstName === '' || $state === '' || $city === '' || !in_array($educationLevel, $allowedEducationLevels, true)) {
            $message = 'لطفاً تمام اطلاعات الزامی را کامل کن.';
            $isError = true;
        } else {
            try {
                (new User($pdo))->updateProfile((int) $_SESSION['user_id'], [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'state' => $state,
                    'city' => $city,
                    'education_level' => 'پایه ' . $educationLevel,
                    'favorite_subject' => $favoriteSubject,
                ]);

                $_SESSION['flash_message'] = 'پروفایل‌ت با موفقیت به‌روزرسانی شد!';
                header('Location: ' . url('profile'));
                exit;
            } catch (PDOException $exception) {
                $message = 'خطایی پیش آمد. دوباره تلاش کن.';
                $isError = true;
            }
        }
    }
}

require __DIR__ . '/../app/views/auth/profile-complete.php';
