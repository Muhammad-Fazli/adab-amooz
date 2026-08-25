<?php $grades = $grades ?? []; ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نمرات من | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/grades.css')) ?>">
</head>
<body>
    <header class="topbar"><div class="shell nav"><a class="brand" href="<?= e(url()) ?>">ادب آموز</a><a class="back" href="<?= e(url()) ?>">بازگشت به خانه ←</a></div></header>
    <main class="shell page">
        <div class="page-heading"><div><span class="eyebrow">کارنامهٔ یادگیری</span><h1>نمرات و نظر استاد</h1><p>نتیجهٔ آزمون‌ها و بازخورد استاد را اینجا ببین.</p></div><span class="grade-icon">✓</span></div>
        <section class="grades-list" aria-label="فهرست نمرات">
            <?php foreach ($grades as $grade): ?>
                <article class="grade-card">
                    <div class="grade-main"><span class="exam-number"><?= e((string) $grade['id']) ?></span><div><h2><?= e((string) $grade['title']) ?></h2><p><?= e((string) ($grade['description'] ?? '')) ?></p></div></div>
                    <div class="score"><strong><?= $grade['score'] === null ? '---' : e(rtrim(rtrim(number_format((float) $grade['score'], 2, '.', ''), '0'), '.')) ?></strong><span>از <?= e((string) $grade['max_score']) ?></span></div>
                    <div class="feedback"><span>نظر استاد</span><p><?= $grade['feedback'] ? e((string) $grade['feedback']) : 'هنوز نمره‌ای ثبت نشده است.' ?></p><?php if (!empty($grade['teacher_name'])): ?><small><?= e((string) $grade['teacher_name']) ?></small><?php endif; ?></div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
