<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/bootstrap.php';
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') { header('Location: ' . url('admin/login')); exit; }
$admin = (new User($pdo))->findById((int) $_SESSION['user_id']);
if (!$admin || $admin['role'] !== 'admin') { http_response_code(403); exit('دسترسی به مدیریت مجاز نیست.'); }
$pdo->exec("CREATE TABLE IF NOT EXISTS books (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(180) NOT NULL, description TEXT NOT NULL, image_url VARCHAR(500) NULL, category VARCHAR(100) NULL, is_published BOOLEAN NOT NULL DEFAULT TRUE, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB");
$message = ''; $isError = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) { $message = 'درخواست نامعتبر است.'; $isError = true; }
    else {
        if (($_POST['action'] ?? '') === 'delete') {
            $bookId = (int) ($_POST['id'] ?? 0);
            $findBook = $pdo->prepare('SELECT image_url FROM books WHERE id = :id LIMIT 1');
            $findBook->execute(['id' => $bookId]);
            $bookToDelete = $findBook->fetch();
            if (!$bookToDelete) { $message = 'این کتاب پیدا نشد.'; $isError = true; }
            else {
                $deleteBook = $pdo->prepare('DELETE FROM books WHERE id = :id');
                $deleteBook->execute(['id' => $bookId]);
                $imagePath = (string) ($bookToDelete['image_url'] ?? '');
                if (str_starts_with($imagePath, 'public/images/books/')) { $fullImagePath = dirname(__DIR__) . '/' . $imagePath; if (is_file($fullImagePath)) unlink($fullImagePath); }
                header('Location: ' . url('admin/books')); exit;
            }
        }
        $name = trim((string) ($_POST['name'] ?? '')); $description = trim((string) ($_POST['description'] ?? '')); $category = trim((string) ($_POST['category'] ?? ''));
        $image = $_FILES['image'] ?? null;
        if ($name === '' || $description === '') { $message = 'نام کتاب و توضیحات را کامل کنید.'; $isError = true; }
        elseif (!$image || $image['error'] !== UPLOAD_ERR_OK) { $message = 'لطفاً یک تصویر معتبر بارگذاری کنید.'; $isError = true; }
        elseif ($image['size'] > 3 * 1024 * 1024) { $message = 'حجم تصویر نباید بیشتر از ۳ مگابایت باشد.'; $isError = true; }
        else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($image['tmp_name']); $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
            if (!isset($extensions[$mime])) { $message = 'فرمت تصویر باید JPG، PNG یا WEBP باشد.'; $isError = true; }
            else {
                $directory = __DIR__ . '/images/books'; if (!is_dir($directory)) mkdir($directory, 0755, true);
                $filename = bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
                if (!move_uploaded_file($image['tmp_name'], $directory . '/' . $filename)) { $message = 'ذخیره تصویر انجام نشد.'; $isError = true; }
                else { $statement = $pdo->prepare('INSERT INTO books (name, description, image_url, category) VALUES (:name, :description, :image_url, :category)'); $statement->execute(['name'=>$name,'description'=>$description,'image_url'=>'public/images/books/'.$filename,'category'=>$category ?: null]); $message = 'کتاب با موفقیت اضافه شد.'; }
            }
        }
    }
}
$books = $pdo->query('SELECT id, name, description, image_url, category FROM books ORDER BY created_at DESC, id DESC')->fetchAll();
$adminName = trim((string) ($admin['first_name'] ?? '')) ?: $admin['username'];
?>
<!doctype html><html lang="fa" dir="rtl"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>مدیریت کتاب‌ها | ادب آموز</title><link rel="stylesheet" href="<?= e(url('css/admin-poets.css')) ?>"></head><body><main class="poets-admin-shell"><header class="poets-admin-header"><a class="brand" href="<?= e(url('admin')) ?>"><span>ب</span> ادب آموز</a><a class="back-link" href="<?= e(url('admin')) ?>">بازگشت به داشبورد ←</a></header><section class="page-intro"><div><span class="eyebrow">مدیریت کتابخانه</span><h1>معرفی کتاب‌ها</h1><p>کتاب‌های تازه را با تصویر و توضیحات به کتابخانه اضافه کنید.</p></div><div class="admin-badge">مدیر: <?= e($adminName) ?></div></section><?php if ($message): ?><p class="message <?= $isError ? 'error' : 'success' ?>"><?= e($message) ?></p><?php endif; ?><button class="add-card" id="openBookForm" type="button"><span class="plus">+</span><strong>افزودن کتاب جدید</strong><small>برای بازکردن فرم کلیک کنید</small></button><section class="form-panel" id="bookFormPanel" hidden><div class="panel-title"><div><span class="eyebrow">محتوای جدید</span><h2>اطلاعات کتاب</h2></div><button class="close-button" id="closeBookForm" type="button" aria-label="بستن فرم">×</button></div><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><div class="form-grid"><label>بارگذاری تصویر<input type="file" name="image" accept="image/jpeg,image/png,image/webp" required><small>JPG، PNG یا WEBP، حداکثر ۳ مگابایت</small></label><label>نام کتاب<input type="text" name="name" maxlength="180" required placeholder="مثلاً کلیدر"></label><label>دسته‌بندی<input type="text" name="category" maxlength="100" placeholder="مثلاً رمان ایرانی"></label><label class="full-width">توضیحات<textarea name="description" rows="6" maxlength="5000" required placeholder="توضیح کوتاهی درباره کتاب بنویسید..."></textarea></label></div><button class="submit-button" type="submit">ثبت کتاب <span>←</span></button></form></section><section class="entries"><div class="entries-heading"><h2>کتاب‌های ثبت‌شده</h2><span><?= count($books) ?> مورد</span></div><div class="entries-grid"><?php foreach ($books as $book): ?><article class="entry"><div class="entry-image"><?php if ($book['image_url']): ?><img src="<?= e(url($book['image_url'])) ?>" alt="<?= e($book['name']) ?>"><?php else: ?><span>تصویر ندارد</span><?php endif; ?></div><div class="entry-body"><h3><?= e($book['name']) ?></h3><small><?= e($book['category'] ?: 'کتاب') ?></small><p><?= e($book['description']) ?></p><form method="post" onsubmit="return confirm('این کتاب حذف شود؟');"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $book['id'] ?>"><button class="delete-button" type="submit">حذف کتاب</button></form></div></article><?php endforeach; ?><?php if (!$books): ?><p class="empty">هنوز کتابی ثبت نشده است.</p><?php endif; ?></div></section></main><script>const panel=document.getElementById('bookFormPanel');document.getElementById('openBookForm').onclick=()=>{panel.hidden=false;panel.scrollIntoView({behavior:'smooth',block:'center'});};document.getElementById('closeBookForm').onclick=()=>{panel.hidden=true;};</script></body></html>
