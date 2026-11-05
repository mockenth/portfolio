<?php
require_once __DIR__ . '/../config/database.php';

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function redirect(string $path): never {
    header('Location: ' . (str_starts_with($path,'http') ? $path : BASE_URL . ltrim($path,'/')));
    exit;
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]+/','-',$text);
    return trim($text,'-');
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): void {
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419); die('Invalid CSRF token.');
    }
}

function flash(string $key, ?string $msg = null) {
    if ($msg !== null) { $_SESSION['flash'][$key] = $msg; return; }
    $out = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $out;
}

function get_setting(string $key, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db()->query("SELECT setting_key, setting_value FROM site_settings") as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

function log_action(?int $userId, string $action, string $description = ''): void {
    db()->prepare("INSERT INTO audit_logs (user_id, action, description, ip_address) VALUES (?,?,?,?)")
        ->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? null]);
}

function validate_upload(array $file): string {
    if ($file['error'] !== UPLOAD_ERR_OK) throw new RuntimeException('Upload error.');
    if ($file['size'] > MAX_UPLOAD_SIZE) throw new RuntimeException('File too large.');
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) throw new RuntimeException('Invalid file type.');
    $ext = match($mime) {
        'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif',default=>'bin'
    };
    return $ext;
}

function save_upload(array $file, string $subdir): string {
    $ext = validate_upload($file);
    $dir = UPLOAD_DIR . $subdir . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
        throw new RuntimeException('Failed to save file.');
    }
    return $subdir . '/' . $name;
}

function paginate(int $total, int $perPage, int $current): array {
    $pages = max(1, (int)ceil($total / $perPage));
    $current = max(1, min($current, $pages));
    return ['total'=>$total,'pages'=>$pages,'current'=>$current,'offset'=>($current-1)*$perPage,'limit'=>$perPage];
}