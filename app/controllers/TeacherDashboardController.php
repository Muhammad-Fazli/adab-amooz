<?php

declare(strict_types=1);

final class TeacherDashboardController
{
    public function __construct(private TeacherDashboard $dashboard, private ?Grades $grades = null)
    {
    }

    public function index(int $teacherId): array
    {
        $data = [
            'summary' => $this->dashboard->summary($teacherId),
            'students' => $this->dashboard->students($teacherId),
            'assignments' => $this->dashboard->recentAssignments($teacherId),
        ];
        if ($this->grades) {
            $data['exams'] = $this->grades->examsForTeacher($teacherId);
            $data['grade_rows'] = $this->grades->gradesForTeacher($teacherId);
        }

        return $data;
    }
}