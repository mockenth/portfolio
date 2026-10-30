<?php
require_once __DIR__ . '/../_init.php';

$id = (int)($_GET['id'] ?? 0);
$s = ['title'=>'','slug'=>'','description'=>'','icon'=>'bi bi-briefcase',
      'starting_price'=>'','status'=>'published'];
$errors=[];
if ($id) {
    $stmt = db()->prepare("SELECT * FROM services WHERE id=?");
    $stmt->execute([$id]);
    $s = $stmt->fetch() ?: $s;
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $s['title'] = trim($_POST['title']??'');
    $s['slug']  = slugify($_POST['slug']??$s['title']);
    $s['description'] = trim($_POST['description']??'');
    $s['icon'] = trim($_POST['icon']??'bi bi-briefcase');
    $s['starting_price'] = $_POST['starting_price']!=='' ? (float)$_POST['starting_price'] : null;
    $s['status'] = in_array($_POST['status']??'',['draft','published'],true) ? $_POST['status'] : 'published';
    if (strlen($s['title'])<2) $errors[]='Title too short.';
    if (strlen($s['description'])<5) $errors[]='Description too short.';
    if (!$errors) {
        if ($id) {
            db()->prepare("UPDATE services SET title=?,slug=?,description=?,icon=?,starting_price=?,status=? WHERE id=?")
                ->execute([$s['title'],$s['slug'],$s['description'],$s['icon'],$s['starting_price'],$s['status'],$id]);
        } else {
            db()->prepare("INSERT INTO services (title,slug,description,icon,starting_price,status) VALUES (?,?,?,?,?,?)")
                ->execute([$s['title'],$s['slug'],$s['description'],$s['icon'],$s['starting_price'],$s['status']]);
        }
        flash('success','Service saved.');
        redirect('admin/services/index.php');
    }
}
render_dashboard_header($id?'Edit Service':'New Service','admin'); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post" class="row g-3">
  <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
  <div class="col-md-8"><label>Title</label>
    <input name="title" class="form-control" required value="<?= e($s['title']) ?>"></div>
  <div class="col-md-4"><label>Slug</label>
    <input name="slug" class="form-control" value="<?= e($s['slug']) ?>"></div>
  <div class="col-12"><label>Description</label>
    <textarea name="description" class="form-control" rows="4" required><?= e($s['description']) ?></textarea></div>
  <div class="col-md-4"><label>Icon Class</label>
    <input name="icon" class="form-control" value="<?= e($s['icon']) ?>">
    <small class="text-muted">Bootstrap Icons, e.g. <code>bi bi-code-slash</code></small></div>
  <div class="col-md-4"><label>Starting Price</label>
    <input type="number" step="0.01" name="starting_price" class="form-control"
           value="<?= e((string)$s['starting_price']) ?>"></div>
  <div class="col-md-4"><label>Status</label>
    <select name="status" class="form-select">
      <option value="published" <?= $s['status']==='published'?'selected':'' ?>>Published</option>
      <option value="draft" <?= $s['status']==='draft'?'selected':'' ?>>Draft</option>
    </select></div>
  <div class="col-12">
    <button class="btn btn-primary">Save</button>
    <a href="<?= BASE_URL ?>admin/services/index.php" class="btn btn-secondary">Cancel</a>
  </div>
</form>
<?php render_dashboard_footer(); ?>