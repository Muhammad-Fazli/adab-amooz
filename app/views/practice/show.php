<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمرین | ادب آموز</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Estedad:wght@400;500;600;700;800&family=Vazirmatn:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(url('css/tamrin.css')) ?>">
</head>
<body>
    <header><div class="shell nav"><a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a><a class="back" href="<?= e(url()) ?>">بازگشت به خانه ←</a></div></header>
    <main>
        <section class="shell intro"><div class="eyebrow">تمرین روزانه</div><h1>آموخته‌هایت را <span class="coral-text">امتحان کن.</span></h1><p>تمرین‌های کوتاه و هدفمند، دانسته‌هایت را به مهارت واقعی تبدیل می‌کنند.</p></section>
        <section class="shell layout">
            <aside class="side"><h2>تمرین‌ها</h2><p>یک سؤال را انتخاب کن و رکورد خودت را بساز.</p><strong>تمرین فعال</strong></aside>
            <article class="question">
                <?php if ($question): ?>
                    <div class="question-top"><span><?= e((string) $question['topic']) ?> · <?= e((string) $question['difficulty']) ?></span><strong>سؤال تمرینی</strong></div>
                    <div class="progress"><div class="bar"></div></div>
                    <h2><?= e((string) $question['question_text']) ?></h2>
                    <?php if (!empty($question['quote_text'])): ?><div class="quote"><?= nl2br(e((string) $question['quote_text'])) ?></div><?php endif; ?>
                    <form method="post" class="answers">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="question_id" value="<?= (int) $question['id'] ?>">
                        <?php foreach ($question['options'] as $option): ?><button class="answer" type="submit" name="option_id" value="<?= (int) $option['id'] ?>"><?= e((string) $option['option_text']) ?></button><?php endforeach; ?>
                    </form>
                    <p class="tip" style="color:<?= $isCorrect === true ? 'green' : ($isCorrect === false ? 'var(--coral)' : 'inherit') ?>"><?= e($message ?: 'برای پاسخ، گزینه‌ای را انتخاب کن.') ?></p>
                <?php else: ?>
                    <h2>هنوز سؤالی برای تمرین منتشر نشده است.</h2>
                <?php endif; ?>
            </article>
        </section>
    </main>
</body>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>