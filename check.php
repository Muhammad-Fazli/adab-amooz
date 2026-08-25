<?php
require_once __DIR__ . '/config/bootstrap.php';

echo "=== بررسی سیستم ===\n\n";

// بررسی فیلدهای دیتابیس
echo "1️⃣ ستون‌های جدول users:\n";
$result = $pdo->query('DESCRIBE users');
$columns = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $columns[] = $row['Field'];
    echo "   - " . $row['Field'] . "\n";
}

echo "\n2️⃣ بررسی فیلدهای جدید:\n";
$required = ['state', 'city', 'education_level', 'favorite_subject', 'profile_completed'];
foreach ($required as $field) {
    $exists = in_array($field, $columns);
    echo "   " . ($exists ? "✓" : "✗") . " $field\n";
}

echo "\n3️⃣ بررسی فایل‌های موجود:\n";
$files = [
    'public/profile-complete.php',
    'app/views/auth/profile-complete.php',
    'app/models/User.php'
];
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    echo "   " . ($exists ? "✓" : "✗") . " $file\n";
}

echo "\n✅ تمام بررسی‌ها انجام شدند!\n";
