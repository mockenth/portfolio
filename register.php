<?php
require_once __DIR__ . '/includes/auth.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password_confirm'] ?? '';
    if (strlen($name) < 2) $errors[]='Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email.';
    if (strlen($pass) < 8) $errors[]='Password must be at least 8 characters.';
    if ($pass !== $pass2) $errors[]='Passwords do not match.';
    if (!$errors) {
        $stmt = db()->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errors[]='Email already registered.';
    }
    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        db()->prepare("INSERT INTO users (name,email,password,role,status) VALUES (?,?,?,'user','active')")
            ->execute([$name,$email,$hash]);
        flash('success','Account created. Please log in.');
        redirect('login.php');
    }
}
$pageTitle = 'Register'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-6">
<div class="card shadow-sm"><div class="card-body">
<h3 class="mb-3">Create Account</h3>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<div class="mb-3"><label class="form-label">Name</label>
<input name="name" class="form-control" value="<?= e($_POST['name']??'') ?>" required></div>
<div class="mb-3"><label class="form-label">Email</label>
<input type="email" name="email" class="form-control" value="<?= e($_POST['email']??'') ?>" required></div>
<div class="mb-3"><label class="form-label">Password</label>
<input type="password" name="password" class="form-control" required></div>
<div class="mb-3"><label class="form-label">Confirm Password</label>
<input type="password" name="password_confirm" class="form-control" required></div>
<button class="btn btn-primary w-100">Register</button>
</form></div></div></div></div>
<?php include 'includes/footer.php'; ?>