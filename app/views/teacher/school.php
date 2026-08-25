<?php
$message = $message ?? '';
$isError = $isError ?? false;
$teacherName = $teacherName ?? '';
$schoolList = $schoolList ?? [];
$schoolClasses = $schoolClasses ?? [];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدرسه من | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/teacher.css')) ?>">
</head>
<body>
    <div class="school-page">
        <header class="school-page-header"><a class="brand" href="<?= e(url('teacher/dashboard')) ?>"><span class="brand-mark">ب</span> ادب آموز</a><a class="home-link" href="<?= e(url('teacher/dashboard')) ?>">بازگشت به پنل ←</a></header>
        <main class="school-content">
            <div class="school-heading"><span class="eyebrow">مدیریت مدرسه</span><h1>مدرسه من</h1><p>اطلاعات مدرسه‌ات را ثبت کن تا در پنل معلم نگهداری شود.</p></div>
            <?php if ($message): ?><p class="grade-message <?= $isError ? 'school-error' : '' ?>"><?= e($message) ?></p><?php endif; ?>
            <section class="school-form-panel">
                <div class="school-form-icon">⌂</div><div><h2>افزودن مدرسه</h2><p>معلم: <?= e($teacherName) ?></p></div>
                <form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><label>نام مدرسه<input name="name" type="text" maxlength="180" required placeholder="مثلاً دبیرستان ادب"></label><label>آدرس مدرسه <span>اختیاری</span><textarea name="address" maxlength="255" rows="4" placeholder="استان، شهر و نشانی مدرسه"></textarea></label><button type="submit">افزودن مدرسه <span>←</span></button></form>
            </section>
            <section class="school-list-panel"><div class="school-list-heading"><div><span class="eyebrow">اطلاعات ذخیره‌شده</span><h2>مدرسه‌های من</h2></div><strong><?= count($schoolList) ?></strong></div><?php if ($schoolList): ?><div class="school-list"><?php foreach ($schoolList as $savedSchool): $classes = $schoolClasses[(int) $savedSchool['id']] ?? []; ?><details class="school-list-item"><summary><span class="school-list-icon">⌂</span><span><strong><?= e((string) $savedSchool['name']) ?></strong><small><?= e((string) ($savedSchool['address'] ?: 'آدرس ثبت نشده')) ?></small></span><time datetime="<?= e((string) $savedSchool['created_at']) ?>"><?= e(date('Y/m/d', strtotime((string) $savedSchool['created_at']))) ?></time></summary><div class="school-class-list"><strong>لیست کلاس‌ها</strong><?php if ($classes): ?><ul><?php foreach ($classes as $class): ?><li><span><?= e((string) $class['title']) ?></span><small><?= e((string) ($class['grade'] ?: 'پایه ثبت نشده')) ?></small></li><?php endforeach; ?></ul><?php else: ?><p>هنوز کلاسی برای این مدرسه ثبت نشده است.</p><?php endif; ?></div></details><?php endforeach; ?></div><?php else: ?><p class="school-empty">هنوز مدرسه‌ای ثبت نشده است.</p><?php endif; ?></section>
        </main>
    </div>
</body>
</html>