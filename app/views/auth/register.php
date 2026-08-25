<?php
$message = $message ?? '';
$isError = $isError ?? false;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/register.css')) ?>">
</head>
<body>
    <main class="page">
        <section class="welcome">
            <a class="brand" href="<?= e(url()) ?>"><span class="mark">ب</span> ادب آموز</a>
            <div class="welcome-copy"><div class="eyebrow">یک شروع تازه</div><h1>به جمع<br>یادگیرنده‌ها بپیوند.</h1><p>با چند قدم ساده، سفر ادبی خودت را شروع کن.</p></div>
        </section>
        <section class="form-area">
            <h2>ساخت حساب کاربری</h2>
            <p class="sub">نوع حساب خودت را انتخاب کن.</p>
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="account-type"><label><input type="radio" name="role" value="student" checked> ثبت‌نام دانش‌آموز</label><label><input type="radio" name="role" value="teacher"> ثبت‌نام معلم</label></div>
                <div class="field"><label for="username">نام کاربری</label><input id="username" name="username" type="text" required minlength="3" autocomplete="username"></div>
                <div class="field" id="teacherField"><label for="teacher_username">نام کاربری معلم</label><input id="teacher_username" name="teacher_username" type="text" required autocomplete="off"></div>
                <div class="field"><label for="password">رمز عبور</label><input id="password" name="password" type="password" required minlength="6" autocomplete="new-password"></div>
                <button class="btn" type="submit">ساخت حساب کاربری ←</button>
                <p class="message" id="message" aria-live="polite" style="color:<?= $isError ? 'var(--coral)' : 'var(--blue)' ?>"><?= e($message) ?></p>
            </form>
            <p class="switch">قبلاً ثبت‌نام کرده‌ای؟ <a href="<?= e(url('login')) ?>">وارد شو</a></p>
            <a class="home" href="<?= e(url()) ?>">← بازگشت به صفحه اصلی</a>
        </section>
    </main>
</body>
<script>
    const updateTeacherField = role => {
        const field = document.getElementById('teacherField');
        const student = role === 'student';
        field.hidden = !student;
        field.style.display = student ? '' : 'none';
        field.querySelector('input').required = student;
        field.querySelector('input').disabled = !student;
    };
    document.querySelectorAll('input[name="role"]').forEach(input => input.addEventListener('change', event => updateTeacherField(event.target.value)));
    updateTeacherField(document.querySelector('input[name="role"]:checked').value);
</script>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>