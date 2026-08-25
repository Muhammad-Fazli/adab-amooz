<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ' . url('login'));
    exit;
}

$assignments = (new Assignments($pdo))->forStudent((int) $_SESSION['user_id']);
require __DIR__ . '/../app/views/assignments/index.php';
