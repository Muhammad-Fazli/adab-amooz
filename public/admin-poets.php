<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ' . url('admin/login'));
    exit;
}

$admin = (new User($pdo))->findById((int) $_SESSION['user_id']);
if ($admin === null || $admin['role'] !== 'admin') {
    http_response_code(403);
    exit('دسترسی به مدیریت مجاز نیست.');
}

$message = '';
$isError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
        $message = 'درخواست نامعتبر است.';
        $isError = true;
    } else {
        if (($_POST['action'] ?? '') === 'delete') {
            $poetId = (int) ($_POST['id'] ?? 0);
            $findPoet = $pdo->prepare('SELECT image_url FROM poets WHERE id = :id LIMIT 1');
            $findPoet->execute(['id' => $poetId]);
            $poetToDelete = $findPoet->fetch();
            if (!$poetToDelete) {
                $message = 'این معرفی پیدا نشد.';
                $isError = true;
            } else {
                $deletePoet = $pdo->prepare('DELETE FROM poets WHERE id = :id');
                $deletePoet->execute(['id' => $poetId]);
                $imagePath = (string) ($poetToDelete['image_url'] ?? '');
                if (str_starts_with($imagePath, 'public/images/poets/')) {
                    $fullImagePath = dirname(__DIR__) . '/' . $imagePath;
                    if (is_file($fullImagePath)) {
                        unlink($fullImagePath);
                    }
                }
                $message = 'معرفی با موفقیت حذف شد.';
            }
        } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $biography = trim((string) ($_POST['biography'] ?? ''));
        $category = in_array($_POST['category'] ?? '', ['poet', 'writer'], true) ? $_POST['category'] : 'poet';
        $period = trim((string) ($_POST['period'] ?? ''));
        $imageUrl = null;
        $image = $_FILES['image'] ?? null;

        if ($name === '' || $biography === '') {
            $message = 'نام و توضیحات را کامل کنید.';
            $isError = true;
        } elseif (!$image || $image['error'] !== UPLOAD_ERR_OK) {
            $message = 'لطفاً یک تصویر معتبر بارگذاری کنید.';
            $isError = true;
        } elseif ($image['size'] > 3 * 1024 * 1024) {
            $message = 'حجم تصویر نباید بیشتر از ۳ مگابایت باشد.';
            $isError = true;
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($image['tmp_name']);
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!isset($extensions[$mime])) {
                $message = 'فرمت تصویر باید JPG، PNG یا WEBP باشد.';
                $isError = true;
            } else {
                $directory = __DIR__ . '/images/poets';
                if (!is_dir($directory)) {
                    mkdir($directory, 0755, true);
                }
                $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                if (!move_uploaded_file($image['tmp_name'], $directory . '/' . $filename)) {
                    $message = 'ذخیره تصویر انجام نشد.';
                    $isError = true;
                } else {
                    $slug = strtolower(trim((string) preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-')) ?: bin2hex(random_bytes(6));
                    $slug .= '-' . bin2hex(random_bytes(3));
                    $statement = $pdo->prepare(
                        'INSERT INTO poets (name, slug, period, biography, image_url, category, era)
                         VALUES (:name, :slug, :period, :biography, :image_url, :category, :era)'
                    );
                    $statement->execute([
                        'name' => $name,
                        'slug' => $slug,
                        'period' => $period !== '' ? $period : null,
                        'biography' => $biography,
                        'image_url' => 'public/images/poets/' . $filename,
                        'category' => $category,
                        'era' => 'contemporary',
                    ]);
                    $message = 'معرفی با موفقیت اضافه شد.';
                }
            }
        }
        }
    }
}

$poets = $pdo->query('SELECT id, name, period, biography, image_url, category FROM poets ORDER BY created_at DESC, id DESC')->fetchAll();
$adminName = trim((string) ($admin['first_name'] ?? '')) ?: (string) $admin['username'];
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>معرفی شاعران و نویسندگان | ادب آموز</title>
    <link rel="stylesheet" href="<?= e(url('css/admin-poets.css')) ?>">
</head>
<body>
    <main class="poets-admin-shell">
        <header class="poets-admin-header">
            <a class="brand" href="<?= e(url('admin')) ?>"><span>ب</span> ادب آموز</a>
            <a class="back-link" href="<?= e(url('admin')) ?>">بازگشت به داشبورد ←</a>
        </header>
        <section class="page-intro">
            <div><span class="eyebrow">مدیریت محتوای ادبی</span><h1>معرفی نویسندگان و شاعران</h1><p>چهره‌های تازه را با تصویر و توضیحات کامل به گنجینه ادب آموز اضافه کنید.</p></div>
            <div class="admin-badge">مدیر: <?= e($adminName) ?></div>
        </section>
        <?php if ($message): ?><p class="message <?= $isError ? 'error' : 'success' ?>"><?= e($message) ?></p><?php endif; ?>
        <button class="add-card" id="openPoetForm" type="button"><span class="plus">+</span><strong>افزودن معرفی جدید</strong><small>برای بازکردن فرم کلیک کنید</small></button>
        <section class="form-panel" id="poetFormPanel" hidden>
            <div class="panel-title"><div><span class="eyebrow">محتوای جدید</span><h2>اطلاعات نویسنده یا شاعر</h2></div><button class="close-button" id="closePoetForm" type="button" aria-label="بستن فرم">×</button></div>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-grid">
                    <label>بارگذاری تصویر<input type="file" name="image" accept="image/jpeg,image/png,image/webp" required><small>JPG، PNG یا WEBP، حداکثر ۳ مگابایت</small></label>
                    <label>اسم نویسنده یا شاعر<input type="text" name="name" maxlength="150" required placeholder="مثلاً سیمین بهبهانی"></label>
                    <label>دوره یا عنوان کوتاه<input type="text" name="period" maxlength="100" placeholder="مثلاً معاصر"></label>
                    <label>نوع معرفی<select name="category"><option value="poet">شاعر</option><option value="writer">نویسنده</option></select></label>
                    <label class="full-width">توضیحات<textarea name="biography" rows="6" maxlength="5000" required placeholder="زندگی، آثار و جایگاه ادبی را بنویسید..."></textarea></label>
                </div>
                <button class="submit-button" type="submit">ثبت معرفی <span>←</span></button>
            </form>
        </section>
        <section class="entries"><div class="entries-heading"><h2>معرفی‌های ثبت‌شده</h2><span><?= count($poets) ?> مورد</span></div><div class="entries-grid">
            <?php foreach ($poets as $poet): ?><article class="entry"><div class="entry-image"><?php if (!empty($poet['image_url'])): ?><img src="<?= e(url((string) $poet['image_url'])) ?>" alt="<?= e($poet['name']) ?>"><?php else: ?><span>تصویر ندارد</span><?php endif; ?></div><div class="entry-body"><h3><?= e($poet['name']) ?></h3><small><?= $poet['category'] === 'writer' ? 'نویسنده' : 'شاعر' ?><?= $poet['period'] ? ' · ' . e($poet['period']) : '' ?></small><p><?= e($poet['biography']) ?></p><form method="post" onsubmit="return confirm('این معرفی حذف شود؟');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $poet['id'] ?>"><button class="delete-button" type="submit">حذف معرفی</button></form></div></article><?php endforeach; ?>
            <?php if (!$poets): ?><p class="empty">هنوز معرفی‌ای ثبت نشده است.</p><?php endif; ?>
        </div></section>
    </main>
    <script>
        const formPanel = document.getElementById('poetFormPanel');
        document.getElementById('openPoetForm').addEventListener('click', () => { formPanel.hidden = false; formPanel.scrollIntoView({ behavior: 'smooth', block: 'center' }); });
        document.getElementById('closePoetForm').addEventListener('click', () => { formPanel.hidden = true; });
    </script>
</body>
</html>
