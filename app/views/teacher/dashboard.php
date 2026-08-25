<?php
$dashboard = $dashboard ?? [
    'summary' => ['students' => 0, 'pending_submissions' => 0, 'quizzes' => 0],
    'students' => [],
    'assignments' => [],
    'exams' => [],
    'grade_rows' => [],
    'grade_message' => '',
    'assignments_created' => [],
    'todos' => [],
    'learning_paths' => [],
];
$gradeLookup = [];
$teacherUsername = (string) ($dashboard['teacher']['username'] ?? '');
$activeSection = $dashboard['active_section'] ?? 'dashboard';
foreach ($dashboard['grade_rows'] as $gradeRow) {
    $gradeLookup[(int) $gradeRow['exam_id']][(int) $gradeRow['student_id']] = $gradeRow;
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل معلم | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/teacher.css')) ?>">
</head>
<body>
    <div class="teacher-shell">
        <aside class="teacher-sidebar">
            <div class="sidebar-brand"><span class="brand-mark">ب</span><div><strong>ادب آموز</strong><small>پنل مدیریت معلم</small></div></div>
            <div class="teacher-profile"><span class="teacher-avatar"><?= e(mb_substr($teacherUsername, 0, 1)) ?></span><div><strong><?= e($teacherUsername) ?></strong><small>معلم ادبیات فارسی</small></div></div>
            <nav class="teacher-nav" aria-label="منوی پنل معلم">
                <a class="<?= $activeSection === 'dashboard' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard')) ?>"><span>⌂</span> داشبورد</a>
                <a href="<?= e(url('profile')) ?>"><span>◉</span> پروفایل</a>
                <a class="<?= $activeSection === 'students' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=students')) ?>"><span>♙</span> دانش‌آموزان</a>
                <a class="<?= $activeSection === 'exams' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=exams')) ?>"><span>▤</span> آزمون‌ها</a>
                <a class="<?= $activeSection === 'grades' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=grades')) ?>"><span>▣</span> نمرات</a>
                <a class="<?= $activeSection === 'lessons' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=lessons')) ?>"><span>◈</span> آموزش‌ها</a>
                <a class="<?= $activeSection === 'assignments' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=assignments')) ?>"><span>✎</span> تکالیف</a>
                <a class="<?= $activeSection === 'teacher-group' ? 'active' : '' ?>" href="<?= e(url('teacher/dashboard?section=teacher-group')) ?>"><span>♧</span> گروه معلمان</a>
            </nav>
            <form method="post" action="<?= e(url('logout')) ?>" class="sidebar-logout"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button type="submit"><span>↪</span> خروج از حساب</button></form>
        </aside>
        <main class="teacher-main" id="dashboard">
            <header class="teacher-topbar"><div><span class="eyebrow">فضای مدیریت کلاس</span><h1>سلام، <span><?= e($teacherUsername) ?></span></h1><p>امروز کلاس و مسیر یادگیری دانش‌آموزانت را مدیریت کن.</p></div><a class="home-link" href="<?= e(url()) ?>">مشاهده سایت ←</a></header>
        <section class="stats-grid">
            <article class="stat-card"><span class="stat-icon blue">♙</span><div><strong><?= (int) $dashboard['summary']['students'] ?></strong><span>دانش‌آموز من</span></div><small>کلاس فعال</small></article>
            <article class="stat-card"><span class="stat-icon gold">▤</span><div><strong><?= (int) $dashboard['summary']['pending_submissions'] ?></strong><span>تکلیف در انتظار</span></div><small>نیازمند بررسی</small></article>
            <article class="stat-card"><span class="stat-icon coral">◈</span><div><strong><?= (int) ($dashboard['summary']['created_exams'] ?? 0) ?></strong><span>آزمون برگزارشده</span></div><small>آزمون‌های من</small></article>
            <article class="stat-card"><span class="stat-icon green">✓</span><div><strong><?= (int) ($dashboard['summary']['graded_exams'] ?? 0) ?></strong><span>آزمون تصحیح‌شده</span></div><small>کارنامه‌ها</small></article>
            <a class="stat-card school-card" href="<?= e(url('teacher/school')) ?>"><span class="stat-icon school">⌂</span><div><strong><?= (int) ($dashboard['school_count'] ?? 0) ?></strong><span>مدرسه من</span></div></a>
        </section>
        <?php if ($activeSection === 'dashboard'): ?><section class="quick-actions" aria-label="دسترسی سریع">
            <a href="<?= e(url('teacher/dashboard?section=exams')) ?>"><span>＋</span><strong>ساخت آزمون</strong><small>آزمون جدید برای کلاس</small></a>
            <a href="<?= e(url('teacher/dashboard?section=lessons')) ?>"><span>◈</span><strong>افزودن آموزش</strong><small>محتوای عمومی سایت</small></a>
            <a href="<?= e(url('teacher/dashboard?section=assignments')) ?>"><span>✎</span><strong>ثبت تکلیف</strong><small>برای دانش‌آموزان کلاس</small></a>
        </section>
        <section class="panel todo-panel">
            <div class="panel-heading"><div><div class="eyebrow">برنامه امروز</div><h2>فهرست کارها</h2></div><span class="todo-count"><?= count($dashboard['todos']) ?> کار</span></div>
            <form method="post" class="todo-create-form"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create_todo"><input name="title" type="text" maxlength="180" required placeholder="یک کار جدید اضافه کن..."><button type="submit">＋ افزودن</button></form>
            <div class="todo-list">
                <?php foreach ($dashboard['todos'] as $todo): ?><form method="post" class="todo-item <?= (bool) $todo['is_completed'] ? 'completed' : '' ?>"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle_todo"><input type="hidden" name="todo_id" value="<?= (int) $todo['id'] ?>"><label><input type="checkbox" name="completed" value="1" <?= (bool) $todo['is_completed'] ? 'checked' : '' ?> onchange="this.form.submit()"><span><?= e((string) $todo['title']) ?></span></label><small><?= (bool) $todo['is_completed'] ? 'انجام‌شده' : 'در انتظار انجام' ?></small></form><?php endforeach; ?>
                <?php if (!$dashboard['todos']): ?><p class="empty-state">هنوز کاری به فهرست اضافه نشده است.</p><?php endif; ?>
            </div>
        </section><?php endif; ?>
        <section class="panel dashboard-section exam-create-panel <?= $activeSection === 'exams' ? 'active' : '' ?>" id="exams">
            <div class="panel-heading"><div><div class="eyebrow">آزمون‌های اختصاصی</div><h2>طراحی آزمون</h2></div></div>
            <form method="post" class="exam-create-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="create_exam">
                <label>عنوان آزمون<input name="title" type="text" required maxlength="180" placeholder="مثلاً آزمون میان‌ترم ادبیات"></label>
                <label>توضیح<input name="description" type="text" maxlength="500" placeholder="موضوع یا توضیح کوتاه آزمون"></label>
                <label>نمرهٔ کل<input name="max_score" type="number" min="1" max="100" step="0.25" value="20" required></label>
                <button type="submit" class="save-grade">ساخت آزمون</button>
            </form>
        </section>
        <section class="panel dashboard-section grade-panel <?= $activeSection === 'grades' ? 'active' : '' ?>" id="grades">
            <div class="panel-heading"><div><div class="eyebrow">ارزیابی آزمون‌ها</div><h2>ثبت نمره و نظر استاد</h2></div></div>
            <div class="grade-entry-list">
                <?php foreach ($dashboard['students'] as $student): ?>
                    <div class="student-grade-row">
                        <div class="student-grade-name"><strong><?= e($student['first_name'] . ' ' . $student['last_name']) ?><?php if (!(bool) $student['profile_completed']): ?> <span class="profile-incomplete">مشخصات کامل نشده</span><?php endif; ?></strong><small><?= e((string) $student['email']) ?></small></div>
                        <div class="student-grade-exams">
                            <?php foreach ($dashboard['exams'] as $exam): $existing = $gradeLookup[(int) $exam['id']][(int) $student['id']] ?? null; ?>
                                <form method="post" class="grade-form">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="save_grade">
                                    <input type="hidden" name="student_id" value="<?= (int) $student['id'] ?>">
                                    <input type="hidden" name="exam_id" value="<?= (int) $exam['id'] ?>">
                                    <strong><?= e((string) $exam['title']) ?></strong>
                                    <label>نمره از <?= e((string) $exam['max_score']) ?><input name="score" type="number" min="0" max="<?= e((string) $exam['max_score']) ?>" step="0.25" value="<?= $existing && $existing['score'] !== null ? e((string) $existing['score']) : '' ?>" placeholder="--"></label>
                                    <label>نظر استاد<textarea name="feedback" rows="2" placeholder="نظر دربارهٔ عملکرد دانش‌آموز"><?= $existing ? e((string) ($existing['feedback'] ?? '')) : '' ?></textarea></label>
                                    <button type="submit" class="save-grade">ثبت نمره</button>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        <section class="content-grid dashboard-section section-<?= e($activeSection) ?> <?= in_array($activeSection, ['students', 'assignments'], true) ? 'active' : '' ?>" id="students">
            <article class="panel students-panel"><div class="panel-heading"><div><div class="eyebrow">کلاس من</div><h2>دانش‌آموزان</h2></div><span class="panel-count"><?= (int) $dashboard['summary']['students'] ?> نفر</span></div><div class="table-wrap"><table><thead><tr><th>نام دانش‌آموز</th><th>ایمیل</th><th>وضعیت پروفایل</th></tr></thead><tbody>
                <?php foreach ($dashboard['students'] as $student): ?><tr><td><strong><?= e($student['first_name'] . ' ' . $student['last_name']) ?></strong></td><td><?= e((string) ($student['email'] ?? 'ثبت نشده')) ?></td><td><?= (bool) $student['profile_completed'] ? '<span class="status-complete">کامل</span>' : '<span class="status-incomplete">مشخصات کامل نشده</span>' ?></td></tr><?php endforeach; ?>
            </tbody></table></div></article>
            <aside class="side-column" id="assignments"><article class="panel assignments-panel"><div class="panel-heading"><div><div class="eyebrow">مدیریت تکالیف</div><h2>اضافه کردن تکلیف</h2></div></div>
                <form method="post" class="assignment-create-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="create_assignment">
                    <label>عنوان تکلیف<input name="title" type="text" required maxlength="180" placeholder="مثلاً خلاصه‌نویسی یک غزل"></label>
                    <label>توضیحات<textarea name="description" rows="3" maxlength="2000" placeholder="توضیح تکلیف برای دانش‌آموزان"></textarea></label>
                    <label>مهلت انجام<input name="due_at" type="datetime-local"></label>
                    <button type="submit" class="save-grade">اضافه کردن تکلیف</button>
                </form>
                <div class="assignment-list-heading">تکالیف ثبت‌شده</div>
                <?php foreach ($dashboard['assignments_created'] as $assignment): ?><div class="created-assignment"><strong><?= e($assignment['title']) ?></strong><small><?= (int) $assignment['submission_count'] ?> پاسخ · <?= $assignment['due_at'] ? e((string) $assignment['due_at']) : 'بدون مهلت' ?></small></div><?php endforeach; ?>
                <?php if (!$dashboard['assignments_created']): ?><p class="empty-state">هنوز تکلیفی ثبت نشده است.</p><?php endif; ?>
            </article><article class="panel assignments-panel"><div class="panel-heading"><div><div class="eyebrow">فعالیت کلاس</div><h2>تکالیف ارسالی</h2></div></div>
                <?php foreach ($dashboard['assignments'] as $assignment): ?><div class="assignment"><div class="assignment-info"><strong><?= e($assignment['title']) ?></strong><small><?= e($assignment['first_name'] . ' ' . $assignment['last_name']) ?></small></div><span class="assignment-state <?= $assignment['status'] === 'reviewed' ? 'checked' : 'pending' ?>"><?= $assignment['status'] === 'reviewed' ? 'بررسی‌شده' : 'در انتظار بررسی' ?></span></div><?php endforeach; ?>
                <?php if (!$dashboard['assignments']): ?><p class="empty-state">هنوز تکلیفی برای بررسی وجود ندارد.</p><?php endif; ?>
            </article></aside>
        </section>
        <?php if ($activeSection === 'lessons'): ?><section class="placeholder-grid dashboard-section active" id="lessons">
            <article class="panel lesson-create-panel"><div class="panel-heading"><div><div class="eyebrow">محتوای عمومی</div><h2>افزودن آموزش</h2></div><span class="group-badge">قابل مشاهده برای همه</span></div>
                <form method="post" class="lesson-create-form">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="create_lesson">
                    <label>مسیر آموزش<select name="path_id" required><?php foreach ($dashboard['learning_paths'] as $path): ?><option value="<?= (int) $path['id'] ?>"><?= e($path['title']) ?></option><?php endforeach; ?></select></label>
                    <label>عنوان آموزش<input name="title" type="text" required maxlength="180" placeholder="مثلاً آرایه تشبیه به زبان ساده"></label>
                    <label>خلاصه<input name="summary" type="text" maxlength="500" placeholder="خلاصه کوتاه آموزش"></label>
                    <label>متن آموزش<textarea name="content" rows="7" required placeholder="متن کامل آموزش را وارد کن..."></textarea></label>
                    <button type="submit" class="save-grade">انتشار آموزش عمومی</button>
                </form>
            </article>
        </section><?php endif; ?>
        <section class="panel dashboard-section teacher-group-panel <?= $activeSection === 'teacher-group' ? 'active' : '' ?>" id="teacher-group">
            <div class="panel-heading"><div><div class="eyebrow">ارتباط همکاران</div><h2>گروه معلمان</h2></div><span class="group-badge">فعال</span></div>
            <div class="teacher-group-content"><span class="group-icon">♧</span><div><strong>گروه معلمان ادب آموز</strong><p>محل ارتباط و هماهنگی معلمان برای مدیریت بهتر کلاس‌ها.</p></div><button type="button" disabled>به‌زودی</button></div>
        </section>
        </main>
    </div>
</body>
<script>
    const dashboardSections = document.querySelectorAll('.dashboard-section');
    const dashboardLinks = document.querySelectorAll('.teacher-nav a[href^="#"]');
    const showDashboardSection = (sectionId) => {
        const requestedElement = document.getElementById(sectionId);
        const section = requestedElement?.classList.contains('dashboard-section')
            ? requestedElement
            : requestedElement?.closest('.dashboard-section');
        dashboardSections.forEach(item => item.classList.toggle('active', item === section));
        dashboardLinks.forEach(link => link.classList.toggle('active', link.getAttribute('href') === `#${sectionId}`));
    };
    dashboardLinks.forEach(link => link.addEventListener('click', event => {
        event.preventDefault();
        showDashboardSection(link.getAttribute('href').slice(1));
        history.replaceState(null, '', link.getAttribute('href'));
    }));
    if (window.location.hash && document.getElementById(window.location.hash.slice(1))) {
        showDashboardSection(window.location.hash.slice(1));
    }
</script>
<script src="<?= e(url('js/persian-numbers.js')) ?>"></script>
</html>