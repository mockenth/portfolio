<?php
require_once __DIR__ . '/_init.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $current = $_POST['current']??'';
    $p1 = $_POST['password']??'';
    $p2 = $_POST['password_confirm']??'';
    if (!password_verify($current, $user['password'])) $errors[]='Current password is incorrect.';
    if (strlen($p1)<8) $errors[]='New password too short.';
    if ($p1 !== $p2) $errors[]='Passwords do not match.';
    if (!$errors) {
        db()->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([password_hash($p1,PASSWORD_DEFAULT), $user['id']]);
        flash('success','Password changed.');
        redirect('user/settings.php');
    }
}
render_dashboard_header('Account Settings','user'); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3" style="max-width:480px">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-12"><label>Current Password</label>
    <input type="password" name="current" class="form-control" required></div>
  <div class="col-12"><label>New Password</label>
    <input type="password" name="password" class="form-control" required></div>
  <div class="col-12"><label>Confirm New Password</label>
    <input type="password" name="password_confirm" class="form-control" required></div>
  <div class="col-12"><button class="btn btn-primary">Change Password</button></div>
</form>
<hr class="my-4">
<a href="<?= BASE_URL ?>logout.php" class="btn btn-outline-danger">Secure Logout</a>
<?php render_dashboard_footer(); ?>