<?php
require_once __DIR__ . '/../config/bootstrap.php';
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>درباره ما | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/about.css')) ?>">
</head>
<body>
    <header>
        <div class="about-shell about-nav">
            <a class="brand" href="<?= e(url()) ?>"><span class="brand-mark">ب</span><span>ادب آموز</span></a>
            <a class="back-link" href="<?= e(url()) ?>">بازگشت به خانه ←</a>
        </div>
    </header>
    <main>
        <section class="about-hero">
            <div class="about-shell">
                <div class="eyebrow">دربارهٔ ادب آموز</div>
                <h1>جایی برای نزدیک‌تر شدن<br><span>به زبان فارسی</span></h1>
                <p>ادب آموز یک فضای یادگیری برای دوست‌داران ادبیات فارسی است؛ از نخستین واژه‌ها تا جهان شعر، داستان و اندیشه.</p>
            </div>
        </section>
        <section class="about-shell about-grid">
            <article class="about-card about-story">
                <span class="about-icon">✦</span>
                <h2>چرا ادب آموز؟</h2>
                <p>یادگیری ادبیات وقتی ماندگار می‌شود که با خواندن، تمرین و کشف همراه باشد. ما آموزش‌ها را کوتاه و روشن طراحی کرده‌ایم تا هر روز بتوانی قدمی تازه برداری.</p>
            </article>
            <article class="about-card about-values">
                <span class="about-icon coral">◈</span>
                <h2>در ادب آموز چه می‌بینی؟</h2>
                <ul>
                    <li>مسیرهای یادگیری ساده و مرحله‌به‌مرحله</li>
                    <li>تمرین‌های کوتاه برای سنجش آموخته‌ها</li>
                    <li>معرفی شاعران، نویسندگان و کتاب‌های ماندگار</li>
                </ul>
            </article>
        </section>
        <section class="about-shell about-quote">
            <p>«ادبیات، راهی است برای بهتر دیدن جهان و خودمان.»</p>
            <span>با مهر، تیم ادب آموز</span>
        </section>
    </main>
    <footer>
        <div class="about-shell">ادب آموز · برای دوست‌داران زبان فارسی</div>
    </footer>
</body>
</html>
