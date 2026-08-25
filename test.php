<?php

require_once __DIR__ . '/config/bootstrap.php';

echo "=== 🧪 تست سیستم ===\n\n";

// تست ۱: بررسی فیلدهای جدول users
echo "✅ تست ۱: بررسی ستون‌های جدول users\n";
$result = $pdo->query("DESCRIBE users");
$columns = [];
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $columns[] = $row['Field'];
}
echo "ستون‌ها: " . implode(', ', $columns) . "\n";

$requiredFields = ['state', 'city', 'education_level', 'favorite_subject', 'profile_completed'];
foreach ($requiredFields as $field) {
    if (in_array($field, $columns)) {
        echo "  ✓ $field موجود است\n";
    } else {
        echo "  ✗ $field وجود ندارد!\n";
    }
}

// تست ۲: بررسی فایل‌های PHP
echo "\n✅ تست ۲: بررسی فایل‌های PHP\n";
$files = [
    'public/profile-complete.php' => 'صفحه تکمیل پروفایل',
    'app/views/auth/profile-complete.php' => 'View تکمیل پروفایل',
    'public/me.php' => 'API کاربر',
];

foreach ($files as $path => $desc) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        echo "  ✓ $desc موجود است\n";
    } else {
        echo "  ✗ $desc وجود ندارد!\n";
    }
}

// تست ۳: بررسی کلاس User
echo "\n✅ تست ۳: بررسی متدهای کلاس User\n";
$userMethods = get_class_methods('User');
$requiredMethods = ['updateProfile'];
foreach ($requiredMethods as $method) {
    if (in_array($method, $userMethods)) {
        echo "  ✓ متد $method موجود است\n";
    } else {
        echo "  ✗ متد $method وجود ندارد!\n";
    }
}

echo "\n✅ تمام تست‌ها انجام شدند!\n";
