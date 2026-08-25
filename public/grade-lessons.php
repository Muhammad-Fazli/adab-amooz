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

$grades = ['7' => 'هفتم', '8' => 'هشتم', '9' => 'نهم', '10' => 'دهم', '11' => 'یازدهم', '12' => 'دوازدهم'];
$grade = (string) ($_GET['grade'] ?? '');
if (!isset($grades[$grade])) {
    header('Location: ' . url('school'));
    exit;
}

$statement = $pdo->query('SELECT l.title, l.summary, l.slug, p.title AS path_title, p.slug AS path_slug FROM lessons l INNER JOIN learning_paths p ON p.id = l.learning_path_id WHERE l.is_published = 1 ORDER BY p.display_order, l.display_order, l.id');
$lessons = $statement->fetchAll();
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ادبیات پایه <?= e($grades[$grade]) ?> | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/student-school.css')) ?>">
</head>
<body>
    <header><div class="school-shell school-nav"><a class="brand" href="<?= e(url()) ?>"><span>ب</span> ادب آموز</a><a class="back-link" href="<?= e(url('school')) ?>">بازگشت به مدرسه من ←</a></div></header>
    <main class="school-shell">
        <section class="school-hero compact"><span class="eyebrow">درس‌های ادبیات فارسی</span><h1>پایه <?= e($grades[$grade]) ?></h1><p>درس‌های منتشرشدهٔ ادبیات را از مسیرهای یادگیری انتخاب کن.</p></section>
        <section class="lessons-section"><div class="section-heading"><div><span class="eyebrow">فهرست درس‌ها</span><h2>ادبیات پایه <?= e($grades[$grade]) ?></h2></div><span class="grade-count"><?= count($lessons) ?> درس</span></div><div class="lesson-grid">
            <?php foreach ($lessons as $lesson): ?><a class="lesson-card" href="<?= e(url('lessons/' . $lesson['path_slug'])) ?>"><span class="lesson-icon">◈</span><small><?= e($lesson['path_title']) ?></small><h3><?= e($lesson['title']) ?></h3><p><?= e((string) ($lesson['summary'] ?? 'برای یادگیری بهتر این درس را شروع کن.')) ?></p><b>شروع درس ←</b></a><?php endforeach; ?>
            <?php if (!$lessons): ?><p class="empty-state">هنوز درسی برای این پایه منتشر نشده است.</p><?php endif; ?>
        </div></section>
    </main>
    <footer><div class="school-shell">ادب آموز · یادگیری ادبیات برای هر پایه</div></footer>
</body>
</html>
