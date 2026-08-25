<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'teacher') {
    http_response_code(403);
    exit('دسترسی به پنل معلم مجاز نیست.');
}

$teacherId = (int) $_SESSION['user_id'];
$teacher = (new User($pdo))->findById($teacherId);
if ($teacher === null || $teacher['role'] !== 'teacher') {
    http_response_code(403);
    exit('دسترسی به پنل معلم مجاز نیست.');
}

$grades = new Grades($pdo);
$assignmentsModel = new Assignments($pdo);
$todoTasks = new TodoTasks($pdo);
$learningPathModel = new LearningPath($pdo);
$gradeMessage = '';
$gradesController = new GradesController($grades);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_grade') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $scoreInput = trim((string) ($_POST['score'] ?? ''));
        $saved = $grades->saveForTeacher(
            $teacherId,
            (int) ($_POST['student_id'] ?? 0),
            (int) ($_POST['exam_id'] ?? 0),
            $scoreInput === '' ? null : (float) $scoreInput,
            (string) ($_POST['feedback'] ?? '')
        );
        $gradeMessage = $saved ? 'نمره با موفقیت ثبت شد.' : 'ثبت نمره ممکن نیست؛ اطلاعات را بررسی کن.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_exam') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $created = $gradesController->createExam($teacherId, (string) ($_POST['title'] ?? ''), (string) ($_POST['description'] ?? ''), (float) ($_POST['max_score'] ?? 20));
        $gradeMessage = $created ? 'آزمون جدید برای دانش‌آموزان شما ساخته شد.' : 'ساخت آزمون انجام نشد؛ اطلاعات را بررسی کن.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_assignment') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $created = $assignmentsModel->createForTeacher(
            $teacherId,
            (string) ($_POST['title'] ?? ''),
            (string) ($_POST['description'] ?? ''),
            str_replace('T', ' ', (string) ($_POST['due_at'] ?? ''))
        );
        $gradeMessage = $created ? 'تکلیف برای دانش‌آموزان شما ثبت شد.' : 'ثبت تکلیف انجام نشد؛ اطلاعات را بررسی کن.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_todo') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $created = $todoTasks->create($teacherId, (string) ($_POST['title'] ?? ''));
        $gradeMessage = $created ? 'کار جدید به فهرست کارها اضافه شد.' : 'افزودن کار انجام نشد.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_lesson') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $created = $learningPathModel->createLesson(
            $teacherId,
            (int) ($_POST['path_id'] ?? 0),
            (string) ($_POST['title'] ?? ''),
            (string) ($_POST['summary'] ?? ''),
            (string) ($_POST['content'] ?? '')
        );
        $gradeMessage = $created ? 'آموزش عمومی با موفقیت منتشر شد.' : 'ثبت آموزش انجام نشد؛ اطلاعات را بررسی کن.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_todo') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $gradeMessage = 'درخواست نامعتبر است.';
    } else {
        $updated = $todoTasks->setCompleted(
            $teacherId,
            (int) ($_POST['todo_id'] ?? 0),
            (string) ($_POST['completed'] ?? '') === '1'
        );
        $gradeMessage = $updated ? 'وضعیت کار به‌روزرسانی شد.' : 'به‌روزرسانی کار انجام نشد.';
    }
}

$dashboard = (new TeacherDashboardController(new TeacherDashboard($pdo), $grades))->index($teacherId);
$dashboard['grade_message'] = $gradeMessage;
$dashboard['assignments_created'] = $assignmentsModel->forTeacher($teacherId);
$dashboard['todos'] = $todoTasks->forTeacher($teacherId);
$dashboard['learning_paths'] = $learningPathModel->published();
$dashboard['teacher'] = $teacher;
$dashboard['school_count'] = count((new School($pdo))->listForTeacher($teacherId));
$dashboard['active_section'] = match ($_POST['action'] ?? '') {
    'save_grade' => 'grades',
    'create_exam' => 'exams',
    'create_assignment' => 'assignments',
    'create_todo', 'toggle_todo' => 'dashboard',
    'create_lesson' => 'lessons',
    default => in_array($_GET['section'] ?? 'dashboard', ['dashboard', 'students', 'exams', 'grades', 'assignments', 'lessons', 'teacher-group'], true)
        ? (string) ($_GET['section'] ?? 'dashboard')
        : 'dashboard',
};
require __DIR__ . '/../app/views/teacher/dashboard.php';