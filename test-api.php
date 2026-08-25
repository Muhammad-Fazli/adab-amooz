<?php
ob_start();
require_once __DIR__ . '/config/bootstrap.php';

echo "=== 🧪 تست API‌ها ===\n\n";

// تست ۱: API me
echo "1️⃣ تست API me (بدون لاگین):\n";
$_SESSION = [];
ob_start();
require __DIR__ . '/public/me.php';
$output = ob_get_clean();
$data = json_decode($output, true);
if ($data && isset($data['authenticated'])) {
    echo "   ✓ API me درست کار می‌کند\n";
    echo "   - authenticated: " . ($data['authenticated'] ? 'true' : 'false') . "\n";
} else {
    echo "   ✗ API me مشکل دارد: " . substr($output, 0, 100) . "\n";
}

// تست ۲: API home-content
echo "\n2️⃣ تست API home-content:\n";
ob_start();
require __DIR__ . '/public/home-content.php';
$output = ob_get_clean();
$data = json_decode($output, true);
if ($data && isset($data['hero_title'])) {
    echo "   ✓ API home-content درست کار می‌کند\n";
    echo "   - hero_title: " . substr($data['hero_title'], 0, 30) . "...\n";
} else {
    echo "   ✗ API home-content مشکل دارد: " . substr($output, 0, 100) . "\n";
}

echo "\n✅ تست‌ها انجام شدند!\n";
ob_end_flush();
