<?php
declare(strict_types=1);

// Debug settings (put these AFTER the declare statement)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('APP_NAME', 'Portfolio');
define('BASE_URL', 'http://localhost/portfolio/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg','image/png','image/webp','image/gif']);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true, 'samesite' => 'Lax'
    ]);
    session_start();
}
date_default_timezone_set('UTC');