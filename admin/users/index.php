<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $targetId = (int)($_POST['id']??0);
    $action   = $_POST['action']??'';
    // prevent admin from demoting themselves
    if ($targetId !== (int)$admin['id']) {
        if ($action==='activate')
            db()->prepare("UPDATE users SET status='active' WHERE id=?")->execute([$targetId]);
        elseif ($action==='deactivate')
            db()->prepare("UPDATE users SET status='inactive' WHERE id=?")->execute([$targetId]);
        flash('success','User updated.');
    } else {
        flash('error','You cannot change your own status.');
    }
    redirect('admin/users/index.php');
}

$q = trim($_GET['q']??'');
$where='WHERE 1=1'; $params=[];
if ($q!=='') { $where.=" AND (name LIKE ? OR email LIKE ?)"; $params[]="%$q%"; $params[]="%$q%"; }
$stmt = db()->prepare("SELECT * FROM users $where ORDER BY created_at DESC");
$stmt->execute($params);
$users = $stmt->fetchAll();

render_dashboard_header('Users','admin'); ?>
<form class="mb-3 d-flex gap-2" method="get">
  <input name="q" class="form-control" placeholder="Search name/email…" value="<?= e($q) ?>">
  <button class="btn btn-outline-secondary">Search</button>
</form>
<div class="table-responsive"><table class="table align-middle">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Joined</th><th></th></tr></thead>
<tbody>
<?php foreach ($users as $u): ?>
<tr>
  <td><?= e($u['name']) ?></td>
  <td><?= e($u['email']) ?></td>
  <td><span class="badge bg-<?= $u['role']==='admin'?'dark':'primary' ?>"><?= e($u['role']) ?></span></td>
  <td><span class="badge bg-<?= $u['status']==='active'?'success':'secondary' ?>"><?= e($u['status']) ?></span></td>
  <td class="small text-muted"><?= e(date('Y-m-d', strtotime($u['created_at']))) ?></td>
  <td class="text-end">
    <?php if ((int)$u['id'] !== (int)$admin['id']): ?>
    <form method="post" class="d-inline">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
      <input type="hidden" name="action" value="<?= $u['status']==='active'?'deactivate':'activate' ?>">
      <button class="btn btn-sm btn-outline-<?= $u['status']==='active'?'warning':'success' ?>">
        <?= $u['status']==='active'?'Deactivate':'Activate' ?></button>
    </form>
    <?php endif; ?>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table></div>
<?php render_dashboard_footer(); ?>