<?php

require_once __DIR__ . '/../config/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !hash_equals(csrf_token(), (string) ($_POST['csrf_token'] ?? ''))) {
	http_response_code(405);
	exit('درخواست خروج نامعتبر است.');
}

(new AuthController(new User($pdo)))->logout();
header('Location: ' . url());
exit;