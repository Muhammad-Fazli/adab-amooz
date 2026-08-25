<?php

declare(strict_types=1);

final class PracticeController
{
    public function __construct(private Practice $practice)
    {
    }

    public function question(int $questionId = 0): ?array
    {
        return $this->practice->findQuestion($questionId);
    }

    public function answer(int $userId, int $questionId, int $optionId): ?bool
    {
        return $this->practice->saveAttempt($userId, $questionId, $optionId);
    }
}