<?php
require_once __DIR__ . '/includes/auth.php';
$id = (int)($_GET['id']??0);
$token = $_GET['token']??'';
$hash = hash('sha256', $token);
$stmt = db()->prepare("SELECT * FROM password_resets WHERE user_id=? AND token_hash=? AND used=0 AND expires_at > NOW() ORDER BY id DESC LIMIT 1");
$stmt->execute([$id,$hash]);
$reset = $stmt->fetch();
$errors=[];
if (!$reset) { $errors[]='Invalid or expired reset link.'; }
if ($_SERVER['REQUEST_METHOD']==='POST' && $reset) {
    verify_csrf($_POST['csrf']??null);
    $p1 = $_POST['password']??''; $p2 = $_POST['password_confirm']??'';
    if (strlen($p1)<8) $errors[]='Password too short.';
    if ($p1!==$p2) $errors[]='Passwords do not match.';
    if (!$errors) {
        db()->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([password_hash($p1, PASSWORD_DEFAULT), $id]);
        db()->prepare("UPDATE password_resets SET used=1 WHERE id=?")->execute([$reset['id']]);
        flash('success','Password updated. Please log in.');
        redirect('login.php');
    }
}
$pageTitle='Reset Password'; include 'includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-md-5"><div class="card"><div class="card-body">
<h3>Reset Password</h3>
<?php foreach($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<?php if($reset): ?>
<form method="post">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<div class="mb-3"><label>New Password</label><input type="password" name="password" class="form-control" required></div>
<div class="mb-3"><label>Confirm</label><input type="password" name="password_confirm" class="form-control" required></div>
<button class="btn btn-primary w-100">Update Password</button>
</form>
<?php endif; ?></div></div></div></div>
<?php include 'includes/footer.php'; ?>