<!doctype html>
<?php $isAuthenticated = !empty($_SESSION['user_id']); ?>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($learningPath['title']) ?> | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@500;600;700;800&family=Vazirmatn:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/amoozesh.css')) ?>">
</head>
<body>
    <header><div class="shell nav"><a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a><a class="back" href="<?= e(url('lessons')) ?>">بازگشت به آموزش‌ها ←</a></div></header>
    <main>
        <section class="shell lesson-hero"><div><div class="eyebrow">مسیر یادگیری</div><h1><?= e($learningPath['title']) ?></h1><p><?= e((string) ($learningPath['description'] ?? '')) ?></p></div><span class="lesson-level"><?= e($learningPath['level']) ?></span></section>
        <section class="shell lesson-content"><div class="section-heading"><div><div class="eyebrow">فهرست درس‌ها</div><h2>قدم‌به‌قدم پیش برو</h2></div><span><?= count($lessons) ?> درس منتشرشده</span></div><?php if ($lessons): ?><div class="lesson-cards"><?php $lessonNumber = 1; foreach ($lessons as $lesson): ?><article class="lesson-card"><span class="lesson-number"><?= $lessonNumber ?></span><div><h3><?= e($lesson['title']) ?></h3><p><?= e((string) ($lesson['summary'] ?? '')) ?></p><details><summary>خواندن متن آموزش</summary><div><?= nl2br(e((string) $lesson['content'])) ?></div></details><button type="button" class="read-button <?= $lesson['progress_status'] === 'completed' ? 'completed' : '' ?>" data-lesson-id="<?= (int) $lesson['id'] ?>" <?= $lesson['progress_status'] === 'completed' || !$isAuthenticated ? 'disabled' : '' ?>><?= $lesson['progress_status'] === 'completed' ? 'خوانده شد ✓' : ($isAuthenticated ? 'خواندم ✓' : 'برای ثبت مطالعه وارد شوید') ?></button></div></article><?php $lessonNumber++; endforeach; ?></div><?php else: ?><div class="empty-state">هنوز درسی برای این مسیر منتشر نشده است.</div><?php endif; ?></section>
    </main>
</body>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
<script>
    document.querySelectorAll('.read-button:not(.completed)').forEach(button => {
        button.addEventListener('click', () => {
            button.disabled = true;
            fetch('<?= e(url('api/me')) ?>', { credentials: 'same-origin' }).then(response => response.json()).then(user => {
                if (!user.authenticated) {
                    button.disabled = true;
                    button.title = 'برای ثبت مطالعه ابتدا وارد حساب شو';
                    return;
                }
                const data = new FormData();
                data.append('lesson_id', button.dataset.lessonId);
                data.append('csrf_token', user.csrf_token);
                return fetch('<?= e(url('api/lesson-progress')) ?>', { method: 'POST', body: data, credentials: 'same-origin' });
            }).then(response => response?.json()).then(result => {
                if (result?.completed) {
                    button.textContent = 'خوانده شد ✓';
                    button.classList.add('completed');
                } else if (result?.error) {
                    button.disabled = false;
                    button.title = result.error;
                }
            }).catch(() => { button.disabled = false; });
        });
    });
</script>
</html>