<?php
require_once __DIR__ . '/includes/auth.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    if (!rate_limit('login', 8, 300)) $errors[]='Too many attempts. Try again later.';
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (!$errors) {
        $stmt = db()->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && $user['status']==='active' && password_verify($pass, $user['password'])) {
            login_user($user);
            log_action((int)$user['id'], 'login');
            redirect($user['role']==='admin' ? 'admin/dashboard.php' : 'user/dashboard.php');
        } else {
            $errors[] = 'Invalid credentials or inactive account.';
        }
    }
}
$pageTitle='Login'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card shadow-sm"><div class="card-body">
<h3 class="mb-3">Login</h3>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<div class="mb-3"><label class="form-label">Email</label>
<input type="email" name="email" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Password</label>
<input type="password" name="password" class="form-control" required></div>
<button class="btn btn-primary w-100">Login</button>
</form>
<div class="mt-2 small"><a href="forgot-password.php">Forgot password?</a></div>
</div></div></div></div>
<?php include 'includes/footer.php'; ?>