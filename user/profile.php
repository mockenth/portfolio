<?php
require_once __DIR__ . '/_init.php';
$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $name  = trim($_POST['name']??'');
    $phone = trim($_POST['phone']??'');
    $bio   = trim($_POST['bio']??'');
    if (strlen($name)<2) $errors[]='Name too short.';
    $image = $user['profile_image'];
    if (!empty($_FILES['profile_image']['name'])) {
        try {
            $image = 'profiles/'.basename(save_upload($_FILES['profile_image'],'profiles'));
            // save_upload returns "profiles/xxx" already
            $image = basename($image) ? (str_starts_with($image,'profiles/') ? $image : 'profiles/'.$image) : $image;
        } catch (Throwable $ex) { $errors[]=$ex->getMessage(); }
    }
    if (!$errors) {
        db()->prepare("UPDATE users SET name=?,phone=?,bio=?,profile_image=? WHERE id=?")
            ->execute([$name,$phone,$bio,$image,$user['id']]);
        flash('success','Profile updated.');
        redirect('user/profile.php');
    }
}
render_dashboard_header('My Profile','user'); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="row g-3" style="max-width:640px">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-12 text-center">
    <img src="<?= $user['profile_image']?UPLOAD_URL.e($user['profile_image'])
      :'https://ui-avatars.com/api/?name='.urlencode($user['name']).'&size=120' ?>"
      class="rounded-circle mb-2" width="120" height="120" style="object-fit:cover">
  </div>
  <div class="col-12"><label>Name</label>
    <input name="name" class="form-control" value="<?= e($user['name']) ?>" required></div>
  <div class="col-12"><label>Email (read-only)</label>
    <input class="form-control" value="<?= e($user['email']) ?>" disabled></div>
  <div class="col-12"><label>Phone</label>
    <input name="phone" class="form-control" value="<?= e((string)$user['phone']) ?>"></div>
  <div class="col-12"><label>Bio</label>
    <textarea name="bio" class="form-control" rows="3"><?= e((string)$user['bio']) ?></textarea></div>
  <div class="col-12"><label>Profile Photo</label>
    <input type="file" name="profile_image" class="form-control" accept="image/*"></div>
  <div class="col-12"><button class="btn btn-primary">Save Profile</button></div>
</form>
<?php render_dashboard_footer(); ?>