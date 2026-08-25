<?php

declare(strict_types=1);

final class AuthController
{
    public function __construct(private User $users)
    {
    }

    public function register(array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $role = ($input['role'] ?? 'student') === 'teacher' ? 'teacher' : 'student';
        $teacherId = null;
        if ($role === 'student') {
            $teacherUsername = trim((string) ($input['teacher_username'] ?? ''));
            $teacher = $this->users->findByUsername($teacherUsername);
            if ($teacher === null || $teacher['role'] !== 'teacher') {
                return ['error' => 'برای ثبت‌نام دانش‌آموز، نام کاربری معلم معتبر وارد کن.'];
            }
            $teacherId = (int) $teacher['id'];
        }

        if ($username === '' || $password === '') {
            return ['error' => 'لطفاً همه اطلاعات را کامل وارد کن.'];
        }
        if (strlen($username) < 3) {
            return ['error' => 'نام کاربری باید حداقل ۳ کاراکتر باشد.'];
        }
        if (strlen($password) < 6) {
            return ['error' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.'];
        }
        if ($this->users->findByUsername($username) !== null) {
            return ['error' => 'این نام کاربری قبلاً استفاده شده است.'];
        }

        try {
            $userId = $this->users->create($username, $password, $role, $teacherId);
        } catch (PDOException $exception) {
            return ['error' => 'ثبت‌نام انجام نشد. دوباره تلاش کن.'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_username'] = $username;
        $_SESSION['user_first_name'] = $username;
        $_SESSION['user_last_name'] = '';

        return ['user_id' => $userId];
    }

    public function login(array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $requestedRole = ($input['role'] ?? 'student') === 'teacher' ? 'teacher' : 'student';
        $user = $this->users->findByUsername($username);

        if ($user === null || $user['role'] !== $requestedRole || !password_verify($password, $user['password_hash'])) {
            return ['error' => 'نام کاربری یا رمز عبور نادرست است.'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_first_name'] = $user['username'];
        $_SESSION['user_last_name'] = '';
        $_SESSION['flash_message'] = 'سلام ' . $user['username'] . '! خوش‌آمدید.';

        return ['user_id' => (int) $user['id']];
    }

    public function loginAdmin(array $input): array
    {
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $user = $this->users->findByUsername($username);

        if ($user === null || $user['role'] !== 'admin' || !password_verify($password, $user['password_hash'])) {
            return ['error' => 'نام کاربری یا رمز عبور ادمین نادرست است.'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_first_name'] = $user['first_name'];
        $_SESSION['user_last_name'] = $user['last_name'];

        return ['user_id' => (int) $user['id']];
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }
}