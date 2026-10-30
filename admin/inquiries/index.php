<?php
require_once __DIR__ . '/../_init.php';

$statuses = ['pending','in_review','approved','in_progress','completed','rejected'];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $id = (int)($_POST['id']??0);
    $newStatus = in_array($_POST['status']??'', $statuses, true) ? $_POST['status'] : 'pending';
    $response = trim($_POST['admin_response']??'');
    db()->prepare("UPDATE project_inquiries SET status=?, admin_response=? WHERE id=?")
        ->execute([$newStatus,$response ?: null, $id]);
    // notify user
    $stmt = db()->prepare("SELECT user_id, subject FROM project_inquiries WHERE id=?");
    $stmt->execute([$id]); $row = $stmt->fetch();
    if ($row) {
        db()->prepare("INSERT INTO notifications (user_id,title,message,link) VALUES (?,?,?,?)")
            ->execute([$row['user_id'],'Inquiry updated',
                "Your inquiry \"{$row['subject']}\" status is now $newStatus.",
                "user/inquiries.php"]);
    }
    flash('success','Inquiry updated.');
    redirect('admin/inquiries/index.php');
}

$stmt = db()->query("
    SELECT i.*, u.name AS user_name, u.email AS user_email, s.title AS service_title
    FROM project_inquiries i
    JOIN users u ON u.id=i.user_id
    LEFT JOIN services s ON s.id=i.service_id
    ORDER BY i.created_at DESC LIMIT 100");
$inquiries = $stmt->fetchAll();

render_dashboard_header('Inquiries','admin'); ?>
<?php foreach ($inquiries as $i): ?>
<div class="card mb-3 shadow-sm">
  <div class="card-body">
    <div class="d-flex justify-content-between">
      <h6><?= e($i['subject']) ?>
        <span class="badge bg-secondary ms-2"><?= e($i['status']) ?></span></h6>
      <small class="text-muted"><?= e($i['created_at']) ?></small>
    </div>
    <p class="small mb-1">
      From <strong><?= e($i['user_name']) ?></strong> (<?= e($i['user_email']) ?>)
      <?= $i['service_title'] ? '· Service: '.e($i['service_title']) : '' ?>
      <?= $i['budget'] ? '· Budget: $'.number_format((float)$i['budget'],2) : '' ?>
      <?= $i['deadline'] ? '· Deadline: '.e($i['deadline']) : '' ?>
    </p>
    <p class="mb-2"><?= nl2br(e($i['description'])) ?></p>
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="id" value="<?= (int)$i['id'] ?>">
      <div class="col-md-3">
        <select name="status" class="form-select form-select-sm">
          <?php foreach ($statuses as $st): ?>
          <option value="<?= $st ?>" <?= $i['status']===$st?'selected':'' ?>><?= $st ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-7">
        <input name="admin_response" class="form-control form-control-sm"
               placeholder="Response to user…" value="<?= e((string)$i['admin_response']) ?>">
      </div>
      <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Update</button></div>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php if (!$inquiries): ?><div class="alert alert-info">No inquiries yet.</div><?php endif; ?>
<?php render_dashboard_footer(); ?>