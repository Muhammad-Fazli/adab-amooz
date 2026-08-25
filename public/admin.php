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

$dashboard = new AdminDashboard($pdo);
$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است.';
        $isError = true;
    } else {
        if (($_POST['action'] ?? '') === 'create_lesson') {
            $pathId = (int) ($_POST['path_id'] ?? 0);
            $title = trim((string) ($_POST['lesson_title'] ?? ''));
            $body = trim((string) ($_POST['lesson_content'] ?? ''));
            if ($pathId < 1 || $title === '' || $body === '') {
                $message = 'عنوان، مسیر آموزشی و متن درس را کامل کن.';
                $isError = true;
            } else {
                $created = $dashboard->createLesson($pathId, $title, (string) ($_POST['lesson_summary'] ?? ''), $body, isset($_POST['is_published']));
                $message = $created ? 'آموزش با موفقیت ثبت شد.' : 'ثبت آموزش انجام نشد؛ مسیر انتخاب‌شده معتبر نیست.';
                $isError = !$created;
            }
        }
    }
}

$students = $dashboard->students();
$teacherCount = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher'")->fetchColumn();
$classroomCount = (int) $pdo->query('SELECT COUNT(*) FROM classrooms')->fetchColumn();
$learningPaths = $dashboard->learningPaths();
$lessons = $dashboard->lessons();
$adminName = trim((string) ($admin['first_name'] ?? '')) ?: (string) $admin['username'];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت سایت | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@500;600;700;800&family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/admin.css')) ?>">
</head>
<body>
    <aside class="admin-sidebar">
        <a class="sidebar-brand" href="<?= e(url('admin')) ?>"><span>ب</span><strong>ادب آموز</strong><small>پنل مدیریت</small></a>
        <nav aria-label="منوی مدیریت">
            <a class="active" href="#overview"><span class="nav-icon" aria-hidden="true">▦</span> داشبورد</a>
            <a href="#lessons"><span class="nav-icon" aria-hidden="true">▤</span> آموزش‌ها</a>
            <a href="#students"><span class="nav-icon" aria-hidden="true">♙</span> کاربران</a>
            <a href="<?= e(url('admin/poets')) ?>"><span class="nav-icon" aria-hidden="true">♧</span> نویسندگان و شاعران</a>
        </nav>
        <div class="sidebar-tools">
            <a href="<?= e(url()) ?>"><span aria-hidden="true">⌂</span> مشاهده سایت</a>
            <form method="post" action="<?= e(url('logout')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="logout" type="submit"><span aria-hidden="true">↪</span> خروج</button></form>
        </div>
        <div class="sidebar-footer"><span class="admin-avatar"><?= e(mb_substr($adminName, 0, 1)) ?></span><div><strong><?= e($adminName) ?></strong><small>مدیر سامانه</small></div></div>
    </aside>
    <main class="shell">
        <section class="intro" id="overview"><div><span class="eyebrow">نمای کلی سامانه</span><h1>سلام، <b><?= e($adminName) ?></b></h1><p>محتوای سایت و فعالیت کاربران را از اینجا مدیریت کن.</p></div></section>
        <?php if ($message): ?><p class="message <?= $isError ? 'error' : 'success' ?>"><?= e($message) ?></p><?php endif; ?>
        <section class="stats-grid" aria-label="آمار سامانه">
            <a class="stat-card stat-blue" href="<?= e(url('admin/users')) ?>"><span class="stat-icon" aria-hidden="true">♙</span><div><strong><?= count($students) ?></strong><span>کاربران فعال</span></div><small>مشاهده کاربران</small></a>
            <a class="stat-card stat-green" href="<?= e(url('admin/lessons')) ?>"><span class="stat-icon" aria-hidden="true">▤</span><div><strong><?= count($lessons) ?></strong><span>آموزش‌ها</span></div><small>مدیریت کتابخانه</small></a>
            <a class="stat-card stat-gold" href="<?= e(url('admin/lessons')) ?>"><span class="stat-icon" aria-hidden="true">▦</span><div><strong><?= count($learningPaths) ?></strong><span>مسیر یادگیری</span></div><small>مشاهده آموزش‌ها</small></a>
            <a class="stat-card stat-coral" href="<?= e(url('admin/teachers')) ?>"><span class="stat-icon" aria-hidden="true">♧</span><div><strong><?= $teacherCount ?></strong><span>معلمان</span></div><small>مشاهده معلمان</small></a>
            <a class="stat-card stat-blue" href="<?= e(url('admin/users')) ?>"><span class="stat-icon" aria-hidden="true">♙</span><div><strong><?= count($students) ?></strong><span>دانش‌آموزان ثبت‌شده</span></div><small>مشاهده دانش‌آموزان</small></a>
            <a class="stat-card stat-teal" href="<?= e(url('admin/classes')) ?>"><span class="stat-icon" aria-hidden="true">▤</span><div><strong><?= $classroomCount ?></strong><span>کلاس‌ها</span></div><small>کلاس‌های ساخته‌شده</small></a>
        </section>
        <section class="dashboard-grid">
            <a class="stat-card stat-coral" href="<?= e(url('admin/poets')) ?>"><span class="stat-icon" aria-hidden="true">♧</span><div><strong>+</strong><span>معرفی نویسندگان و شاعران</span></div><small>افزودن محتوا</small></a>
            <a class="stat-card stat-green" href="<?= e(url('admin/books')) ?>"><span class="stat-icon" aria-hidden="true">▤</span><div><strong>+</strong><span>معرفی کتاب‌ها</span></div><small>افزودن محتوا</small></a>
        </section>
        <section class="dashboard-grid lessons-area" id="lessons">
            <article class="panel editor">
                <div class="panel-head"><div><span class="eyebrow">آموزش‌ها</span><h2>بارگذاری آموزش جدید</h2></div><span class="live">● انتشار</span></div>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_lesson">
                    <label>مسیر آموزشی<select name="path_id" required><option value="">انتخاب مسیر</option><?php foreach ($learningPaths as $path): ?><option value="<?= (int) $path['id'] ?>"><?= e($path['title']) ?></option><?php endforeach; ?></select></label>
                    <label>عنوان آموزش<input name="lesson_title" required placeholder="مثلاً تشبیه چیست؟"></label>
                    <label>خلاصه آموزش<input name="lesson_summary" placeholder="یک توضیح کوتاه برای دانش‌آموزان"></label>
                    <label>متن کامل آموزش<textarea name="lesson_content" rows="7" required placeholder="متن آموزش را اینجا بنویس..."></textarea></label>
                    <label class="check-row"><input type="checkbox" name="is_published" checked> همین حالا برای دانش‌آموزان منتشر شود</label>
                    <button type="submit">ثبت و انتشار آموزش <span>←</span></button>
                </form>
            </article>
            <article class="panel students">
                <div class="panel-head"><div><span class="eyebrow">کتابخانه آموزش</span><h2>آموزش‌های ثبت‌شده</h2></div><span class="badge"><?= count($lessons) ?></span></div>
                <div class="lesson-list"><?php foreach ($lessons as $lesson): ?><div class="lesson-row"><div><strong><?= e($lesson['title']) ?></strong><small><?= e($lesson['path_title']) ?></small></div><span class="lesson-status <?= $lesson['is_published'] ? 'published' : 'draft' ?>"><?= $lesson['is_published'] ? 'منتشرشده' : 'پیش‌نویس' ?></span></div><?php endforeach; ?><?php if (!$lessons): ?><p class="empty">هنوز آموزشی ثبت نشده است.</p><?php endif; ?></div>
            </article>
        </section>
    </main>
</body>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>
