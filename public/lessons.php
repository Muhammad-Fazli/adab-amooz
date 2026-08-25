<?php

require_once __DIR__ . '/../config/bootstrap.php';

$learningPaths = (new LearningPathController(new LearningPath($pdo)))->index();
require __DIR__ . '/../app/views/learning-paths/index.php';