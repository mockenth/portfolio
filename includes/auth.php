<?php
require_once __DIR__ . '/functions.php';

function current_user(): ?array {
    if (empty($_SESSION['user_id'])) return null;
    static $user = null;
    if ($user === null) {
        $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND status='active' LIMIT 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch() ?: null;
    }
    return $user;
}

function is_logged_in(): bool { return current_user() !== null; }
function is_admin(): bool { $u = current_user(); return $u && $u['role']==='admin'; }

function require_login(): array {
    $u = current_user();
    if (!$u) { flash('error','Please log in.'); redirect('login.php'); }
    return $u;
}
function require_admin(): array {
    $u = require_login();
    if ($u['role'] !== 'admin') { http_response_code(403); die('Forbidden.'); }
    return $u;
}

function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['role'] = $user['role'];
}

function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);
    }
    session_destroy();
}

// Simple in-session rate limiter
function rate_limit(string $key, int $max = 5, int $window = 300): bool {
    $now = time();
    $_SESSION['rl'][$key] = array_values(array_filter(
        $_SESSION['rl'][$key] ?? [], fn($t) => $t > $now - $window
    ));
    if (count($_SESSION['rl'][$key]) >= $max) return false;
    $_SESSION['rl'][$key][] = $now;
    return true;
}