<?php $assignments = $assignments ?? []; ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تکالیف من | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/grades.css')) ?>">
</head>
<body>
    <header class="topbar"><div class="shell nav"><a class="brand" href="<?= e(url()) ?>">ادب آموز</a><a class="back" href="<?= e(url()) ?>">بازگشت به خانه ←</a></div></header>
    <main class="shell page">
        <div class="page-heading"><div><span class="eyebrow">کلاس من</span><h1>تکالیف من</h1><p>تکالیف ثبت‌شده توسط استاد را اینجا ببین.</p></div><span class="grade-icon">✎</span></div>
        <section class="grades-list" aria-label="فهرست تکالیف">
            <?php foreach ($assignments as $assignment): ?>
                <article class="assignment-card">
                    <div><h2><?= e((string) $assignment['title']) ?></h2><p><?= e((string) ($assignment['description'] ?? '')) ?></p></div>
                    <div class="assignment-meta"><span>استاد: <?= e(trim((string) $assignment['teacher_first_name'] . ' ' . (string) $assignment['teacher_last_name'])) ?></span><strong><?= $assignment['due_at'] ? 'مهلت: ' . e((string) $assignment['due_at']) : 'بدون مهلت' ?></strong></div>
                </article>
            <?php endforeach; ?>
            <?php if (!$assignments): ?><div class="empty-assignment">هنوز تکلیفی برای شما ثبت نشده است.</div><?php endif; ?>
        </section>
    </main>
</body>
</html>
