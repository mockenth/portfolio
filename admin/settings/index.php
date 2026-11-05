<?php
require_once __DIR__ . '/../_init.php';
$errors=[];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $name  = trim($_POST['name']??'');
    $email = trim($_POST['email']??'');
    $p1 = $_POST['password']??'';
    $p2 = $_POST['password_confirm']??'';
    
    if (strlen($name)<2) $errors[]='Name too short.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[]='Invalid email.';
    if ($p1 !== '' && strlen($p1)<8) $errors[]='Password min 8 chars.';
    if ($p1 !== $p2) $errors[]='Passwords do not match.';

    // 1. Handle Profile Image Upload
    $currentImage = get_setting('profile_image');
    if (!empty($_FILES['profile_image']['name'])) {
        try {
            $currentImage = save_upload($_FILES['profile_image'], 'profiles');
            
            // Save to site_settings table so homepage can read it
            $stmt = db()->prepare("SELECT COUNT(*) FROM site_settings WHERE setting_key='profile_image'");
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                db()->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key='profile_image'")->execute([$currentImage]);
            } else {
                db()->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('profile_image', ?)")->execute([$currentImage]);
            }
        } catch (Throwable $ex) { $errors[]=$ex->getMessage(); }
    }

    // 2. Handle Resume Upload
    $currentResume = get_setting('resume_file');
    if (!empty($_FILES['resume_file']['name'])) {
        try {
            // We save it in the 'documents' folder
            $currentResume = save_upload($_FILES['resume_file'], 'documents');
            
            $stmt = db()->prepare("SELECT COUNT(*) FROM site_settings WHERE setting_key='resume_file'");
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                db()->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key='resume_file'")->execute([$currentResume]);
            } else {
                db()->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES ('resume_file', ?)")->execute([$currentResume]);
            }
        } catch (Throwable $ex) { $errors[]=$ex->getMessage(); }
    }

    // 3. Update User Account Info
    if (!$errors) {
        if ($p1 !== '') {
            $hashed = password_hash($p1, PASSWORD_DEFAULT);
            db()->prepare("UPDATE users SET name=?, email=?, password=? WHERE id=?")
                ->execute([$name, $email, $hashed, $admin['id']]);
        } else {
            db()->prepare("UPDATE users SET name=?, email=? WHERE id=?")
                ->execute([$name, $email, $admin['id']]);
        }
        
        flash('success','Account updated successfully. Please use your new credentials next time you log in.');
        redirect('admin/settings/index.php');
    }
}
render_dashboard_header('Admin Settings','admin'); ?>

<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>

<form method="post" enctype="multipart/form-data" class="row g-3" style="max-width:640px">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  
  <!-- Profile Image Section -->
  <div class="col-12 text-center">
    <?php $img = get_setting('profile_image'); ?>
    <img src="<?= $img ? UPLOAD_URL.e($img) : 'https://ui-avatars.com/api/?name='.urlencode($admin['name']).'&size=120' ?>" 
         class="rounded-circle mb-2" width="120" height="120" style="object-fit:cover">
    <input type="file" name="profile_image" class="form-control" accept="image/*">
    <small class="text-muted">This photo appears on the public homepage.</small>
  </div>

  <!-- Resume Section -->
  <div class="col-12 mt-4">
    <label class="form-label fw-bold">Upload ATS Resume (PDF)</label>
    <input type="file" name="resume_file" class="form-control" accept=".pdf">
    <?php if(get_setting('resume_file')): ?>
        <small class="text-success d-block mt-1">
            <i class="bi bi-check-circle"></i> Resume currently active. Upload a new file to replace it.
        </small>
    <?php else: ?>
        <small class="text-muted d-block mt-1">
            <i class="bi bi-exclamation-circle"></i> No resume uploaded yet.
        </small>
    <?php endif; ?>
  </div>

  <hr class="mt-4">

  <!-- Account Info Section -->
  <div class="col-12"><label>Name</label>
    <input name="name" class="form-control" value="<?= e($admin['name']) ?>" required></div>
  <div class="col-12"><label>Email</label>
    <input type="email" name="email" class="form-control" value="<?= e($admin['email']) ?>" required></div>
  <div class="col-12"><label>New Password <small class="text-muted">(leave blank to keep current)</small></label>
    <input type="password" name="password" class="form-control"></div>
  <div class="col-12"><label>Confirm New Password</label>
    <input type="password" name="password_confirm" class="form-control"></div>
    
  <div class="col-12 mt-4">
    <button class="btn btn-primary w-100">Save Settings</button>
  </div>
</form>

<?php render_dashboard_footer(); ?>