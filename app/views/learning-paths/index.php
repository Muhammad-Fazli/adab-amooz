<?php $learningPaths = $learningPaths ?? []; ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>آموزش‌ها | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/amoozesh.css')) ?>">
</head>
<body>
    <header>
        <div class="shell nav"><a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a><a class="back" href="<?= e(url()) ?>">بازگشت به خانه ←</a></div>
    </header>
    <main>
        <section class="shell hero">
            <div><div class="eyebrow">مسیرهای یادگیری</div><h1>یاد بگیر،<br><span class="coral-text">کشف کن.</span></h1><p>درس‌های کوتاه و کاربردی ادبیات فارسی را انتخاب کن و با ریتم خودت پیش برو.</p></div>
            <div class="stats"><div class="stat"><strong><?= count($learningPaths) ?></strong><small>موضوع اصلی</small></div></div>
        </section>
        <section class="shell grid">
            <?php foreach ($learningPaths as $learningPath): ?>
                <a class="card" href="<?= e(url('lessons/' . $learningPath['slug'])) ?>">
                    <div class="icon">✺</div>
                    <h2><?= e($learningPath['title']) ?></h2>
                    <p><?= e((string) ($learningPath['description'] ?? '')) ?></p>
                    <div class="meta"><span><?= e($learningPath['level']) ?></span><span class="start">شروع ←</span></div>
                </a>
            <?php endforeach; ?>
        </section>
    </main>
</body>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>