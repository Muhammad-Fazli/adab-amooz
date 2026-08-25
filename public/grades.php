<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('login'));
    exit;
}

$grades = (new GradesController(new Grades($pdo)))->student((int) $_SESSION['user_id']);
require __DIR__ . '/../app/views/grades/index.php';
