<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('login'));
    exit;
}

$practiceController = new PracticeController(new Practice($pdo));
$questionId = (int) ($_GET['question_id'] ?? $_POST['question_id'] ?? 0);
$message = '';
$isCorrect = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است.';
    } else {
        $isCorrect = $practiceController->answer(
            (int) $_SESSION['user_id'],
            $questionId,
            (int) ($_POST['option_id'] ?? 0)
        );
        $message = $isCorrect === null
            ? 'گزینه انتخاب‌شده معتبر نیست.'
            : ($isCorrect ? 'آفرین، پاسخ درست است.' : 'پاسخ درست نبود؛ دوباره مرورش کن.');
    }
}
$question = $practiceController->question($questionId);
require __DIR__ . '/../app/views/practice/show.php';