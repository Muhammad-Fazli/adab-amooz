<?php

declare(strict_types=1);

final class Practice
{
    public function __construct(private PDO $database)
    {
    }

    public function findQuestion(int $questionId = 0): ?array
    {
        $query = $questionId > 0
            ? 'SELECT * FROM questions WHERE id = :id AND is_published = 1 LIMIT 1'
            : 'SELECT * FROM questions WHERE is_published = 1 ORDER BY id LIMIT 1';
        $statement = $this->database->prepare($query);
        if ($questionId > 0) {
            $statement->execute(['id' => $questionId]);
        } else {
            $statement->execute();
        }
        $question = $statement->fetch();
        if (!$question) {
            return null;
        }

        $options = $this->database->prepare(
            'SELECT id, option_text FROM question_options WHERE question_id = :question_id ORDER BY display_order, id'
        );
        $options->execute(['question_id' => $question['id']]);
        $question['options'] = $options->fetchAll();

        return $question;
    }

    public function saveAttempt(int $userId, int $questionId, int $optionId): ?bool
    {
        $statement = $this->database->prepare(
            'SELECT qo.is_correct
             FROM question_options qo
             INNER JOIN questions q ON q.id = qo.question_id
             WHERE qo.id = :option_id AND qo.question_id = :question_id AND q.is_published = 1
             LIMIT 1'
        );
        $statement->execute(['option_id' => $optionId, 'question_id' => $questionId]);
        $option = $statement->fetch();
        if (!$option) {
            return null;
        }

        $attempt = $this->database->prepare(
            'INSERT INTO practice_attempts (user_id, question_id, selected_option_id, is_correct) VALUES (:user_id, :question_id, :option_id, :is_correct)'
        );
        $attempt->execute([
            'user_id' => $userId,
            'question_id' => $questionId,
            'option_id' => $optionId,
            'is_correct' => (int) $option['is_correct'],
        ]);

        return (bool) $option['is_correct'];
    }

    public function summary(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT COUNT(*) AS total_attempts,
                    COALESCE(SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END), 0) AS correct_attempts
             FROM practice_attempts
             WHERE user_id = :user_id'
        );
        $statement->execute(['user_id' => $userId]);
        $summary = $statement->fetch() ?: ['total_attempts' => 0, 'correct_attempts' => 0];

        $datesStatement = $this->database->prepare(
            'SELECT DISTINCT DATE(answered_at) AS activity_date
             FROM practice_attempts
             WHERE user_id = :user_id
             ORDER BY activity_date DESC'
        );
        $datesStatement->execute(['user_id' => $userId]);
        $activityDates = array_map(
            static fn (array $row): string => (string) $row['activity_date'],
            $datesStatement->fetchAll()
        );

        $streak = 0;
        $expectedDate = new DateTimeImmutable('today');
        if (($activityDates[0] ?? '') === $expectedDate->modify('-1 day')->format('Y-m-d')) {
            $expectedDate = $expectedDate->modify('-1 day');
        }
        foreach ($activityDates as $activityDate) {
            if ($activityDate !== $expectedDate->format('Y-m-d')) {
                break;
            }
            $streak++;
            $expectedDate = $expectedDate->modify('-1 day');
        }

        return [
            'total_attempts' => (int) $summary['total_attempts'],
            'correct_attempts' => (int) $summary['correct_attempts'],
            'streak_days' => $streak,
        ];
    }

    public function recentAttempts(int $userId): array
    {
        $statement = $this->database->prepare(
            'SELECT q.question_text, pa.is_correct, pa.answered_at
             FROM practice_attempts pa
             INNER JOIN questions q ON q.id = pa.question_id
             WHERE pa.user_id = :user_id
             ORDER BY pa.answered_at DESC
             LIMIT 3'
        );
        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }
}