<?php
$message = $message ?? '';
$isError = $isError ?? false;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/login.css')) ?>">
</head>
<body>
    <main class="page">
        <section class="welcome">
            <a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a>
            <div class="welcome-copy"><div class="eyebrow">خوش برگشتی</div><h1>دوباره وارد<br>دنیای کلمه‌ها شو.</h1><p>یادگیری‌ات را از همان‌جایی که رها کردی ادامه بده.</p></div>
        </section>
        <section class="form-area">
            <h2>ورود به حساب</h2>
            <p class="sub">نوع حساب خودت را انتخاب کن.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="account-type"><label><input type="radio" name="role" value="student" checked> ورود دانش‌آموز</label><label><input type="radio" name="role" value="teacher"> ورود معلم</label></div>
                <div class="field"><label for="username">نام کاربری</label><input id="username" name="username" type="text" required autocomplete="username"></div>
                <div class="field"><label for="password">رمز عبور</label><input id="password" name="password" type="password" required autocomplete="current-password"></div>
                <button class="btn" type="submit">ورود به ادب آموز ←</button>
                <p class="message" id="message" aria-live="polite" style="color:<?= $isError ? 'var(--coral)' : 'var(--blue)' ?>"><?= e($message) ?></p>
            </form>
            <p class="switch">حساب کاربری نداری؟ <a href="<?= e(url('register')) ?>">ثبت‌نام کن</a></p>
            <a class="home" href="<?= e(url()) ?>">← بازگشت به صفحه اصلی</a>
        </section>
    </main>
</body>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>