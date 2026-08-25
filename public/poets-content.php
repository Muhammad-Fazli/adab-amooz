<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';
header('Content-Type: application/json; charset=utf-8');

$poets = (new AdminDashboard($pdo))->allPublishedPoets();
echo json_encode([
    'poets' => array_map(static fn (array $poet): array => [
        'name' => (string) $poet['name'],
        'period' => (string) ($poet['period'] ?? ''),
        'biography' => (string) ($poet['biography'] ?? ''),
        'image_url' => asset_url($poet['image_url'] ?? null),
        'category' => (string) $poet['category'],
        'era' => (string) $poet['era'],
    ], $poets),
], JSON_UNESCAPED_UNICODE);
