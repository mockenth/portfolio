<?php
require_once __DIR__ . '/../_init.php';
$id = (int)($_GET['id']??0);
$stmt = db()->prepare("SELECT * FROM contact_messages WHERE id=?");
$stmt->execute([$id]);
$m = $stmt->fetch();
if (!$m) { flash('error','Not found.'); redirect('admin/messages/index.php'); }
if ($m['status']==='unread') {
    db()->prepare("UPDATE contact_messages SET status='read' WHERE id=?")->execute([$id]);
    $m['status']='read';
}
render_dashboard_header('Message','admin'); ?>
<div class="card shadow-sm">
  <div class="card-body">
    <h5><?= e($m['subject']) ?></h5>
    <p class="small text-muted">
      From <strong><?= e($m['name']) ?></strong> &lt;<?= e($m['email']) ?>&gt;
      — <?= e($m['created_at']) ?>
    </p>
    <p><?= nl2br(e($m['message'])) ?></p>
    <a class="btn btn-outline-secondary" href="mailto:<?= e($m['email']) ?>?subject=Re: <?= e($m['subject']) ?>">
      Reply by Email</a>
    <form method="post" action="<?= BASE_URL ?>admin/messages/index.php" class="d-inline">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
      <button name="action" value="archive" class="btn btn-outline-warning">Archive</button>
      <button name="action" value="delete" class="btn btn-outline-danger" data-confirm="Delete?">Delete</button>
    </form>
    <a class="btn btn-link" href="<?= BASE_URL ?>admin/messages/index.php">Back</a>
  </div>
</div>
<?php render_dashboard_footer(); ?>