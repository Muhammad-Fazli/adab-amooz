<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'teacher') {
    http_response_code(403);
    exit('دسترسی به بخش مدرسه مجاز نیست.');
}

$teacher = (new User($pdo))->findById((int) $_SESSION['user_id']);
if ($teacher === null || $teacher['role'] !== 'teacher') {
    http_response_code(403);
    exit('دسترسی به بخش مدرسه مجاز نیست.');
}

$schools = new School($pdo);
$schoolList = $schools->listForTeacher((int) $teacher['id']);
$schoolClasses = [];
foreach ($schoolList as $schoolItem) {
    $schoolClasses[(int) $schoolItem['id']] = $schools->classesForSchool((int) $teacher['id'], (int) $schoolItem['id']);
}
$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است.';
        $isError = true;
    } else {
        $saved = $schools->saveForTeacher(
            (int) $teacher['id'],
            (string) ($_POST['name'] ?? ''),
            (string) ($_POST['address'] ?? '')
        );
        if ($saved) {
            $schoolList = $schools->listForTeacher((int) $teacher['id']);
            $schoolClasses = [];
            foreach ($schoolList as $schoolItem) {
                $schoolClasses[(int) $schoolItem['id']] = $schools->classesForSchool((int) $teacher['id'], (int) $schoolItem['id']);
            }
            $message = 'اطلاعات مدرسه با موفقیت ذخیره شد.';
        } else {
            $message = 'نام مدرسه الزامی است و طول اطلاعات را بررسی کن.';
            $isError = true;
        }
    }
}

$teacherName = trim((string) ($teacher['first_name'] ?? '')) ?: (string) $teacher['username'];
require __DIR__ . '/../app/views/teacher/school.php';