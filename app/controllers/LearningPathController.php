<?php

declare(strict_types=1);

final class LearningPathController
{
    public function __construct(private LearningPath $learningPaths)
    {
    }

    public function index(): array
    {
        return $this->learningPaths->published();
    }
}