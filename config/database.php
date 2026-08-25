<?php

declare(strict_types=1);

$databaseHost = '127.0.0.1';
$databaseName = 'adab_amooz';
$databaseUser = 'root';
$databasePassword = '';

$pdo = new PDO(
    "mysql:host={$databaseHost};dbname={$databaseName};charset=utf8mb4",
    $databaseUser,
    $databasePassword,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);