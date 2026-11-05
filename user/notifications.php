<?php
require_once __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    if (($_POST['action']??'')==='mark_all') {
        db()->prepare("UPDATE notifications SET is_read=1 WHERE user_id=?")->execute([$user['id']]);
        flash('success','All notifications marked as read.');
    } elseif (($_POST['action']??'')==='mark') {
        db()->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?")
            ->execute([(int)$_POST['id'], $user['id']]);
    }
    redirect('user/notifications.php');
}

$stmt = db()->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 100");
$stmt->execute([$user['id']]);
$notes = $stmt->fetchAll();

render_dashboard_header('Notifications','user'); ?>
<div class="d-flex justify-content-between mb-3">
  <span class="text-muted"><?= count($notes) ?> notification(s)</span>
  <form method="post"><input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <button name="action" value="mark_all" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
  </form>
</div>
<div class="list-group">
<?php foreach ($notes as $n): ?>
  <div class="list-group-item <?= $n['is_read']?'':'list-group-item-info' ?>">
    <div class="d-flex justify-content-between">
      <div>
        <strong><?= e($n['title']) ?></strong>
        <div class="small"><?= e($n['message']) ?></div>
        <?php if ($n['link']): ?>
        <a href="<?= BASE_URL.e($n['link']) ?>" class="small">Open →</a>
        <?php endif; ?>
      </div>
      <div class="text-end">
        <div class="small text-muted"><?= e(date('Y-m-d H:i',strtotime($n['created_at']))) ?></div>
        <?php if (!$n['is_read']): ?>
        <form method="post" class="mt-1">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="action" value="mark">
          <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
          <button class="btn btn-sm btn-outline-primary">Mark read</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$notes): ?><div class="alert alert-info">No notifications.</div><?php endif; ?>
</div>
<?php render_dashboard_footer(); ?>