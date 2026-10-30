<?php
require_once __DIR__ . '/../_init.php';

$keys = ['owner_name','owner_title','owner_bio','owner_email','owner_phone',
         'social_github','social_linkedin','social_twitter',
         'site_title','site_description','footer_text'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $stmt = db()->prepare("UPDATE site_settings SET setting_value=? WHERE setting_key=?");
    foreach ($keys as $k) if (isset($_POST[$k])) $stmt->execute([trim($_POST[$k]), $k]);
    flash('success','Content saved.');
    redirect('admin/content/index.php');
}

render_dashboard_header('Website Content','admin'); ?>
<form method="post" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <?php foreach ($keys as $k): ?>
  <div class="col-md-6">
    <label class="form-label text-capitalize"><?= str_replace('_',' ',$k) ?></label>
    <?php if (in_array($k,['owner_bio','site_description'],true)): ?>
      <textarea class="form-control" name="<?= $k ?>" rows="3"><?= e(get_setting($k)) ?></textarea>
    <?php else: ?>
      <input class="form-control" name="<?= $k ?>" value="<?= e(get_setting($k)) ?>">
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  <div class="col-12"><button class="btn btn-primary">Save Changes</button></div>
</form>
<?php render_dashboard_footer(); ?>