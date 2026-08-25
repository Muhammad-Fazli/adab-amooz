<?php
$message = $message ?? '';
$isError = $isError ?? false;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود مدیریت | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/login.css')) ?>">
</head>
<body>
    <main class="page">
        <section class="welcome">
            <a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a>
            <div class="welcome-copy"><div class="eyebrow">مدیریت مرکزی</div><h1>مدیریت<br>ادب آموز.</h1><p>برای مدیریت محتوای آموزشی و اعضای سامانه وارد شوید.</p></div>
        </section>
        <section class="form-area">
            <h2>ورود ادمین</h2>
            <p class="sub">اطلاعات حساب مدیریت را وارد کنید.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="field"><label for="username">نام کاربری</label><input id="username" name="username" type="text" required autocomplete="username"></div>
                <div class="field"><label for="password">رمز عبور</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
                <button class="btn" type="submit">ورود به مدیریت ←</button>
                <p class="message" aria-live="polite" style="color:<?= $isError ? 'var(--coral)' : 'var(--blue)' ?>"><?= e($message) ?></p>
            </form>
            <a class="home" href="<?= e(url('login')) ?>">← بازگشت به ورود کاربران</a>
        </section>
    </main>
</body>
</html>