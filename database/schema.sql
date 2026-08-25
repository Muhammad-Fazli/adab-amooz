CREATE DATABASE IF NOT EXISTS adab_amooz
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE adab_amooz;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(80) NOT NULL UNIQUE,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'teacher', 'admin') NOT NULL DEFAULT 'student',
    avatar_path VARCHAR(255) NULL,
    state VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    education_level VARCHAR(50) NULL,
    favorite_subject VARCHAR(100) NULL,
    teacher_id INT UNSIGNED NULL,
    profile_completed BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_teacher (teacher_id),
    CONSTRAINT fk_users_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE schools (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NOT NULL,
    name VARCHAR(180) NOT NULL,
    address VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_schools_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE teacher_todos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    is_completed BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_teacher_todos_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO users (username, first_name, last_name, email, password_hash, role)
VALUES ('admin', 'admin', '', NULL, '$2y$10$msfwIsgEeRD2e4FYcUGQ8eoA2VWKuyam51Xfn3WEaR31vMJ568YRG', 'admin')
ON DUPLICATE KEY UPDATE role = 'admin', password_hash = VALUES(password_hash);

CREATE TABLE site_content (
    content_key VARCHAR(80) PRIMARY KEY,
    content_value TEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_content (content_key, content_value) VALUES
    ('hero_title', 'گذر تمدن از ادبیات'),
    ('hero_description', 'ادبیات فارسی را با درس‌های کوتاه، تمرین‌های هوشمند و قصه‌های شاعران، عمیق‌تر و شیرین‌تر یاد بگیر.')
ON DUPLICATE KEY UPDATE content_key = VALUES(content_key);

CREATE TABLE learning_paths (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    description TEXT NULL,
    level ENUM('beginner', 'intermediate', 'all') NOT NULL DEFAULT 'all',
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE lessons (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    learning_path_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    summary TEXT NULL,
    content LONGTEXT NULL,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_lessons_path_slug (learning_path_id, slug),
    CONSTRAINT fk_lessons_path FOREIGN KEY (learning_path_id)
        REFERENCES learning_paths (id) ON DELETE CASCADE,
    CONSTRAINT fk_lessons_teacher FOREIGN KEY (teacher_id)
        REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE user_lesson_progress (
    user_id INT UNSIGNED NOT NULL,
    lesson_id INT UNSIGNED NOT NULL,
    status ENUM('not_started', 'in_progress', 'completed') NOT NULL DEFAULT 'not_started',
    completed_at TIMESTAMP NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, lesson_id),
    CONSTRAINT fk_progress_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_progress_lesson FOREIGN KEY (lesson_id) REFERENCES lessons (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE questions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id INT UNSIGNED NULL,
    topic VARCHAR(100) NOT NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    question_text TEXT NOT NULL,
    quote_text TEXT NULL,
    is_published BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_questions_lesson FOREIGN KEY (lesson_id)
        REFERENCES lessons (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE question_options (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    option_text TEXT NOT NULL,
    is_correct BOOLEAN NOT NULL DEFAULT FALSE,
    display_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_options_question FOREIGN KEY (question_id)
        REFERENCES questions (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE practice_attempts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    selected_option_id INT UNSIGNED NULL,
    is_correct BOOLEAN NULL,
    answered_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attempts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_attempts_question FOREIGN KEY (question_id) REFERENCES questions (id) ON DELETE CASCADE,
    CONSTRAINT fk_attempts_option FOREIGN KEY (selected_option_id) REFERENCES question_options (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE exams (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NULL,
    title VARCHAR(180) NOT NULL UNIQUE,
    description TEXT NULL,
    max_score DECIMAL(5,2) NOT NULL DEFAULT 20,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_exams_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE exam_grades (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exam_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NOT NULL,
    score DECIMAL(5,2) NULL,
    feedback TEXT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_exam_grade_student (exam_id, student_id),
    CONSTRAINT fk_exam_grades_exam FOREIGN KEY (exam_id) REFERENCES exams (id) ON DELETE CASCADE,
    CONSTRAINT fk_exam_grades_student FOREIGN KEY (student_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_exam_grades_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT IGNORE INTO exams (title, description, max_score, display_order) VALUES
    ('آزمون آرایه‌های ادبی', 'تشبیه، استعاره و کنایه', 20, 1),
    ('آزمون قرابت معنایی', 'درک مفهوم و ارتباط بیت‌ها', 20, 2),
    ('آزمون واژگان فارسی', 'واژه‌ها و املا', 20, 3);

CREATE TABLE poets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    slug VARCHAR(160) NOT NULL UNIQUE,
    period VARCHAR(100) NULL,
    biography TEXT NULL,
    image_url VARCHAR(500) NULL,
    category ENUM('poet', 'writer') NOT NULL DEFAULT 'poet',
    era ENUM('classical', 'contemporary') NOT NULL DEFAULT 'classical',
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE books (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NULL,
    category VARCHAR(100) NULL,
    is_published BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE classrooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT UNSIGNED NOT NULL,
    school_id INT UNSIGNED NULL,
    title VARCHAR(150) NOT NULL,
    grade VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_classrooms_teacher FOREIGN KEY (teacher_id)
        REFERENCES users (id) ON DELETE RESTRICT,
    CONSTRAINT fk_classrooms_school FOREIGN KEY (school_id)
        REFERENCES schools (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE classroom_students (
    classroom_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (classroom_id, student_id),
    CONSTRAINT fk_class_students_classroom FOREIGN KEY (classroom_id)
        REFERENCES classrooms (id) ON DELETE CASCADE,
    CONSTRAINT fk_class_students_student FOREIGN KEY (student_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE assignments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    classroom_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    description TEXT NULL,
    due_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assignments_classroom FOREIGN KEY (classroom_id)
        REFERENCES classrooms (id) ON DELETE CASCADE,
    CONSTRAINT fk_assignments_teacher FOREIGN KEY (teacher_id)
        REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE assignment_submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    answer_text LONGTEXT NULL,
    score DECIMAL(5,2) NULL,
    status ENUM('pending', 'reviewed') NOT NULL DEFAULT 'pending',
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    UNIQUE KEY uq_assignment_student (assignment_id, student_id),
    CONSTRAINT fk_submissions_assignment FOREIGN KEY (assignment_id)
        REFERENCES assignments (id) ON DELETE CASCADE,
    CONSTRAINT fk_submissions_student FOREIGN KEY (student_id)
        REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE quizzes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    classroom_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    subject VARCHAR(100) NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 20,
    question_count SMALLINT UNSIGNED NOT NULL DEFAULT 10,
    status ENUM('draft', 'published', 'closed') NOT NULL DEFAULT 'draft',
    scheduled_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_quizzes_classroom FOREIGN KEY (classroom_id)
        REFERENCES classrooms (id) ON DELETE CASCADE,
    CONSTRAINT fk_quizzes_teacher FOREIGN KEY (teacher_id)
        REFERENCES users (id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE quiz_questions (
    quiz_id INT UNSIGNED NOT NULL,
    question_id INT UNSIGNED NOT NULL,
    display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (quiz_id, question_id),
    CONSTRAINT fk_quiz_questions_quiz FOREIGN KEY (quiz_id)
        REFERENCES quizzes (id) ON DELETE CASCADE,
    CONSTRAINT fk_quiz_questions_question FOREIGN KEY (question_id)
        REFERENCES questions (id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT IGNORE INTO learning_paths (title, slug, description, level, display_order) VALUES
    ('آرایه‌های ادبی', 'araye-adabi', 'تشبیه، استعاره و کنایه را با مثال‌های جذاب و قابل فهم یاد بگیر.', 'beginner', 1),
    ('قرابت معنایی', 'gharabat-manayi', 'مفهوم پنهان بیت‌ها را پیدا کن و برای سؤال‌های کنکوری آماده شو.', 'intermediate', 2),
    ('واژگان فارسی', 'vazhegan-farsi', 'واژه‌های مهم کتاب درسی را با فلش‌کارت‌های هوشمند مرور کن.', 'all', 3),
    ('املای فارسی', 'emlay-farsi', 'دام‌های املایی را بشناس و با آزمون‌های سریع مهارتت را بسنج.', 'beginner', 4),
    ('دستور زبان', 'dastoor-zaban', 'نقش کلمات و ساختار جمله را قدم‌به‌قدم و ساده یاد بگیر.', 'intermediate', 5),
    ('تاریخ ادبیات', 'tarikh-e-adabiat', 'با جریان‌ها، آثار و بزرگان زبان فارسی آشنا شو.', 'all', 6);

INSERT INTO questions (topic, difficulty, question_text, quote_text, is_published)
SELECT 'قرابت معنایی', 'intermediate', 'مفهوم مشترک کدام گزینه با بیت زیر نزدیک‌تر است؟', 'توانا بود هر که دانا بود\nز دانش دل پیر برنا بود', 1
WHERE NOT EXISTS (
        SELECT 1 FROM questions WHERE question_text = 'مفهوم مشترک کدام گزینه با بیت زیر نزدیک‌تر است؟'
);

INSERT INTO question_options (question_id, option_text, is_correct, display_order)
SELECT q.id, options.option_text, options.is_correct, options.display_order
FROM questions q
JOIN (
        SELECT 'دانایی و خرد، انسان را توانمند می‌کند' AS option_text, 1 AS is_correct, 1 AS display_order
        UNION ALL SELECT 'هر که ثروتمندتر باشد موفق‌تر است', 0, 2
        UNION ALL SELECT 'جوانی بهترین دوران زندگی است', 0, 3
        UNION ALL SELECT 'آموختن تنها در مدرسه اتفاق می‌افتد', 0, 4
) AS options
WHERE q.question_text = 'مفهوم مشترک کدام گزینه با بیت زیر نزدیک‌تر است؟'
    AND NOT EXISTS (SELECT 1 FROM question_options qo WHERE qo.question_id = q.id);