<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    // اضافه کردن فیلدهای جدید به جدول users
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS state VARCHAR(100) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS city VARCHAR(100) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS education_level VARCHAR(50) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS favorite_subject VARCHAR(100) NULL");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_completed BOOLEAN NOT NULL DEFAULT FALSE");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS teacher_id INT UNSIGNED NULL");
    $pdo->exec("CREATE TABLE IF NOT EXISTS schools (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        teacher_id INT UNSIGNED NOT NULL,
        name VARCHAR(180) NOT NULL,
        address VARCHAR(255) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_schools_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    try {
        $pdo->exec("ALTER TABLE schools DROP FOREIGN KEY fk_schools_teacher");
        $pdo->exec("ALTER TABLE schools DROP INDEX teacher_id");
        $pdo->exec("ALTER TABLE schools ADD CONSTRAINT fk_schools_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE");
    } catch (PDOException $exception) {
        // The constraint/index is already in the desired state on a fresh installation.
    }
    $pdo->exec("ALTER TABLE classrooms ADD COLUMN IF NOT EXISTS school_id INT UNSIGNED NULL");
    try {
        $pdo->exec("ALTER TABLE classrooms ADD CONSTRAINT fk_classrooms_school FOREIGN KEY (school_id) REFERENCES schools (id) ON DELETE SET NULL");
    } catch (PDOException $exception) {
        // The constraint already exists on a second migration run.
    }
    $pdo->exec("ALTER TABLE lessons ADD COLUMN IF NOT EXISTS teacher_id INT UNSIGNED NULL");
    try {
        $pdo->exec("ALTER TABLE lessons ADD CONSTRAINT fk_lessons_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE SET NULL");
    } catch (PDOException $exception) {
        // The constraint already exists on a second migration run.
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS teacher_todos (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        teacher_id INT UNSIGNED NOT NULL,
        title VARCHAR(180) NOT NULL,
        is_completed BOOLEAN NOT NULL DEFAULT FALSE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_teacher_todos_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    try {
        $pdo->exec("ALTER TABLE users ADD INDEX idx_users_teacher (teacher_id)");
    } catch (PDOException $exception) {
        // The index already exists on a second migration run.
    }
    try {
        $pdo->exec("ALTER TABLE users ADD CONSTRAINT fk_users_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE SET NULL");
    } catch (PDOException $exception) {
        // The constraint already exists on a second migration run.
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS exams (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        teacher_id INT UNSIGNED NULL,
        title VARCHAR(180) NOT NULL UNIQUE,
        description TEXT NULL,
        max_score DECIMAL(5,2) NOT NULL DEFAULT 20,
        display_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        is_published BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_exams_teacher FOREIGN KEY (teacher_id) REFERENCES users (id) ON DELETE CASCADE
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS books (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(180) NOT NULL,
        description TEXT NOT NULL,
        image_url VARCHAR(500) NULL,
        category VARCHAR(100) NULL,
        is_published BOOLEAN NOT NULL DEFAULT TRUE,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    $pdo->exec("CREATE TABLE IF NOT EXISTS poets (
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
    ) ENGINE=InnoDB");
    $pdo->exec("ALTER TABLE poets ADD COLUMN IF NOT EXISTS era ENUM('classical', 'contemporary') NOT NULL DEFAULT 'classical'");
    $pdo->exec("INSERT IGNORE INTO poets (name, slug, period, biography, image_url, category, era) VALUES
        ('فردوسی', 'ferdowsi', 'قرن چهارم', 'سراینده شاهنامه و پاسدار زبان فارسی', 'https://commons.wikimedia.org/wiki/Special:FilePath/FerdowsiFartur.jpg?width=600', 'poet', 'classical'),
        ('سعدی', 'saadi', 'قرن هفتم', 'استاد حکایت و غزل، نویسنده گلستان', 'https://commons.wikimedia.org/wiki/Special:FilePath/Saadi_-_Il_Roseto,_I_(page_1_crop).jpg?width=600', 'poet', 'classical'),
        ('حافظ', 'hafez', 'قرن هشتم', 'شاعر غزل‌های رازآلود و ماندگار', 'https://commons.wikimedia.org/wiki/Special:FilePath/Chama_al-Din_Muhammad_Hafiz.jpg?width=600', 'poet', 'classical'),
        ('مولانا', 'molana', 'قرن هفتم', 'شاعر شور و معنا، آفریننده مثنوی', 'https://images.unsplash.com/photo-1577083552431-6e5fd01988c5?w=600&q=80', 'poet', 'classical'),
        ('خیام', 'khayyam', 'قرن پنجم', 'حکیم، ریاضی‌دان و شاعر رباعی‌های اندیشه‌ورز', 'https://images.unsplash.com/photo-1500534623283-312aade485b7?w=600&q=80', 'poet', 'classical'),
        ('نظامی', 'nezami', 'قرن ششم', 'آفریننده خمسه و داستان‌های عاشقانه ماندگار', 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=600&q=80', 'poet', 'classical'),
        ('عطار', 'attar', 'قرن ششم', 'شاعر عرفان و نویسنده منطق‌الطیر', 'https://images.unsplash.com/photo-1519682337058-a94d519337bc?w=600&q=80', 'poet', 'classical'),
        ('پروین اعتصامی', 'parvin-etesami', 'معاصر', 'شاعر مناظره‌های حکیمانه و زبان روشن', 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&q=80', 'poet', 'contemporary'),
        ('شهریار', 'shahriar', 'معاصر', 'شاعر نام‌آشنای غزل و حیدربابایه سلام', 'https://images.unsplash.com/photo-1500534623283-312aade485b7?w=600&q=80', 'poet', 'contemporary'),
        ('فاضل نظری', 'fazel-nazari', 'معاصر', 'شاعر غزل‌های امروزی با زبانی موجز و عاطفی', 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?w=600&q=80', 'poet', 'contemporary'),
        ('صادق هدایت', 'sadegh-hedayat', 'معاصر', 'نویسنده بوف کور و از پیشگامان داستان‌نویسی مدرن فارسی', 'https://images.unsplash.com/photo-1495446815901-a7297e633e8d?w=600&q=80', 'writer', 'contemporary'),
        ('محمود دولت‌آبادی', 'mahmoud-dowlatabadi', 'معاصر', 'نویسنده کلیدر و راوی زندگی مردم و زمین', 'https://images.unsplash.com/photo-1516979187457-637abb4f9353?w=600&q=80', 'writer', 'contemporary')");
    $pdo->exec("ALTER TABLE exams ADD COLUMN IF NOT EXISTS teacher_id INT UNSIGNED NULL");
    $pdo->exec("CREATE TABLE IF NOT EXISTS exam_grades (
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
    ) ENGINE=InnoDB");
    $pdo->exec("INSERT IGNORE INTO exams (title, description, max_score, display_order) VALUES
        ('آزمون آرایه‌های ادبی', 'تشبیه، استعاره و کنایه', 20, 1),
        ('آزمون قرابت معنایی', 'درک مفهوم و ارتباط بیت‌ها', 20, 2),
        ('آزمون واژگان فارسی', 'واژه‌ها و املا', 20, 3)");
    $teacherStatement = $pdo->prepare("INSERT IGNORE INTO users (username, first_name, last_name, password_hash, role, profile_completed) VALUES (?, ?, ?, ?, 'teacher', 1)");
    $teacherStatement->execute(['rezaei', 'محمد', 'رضایی', password_hash('Rezaei', PASSWORD_DEFAULT)]);
    $teacherStatement->execute(['محمد رضایی', 'محمد', 'رضایی', password_hash('Rezaei', PASSWORD_DEFAULT)]);
    $teacherId = (int) $pdo->query("SELECT id FROM users WHERE username = 'محمد رضایی' AND role = 'teacher' LIMIT 1")->fetchColumn();
    if ($teacherId > 0) {
        $studentStatement = $pdo->prepare("INSERT IGNORE INTO users (username, first_name, last_name, password_hash, role, teacher_id) VALUES (?, ?, '', ?, 'student', ?)");
        $studentStatement->execute(['hossein', 'حسین', password_hash('Hossein', PASSWORD_DEFAULT), $teacherId]);
        $studentStatement->execute(['mohammad', 'محمد', password_hash('Muhammad', PASSWORD_DEFAULT), $teacherId]);
        $relinkStatement = $pdo->prepare("UPDATE users SET teacher_id = ? WHERE username IN ('hossein', 'mohammad') AND role = 'student'");
        $relinkStatement->execute([$teacherId]);
        $sampleStudent = $pdo->prepare("SELECT id FROM users WHERE username = 'mohammad' AND teacher_id = ? LIMIT 1");
        $sampleStudent->execute([$teacherId]);
        $sampleStudentId = (int) $sampleStudent->fetchColumn();
        if ($sampleStudentId > 0) {
            $oldMuhammad = $pdo->prepare("UPDATE users SET username = CONCAT('محمد-قدیمی-', id) WHERE username = 'محمد' AND id <> ?");
            $oldMuhammad->execute([$sampleStudentId]);
            $renameStudent = $pdo->prepare("UPDATE users SET username = 'محمد', first_name = 'محمد', last_name = '' WHERE id = ?");
            $renameStudent->execute([$sampleStudentId]);
        }
    }

    echo "✅ دیتابیس با موفقیت به‌روزرسانی شد!";
} catch (PDOException $e) {
    echo "❌ خطا: " . $e->getMessage();
}
