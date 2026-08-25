<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('login'));
    exit;
}

$user = (new User($pdo))->findById((int) $_SESSION['user_id']);
if (!$user || $user['role'] !== 'student') {
    header('Location: ' . url());
    exit;
}

$grades = [
    '7' => 'هفتم',
    '8' => 'هشتم',
    '9' => 'نهم',
    '10' => 'دهم',
    '11' => 'یازدهم',
    '12' => 'دوازدهم',
];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدرسه من | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/student-school.css')) ?>">
</head>
<body>
    <header><div class="school-shell school-nav"><a class="brand" href="<?= e(url()) ?>"><span>ب</span> ادب آموز</a><a class="back-link" href="<?= e(url()) ?>">بازگشت به خانه ←</a></div></header>
    <main class="school-shell">
        <section class="school-hero"><span class="eyebrow">مسیر یادگیری من</span><h1>مدرسه من</h1><p>پایهٔ تحصیلی‌ات را انتخاب کن تا درس‌های ادبیات فارسی را ببینی.</p></section>
        <section class="grade-section"><div class="section-heading"><div><span class="eyebrow">دورهٔ اول</span><h2>متوسطه اول</h2></div><span class="grade-count">۳ پایه</span></div><div class="grade-grid">
            <?php foreach (['7', '8', '9'] as $grade): ?><a class="grade-card first" href="<?= e(url('school/grade/' . $grade)) ?>"><span class="grade-icon"><?= e($grade) ?></span><strong>پایه <?= e($grades[$grade]) ?></strong><small>درس‌های ادبیات فارسی</small><b>مشاهده درس‌ها ←</b></a><?php endforeach; ?>
        </div></section>
        <section class="grade-section second"><div class="section-heading"><div><span class="eyebrow">دورهٔ دوم</span><h2>متوسطه دوم</h2></div><span class="grade-count">۳ پایه</span></div><div class="grade-grid">
            <?php foreach (['10', '11', '12'] as $grade): ?><a class="grade-card second" href="<?= e(url('school/grade/' . $grade)) ?>"><span class="grade-icon"><?= e($grade) ?></span><strong>پایه <?= e($grades[$grade]) ?></strong><small>درس‌های ادبیات فارسی</small><b>مشاهده درس‌ها ←</b></a><?php endforeach; ?>
        </div></section>
    </main>
    <footer><div class="school-shell">ادب آموز · هر درس، یک قدم نزدیک‌تر به دنیای کلمه‌ها</div></footer>
</body>
</html>
