<?php
$message = $message ?? '';
$isError = $isError ?? false;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تکمیل پروفایل | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/login.css')) ?>">
    <style>
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
        .field textarea {
            font-family: inherit;
            font-size: 1rem;
            padding: 0.75rem;
            border: 2px solid var(--gray-light);
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="welcome">
            <a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a>
            <div class="welcome-copy"><div class="eyebrow">یک قدم دیگر</div><h1>پروفایل‌ات را<br>کامل کن</h1><p>اطلاعات کمی بیشتر ما را کمک می‌کند تا بهتر برایت یادگیری طراحی کنیم.</p></div>
        </section>
        <section class="form-area">
            <h2>تکمیل اطلاعات</h2>
            <p class="sub">این اطلاعات برای تخصیص بهتر محتوا به تو مفید هستند.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                
                <div class="form-grid">
                    <div class="field">
                        <label for="first_name">نام</label>
                        <input id="first_name" name="first_name" type="text" required value="<?= e($user['first_name'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label for="last_name">نام خانوادگی</label>
                        <input id="last_name" name="last_name" type="text" value="<?= e($user['last_name'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="state">استان <span style="color: var(--coral);">*</span></label>
                        <input id="state" name="state" type="text" placeholder="مثال: تهران" required value="<?= e($user['state'] ?? '') ?>">
                    </div>
                    <div class="field">
                        <label for="city">شهر <span style="color: var(--coral);">*</span></label>
                        <input id="city" name="city" type="text" placeholder="مثال: تهران" required value="<?= e($user['city'] ?? '') ?>">
                    </div>
                </div>

                <div class="field">
                    <label>پایه تحصیلی <span style="color: var(--coral);">*</span></label>
                    <input id="education_level" name="education_level" type="number" min="7" max="12" step="1" required placeholder="مثلاً ۷" value="<?= e((string) preg_replace('/[^0-9۰-۹]/u', '', (string) ($user['education_level'] ?? ''))) ?>">
                </div>

                <div class="field">
                    <label for="favorite_subject">نام درس یا موضوع مورد علاقه</label>
                    <input id="favorite_subject" name="favorite_subject" type="text" placeholder="مثال: شاعری معاصر" value="<?= e($user['favorite_subject'] ?? '') ?>">
                </div>

                <button class="btn" type="submit">تکمیل پروفایل ←</button>
                <p class="message" id="message" aria-live="polite" style="color:<?= $isError ? 'var(--coral)' : 'var(--blue)' ?>"><?= e($message) ?></p>
            </form>
            <a class="home" href="<?= e(url('profile')) ?>">← بازگشت به پروفایل</a>
        </section>
    </main>
    <script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</body>
</html>
