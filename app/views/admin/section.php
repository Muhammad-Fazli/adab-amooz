<?php
$section = $section ?? 'users';
$userFilter = $userFilter ?? 'all';
$adminName = $adminName ?? '';
$items = $items ?? [];
$titles = [
    'users' => ['کاربران', 'فهرست کاربران سامانه'],
    'teachers' => ['معلمان', 'فهرست معلمان ثبت‌نام‌شده'],
    'lessons' => ['آموزش‌ها', 'کتابخانه آموزش‌های سامانه'],
    'classes' => ['کلاس‌ها', 'فهرست کلاس‌های ساخته‌شده'],
];
$title = $titles[$section][0] ?? $titles['users'][0];
$subtitle = $titles[$section][1] ?? $titles['users'][1];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@500;600;700;800&family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/admin.css')) ?>">
</head>
<body>
    <aside class="admin-sidebar">
        <a class="sidebar-brand" href="<?= e(url('admin')) ?>"><span>ب</span><strong>ادب آموز</strong><small>پنل مدیریت</small></a>
        <nav aria-label="منوی مدیریت">
            <a href="<?= e(url('admin')) ?>"><span>▦</span> داشبورد</a>
            <a class="<?= $section === 'users' ? 'active' : '' ?>" href="<?= e(url('admin/users')) ?>"><span>♙</span> کاربران</a>
            <a class="<?= $section === 'teachers' ? 'active' : '' ?>" href="<?= e(url('admin/teachers')) ?>"><span>♧</span> معلمان</a>
            <a class="<?= $section === 'lessons' ? 'active' : '' ?>" href="<?= e(url('admin/lessons')) ?>"><span>▤</span> آموزش‌ها</a>
            <a class="<?= $section === 'classes' ? 'active' : '' ?>" href="<?= e(url('admin/classes')) ?>"><span>▤</span> کلاس‌ها</a>
        </nav>
        <div class="sidebar-tools"><a href="<?= e(url()) ?>">مشاهده سایت <span>⌂</span></a><form method="post" action="<?= e(url('logout')) ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="logout" type="submit">خروج <span>↪</span></button></form></div>
        <div class="sidebar-footer"><span class="admin-avatar"><?= e(mb_substr($adminName, 0, 1)) ?></span><div><strong><?= e($adminName) ?></strong><small>مدیر سامانه</small></div></div>
    </aside>
    <main class="shell section-page">
        <div class="page-heading"><div><span class="eyebrow">مدیریت سامانه</span><h1><?= e($title) ?></h1><p><?= e($subtitle) ?></p></div><a class="back-link" href="<?= e(url('admin')) ?>">بازگشت به داشبورد ←</a></div>
        <?php if ($section === 'users'): ?><div class="role-cards"><a class="role-card role-all <?= $userFilter === 'all' ? 'active' : '' ?>" href="<?= e(url('admin/users')) ?>"><span>♙</span><strong>همه کاربران</strong><small>دانش‌آموزان و معلمان</small></a><a class="role-card role-student <?= $userFilter === 'student' ? 'active' : '' ?>" href="<?= e(url('admin/users?filter=student')) ?>"><span>♙</span><strong>دانش‌آموزان</strong><small>مشاهده فهرست دانش‌آموزان</small></a><a class="role-card role-teacher <?= $userFilter === 'teacher' ? 'active' : '' ?>" href="<?= e(url('admin/users?filter=teacher')) ?>"><span>♧</span><strong>معلمان</strong><small>مشاهده فهرست معلمان</small></a></div><?php endif; ?>
        <section class="panel section-panel">
            <div class="panel-head"><div><span class="eyebrow">فهرست <?= e($title) ?></span><h2><?= count($items) ?> مورد ثبت شده</h2></div><span class="badge"><?= count($items) ?></span></div>
            <div class="table-wrap"><table><thead><tr><?php if ($section === 'lessons'): ?><th>عنوان آموزش</th><th>مسیر</th><th>وضعیت</th><?php elseif ($section === 'classes'): ?><th>عنوان کلاس</th><th>معلم</th><th>پایه</th><th>تاریخ ایجاد</th><?php else: ?><th>نام کاربری</th><th>نام نمایشی</th><th>تاریخ ثبت‌نام</th><?php if ($section === 'users'): ?><th>نقش</th><?php endif; ?><?php endif; ?></tr></thead><tbody>
                <?php foreach ($items as $item): ?><tr><?php if ($section === 'lessons'): ?><td><span class="student-dot">●</span><?= e((string) $item['title']) ?></td><td><?= e((string) $item['path_title']) ?></td><td><span class="lesson-status <?= $item['is_published'] ? 'published' : 'draft' ?>"><?= $item['is_published'] ? 'منتشرشده' : 'پیش‌نویس' ?></span></td><?php elseif ($section === 'classes'): ?><td><span class="student-dot">●</span><?= e((string) $item['title']) ?></td><td><?= e((string) $item['teacher_username']) ?></td><td><?= e((string) ($item['grade'] ?? 'ثبت نشده')) ?></td><td><?= e(date('Y/m/d', strtotime($item['created_at']))) ?></td><?php else: ?><td><span class="student-dot">●</span><?= e((string) $item['username']) ?></td><td><?= e(trim((string) ($item['first_name'] ?? '') . ' ' . (string) ($item['last_name'] ?? ''))) ?: 'ثبت نشده' ?></td><td><?= e(date('Y/m/d', strtotime($item['created_at']))) ?></td><?php if ($section === 'users'): ?><td><?= $item['role'] === 'teacher' ? 'معلم' : 'دانش‌آموز' ?></td><?php endif; ?><?php endif; ?></tr><?php endforeach; ?>
                <?php if (!$items): ?><tr><td colspan="4" class="empty">هنوز موردی ثبت نشده است.</td></tr><?php endif; ?>
            </tbody></table></div>
        </section>
    </main>
</body>
</html>