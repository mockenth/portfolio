<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    if (($_POST['action']??'')==='delete') {
        db()->prepare("DELETE FROM services WHERE id=?")->execute([(int)$_POST['id']]);
        flash('success','Service deleted.');
        redirect('admin/services/index.php');
    }
}
$services = db()->query("SELECT * FROM services ORDER BY id DESC")->fetchAll();
render_dashboard_header('Services', 'admin'); ?>
<div class="d-flex justify-content-between mb-3">
  <a href="<?= BASE_URL ?>admin/services/edit.php" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> New Service</a>
</div>
<div class="table-responsive"><table class="table align-middle">
<thead><tr><th>Title</th><th>Icon</th><th>Price</th><th>Status</th><th></th></tr></thead>
<tbody>
<?php foreach ($services as $s): ?>
<tr>
  <td><?= e($s['title']) ?></td>
  <td><i class="<?= e($s['icon']) ?>"></i></td>
  <td><?= $s['starting_price']?'$'.number_format((float)$s['starting_price'],2):'—' ?></td>
  <td><span class="badge bg-<?= $s['status']==='published'?'success':'secondary' ?>">
      <?= e($s['status']) ?></span></td>
  <td class="text-end">
    <a href="<?= BASE_URL ?>admin/services/edit.php?id=<?= (int)$s['id'] ?>"
       class="btn btn-sm btn-outline-primary">Edit</a>
    <form method="post" class="d-inline">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
      <button class="btn btn-sm btn-outline-danger" data-confirm="Delete service?">Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$services): ?><tr><td colspan="5" class="text-muted text-center">No services.</td></tr><?php endif; ?>
</tbody></table></div>
<?php render_dashboard_footer(); ?>