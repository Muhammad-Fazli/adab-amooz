<?php

declare(strict_types=1);

final class TeacherDashboard
{
    public function __construct(private PDO $database)
    {
    }

    public function summary(int $teacherId): array
    {
        $statement = $this->database->prepare(
                'SELECT (SELECT COUNT(*) FROM users WHERE teacher_id = :teacher_id_students AND role = "student") AS students,
                    (SELECT COUNT(*) FROM assignment_submissions s
                     INNER JOIN assignments a ON a.id = s.assignment_id
                     INNER JOIN users u ON u.id = s.student_id
                     WHERE a.teacher_id = :teacher_id_assignments AND u.teacher_id = :teacher_id_students_assignments AND s.status = "pending") AS pending_submissions,
                    (SELECT COUNT(*) FROM exams WHERE teacher_id = :teacher_id_exams OR teacher_id IS NULL) AS quizzes,
                    (SELECT COUNT(*) FROM exams WHERE teacher_id = :teacher_id_owned_exams) AS created_exams,
                    (SELECT COUNT(DISTINCT eg.exam_id) FROM exam_grades eg
                     INNER JOIN users u ON u.id = eg.student_id
                     WHERE eg.teacher_id = :teacher_id_graded_exams AND u.teacher_id = :teacher_id_graded_students) AS graded_exams'
        );
        $statement->execute([
            'teacher_id_students' => $teacherId,
            'teacher_id_assignments' => $teacherId,
            'teacher_id_students_assignments' => $teacherId,
            'teacher_id_exams' => $teacherId,
            'teacher_id_owned_exams' => $teacherId,
            'teacher_id_graded_exams' => $teacherId,
            'teacher_id_graded_students' => $teacherId,
        ]);
        return $statement->fetch() ?: ['students' => 0, 'pending_submissions' => 0, 'quizzes' => 0];
    }

    public function students(int $teacherId): array
    {
        $statement = $this->database->prepare(
                'SELECT u.id, u.first_name, u.last_name, u.email, u.state, u.city,
                    u.education_level, u.favorite_subject, u.profile_completed
                 FROM users u
                 WHERE u.teacher_id = :teacher_id AND u.role = "student"
                 ORDER BY u.first_name, u.last_name'
        );
        $statement->execute(['teacher_id' => $teacherId]);
        return $statement->fetchAll();
    }

    public function recentAssignments(int $teacherId): array
    {
        $statement = $this->database->prepare(
            'SELECT a.title, u.first_name, u.last_name, s.status, s.score, s.submitted_at
             FROM assignment_submissions s INNER JOIN assignments a ON a.id = s.assignment_id
             INNER JOIN users u ON u.id = s.student_id
             WHERE a.teacher_id = :teacher_id ORDER BY s.submitted_at DESC LIMIT 10'
        );
        $statement->execute(['teacher_id' => $teacherId]);
        return $statement->fetchAll();
    }
}