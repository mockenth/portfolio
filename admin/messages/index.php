<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $id = (int)($_POST['id']??0);
    $action = $_POST['action']??'';
    if ($action==='read')   db()->prepare("UPDATE contact_messages SET status='read' WHERE id=?")->execute([$id]);
    if ($action==='archive')db()->prepare("UPDATE contact_messages SET status='archived' WHERE id=?")->execute([$id]);
    if ($action==='delete') db()->prepare("DELETE FROM contact_messages WHERE id=?")->execute([$id]);
    flash('success','Message updated.');
    redirect('admin/messages/index.php');
}

$filter = $_GET['filter']??'all';
$where = in_array($filter,['unread','read','archived'],true) ? "WHERE status=?" : '';
$params = $where ? [$filter] : [];
$stmt = db()->prepare("SELECT * FROM contact_messages $where ORDER BY created_at DESC LIMIT 200");
$stmt->execute($params);
$msgs = $stmt->fetchAll();

render_dashboard_header('Messages','admin'); ?>
<div class="btn-group mb-3">
  <?php foreach (['all'=>'All','unread'=>'Unread','read'=>'Read','archived'=>'Archived'] as $k=>$lbl): ?>
  <a class="btn btn-<?= $filter===$k?'primary':'outline-primary' ?>"
     href="?filter=<?= $k ?>"><?= e($lbl) ?></a>
  <?php endforeach; ?>
</div>
<div class="table-responsive"><table class="table align-middle">
<thead><tr><th>From</th><th>Subject</th><th>Status</th><th>Date</th><th></th></tr></thead>
<tbody>
<?php foreach ($msgs as $m): ?>
<tr>
  <td><?= e($m['name']) ?><div class="small text-muted"><?= e($m['email']) ?></div></td>
  <td><?= e($m['subject']) ?><div class="small text-muted"><?= e(mb_strimwidth($m['message'],0,70,'…')) ?></div></td>
  <td><span class="badge bg-<?= ['unread'=>'danger','read'=>'success','archived'=>'secondary'][$m['status']] ?>">
      <?= e($m['status']) ?></span></td>
  <td class="small"><?= e($m['created_at']) ?></td>
  <td class="text-end">
    <a href="<?= BASE_URL ?>admin/messages/view.php?id=<?= (int)$m['id'] ?>"
       class="btn btn-sm btn-outline-primary">Open</a>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$msgs): ?><tr><td colspan="5" class="text-center text-muted">No messages.</td></tr><?php endif; ?>
</tbody></table></div>
<?php render_dashboard_footer(); ?>