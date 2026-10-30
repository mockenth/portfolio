<?php
require_once __DIR__ . '/_init.php';

$detailId = (int)($_GET['id'] ?? 0);
$services = db()->query("SELECT id,title FROM services WHERE status='published' ORDER BY title")->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $subject = trim($_POST['subject']??'');
    $desc    = trim($_POST['description']??'');
    $serviceId = $_POST['service_id'] ? (int)$_POST['service_id'] : null;
    $budget  = $_POST['budget'] !== '' ? (float)$_POST['budget'] : null;
    $deadline= $_POST['deadline'] ?: null;
    if (strlen($subject)<3) $errors[]='Subject too short.';
    if (strlen($desc)<10)  $errors[]='Description too short.';
    if (!$errors) {
        db()->prepare("INSERT INTO project_inquiries
            (user_id,service_id,subject,description,budget,deadline,status) VALUES (?,?,?,?,?,?,'pending')")
            ->execute([$user['id'],$serviceId,$subject,$desc,$budget,$deadline]);
        db()->prepare("INSERT INTO notifications (user_id,title,message,link) VALUES (?,?,?,?)")
            ->execute([$user['id'],'Inquiry submitted',
                "Your inquiry \"$subject\" has been received.",'user/inquiries.php']);
        flash('success','Inquiry submitted.');
        redirect('user/inquiries.php');
    }
}

$stmt = db()->prepare("
    SELECT i.*, s.title AS service_title FROM project_inquiries i
    LEFT JOIN services s ON s.id=i.service_id
    WHERE i.user_id=? ORDER BY i.created_at DESC");
$stmt->execute([$user['id']]);
$list = $stmt->fetchAll();

$detail = null;
if ($detailId) {
    $stmt = db()->prepare("SELECT * FROM project_inquiries WHERE id=? AND user_id=?");
    $stmt->execute([$detailId, $user['id']]);
    $detail = $stmt->fetch();
}

render_dashboard_header('Project Inquiries','user'); ?>

<?php if ($detail): ?>
<div class="card mb-4 shadow-sm"><div class="card-body">
  <div class="d-flex justify-content-between">
    <h5><?= e($detail['subject']) ?></h5>
    <span class="badge bg-secondary"><?= e($detail['status']) ?></span>
  </div>
  <p class="small text-muted mb-2">Submitted <?= e($detail['created_at']) ?></p>
  <p><?= nl2br(e($detail['description'])) ?></p>
  <?php if ($detail['budget']): ?><p class="small"><strong>Budget:</strong> $<?= number_format((float)$detail['budget'],2) ?></p><?php endif; ?>
  <?php if ($detail['deadline']): ?><p class="small"><strong>Deadline:</strong> <?= e($detail['deadline']) ?></p><?php endif; ?>
  <?php if ($detail['admin_response']): ?>
    <div class="alert alert-info"><strong>Admin Response:</strong><br><?= nl2br(e($detail['admin_response'])) ?></div>
  <?php endif; ?>
  <a href="<?= BASE_URL ?>user/inquiries.php" class="btn btn-sm btn-outline-secondary">Back</a>
</div></div>
<?php endif; ?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h6>New Inquiry</h6>
      <?php foreach ($errors as $e): ?><div class="alert alert-danger small"><?= e($e) ?></div><?php endforeach; ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="mb-2"><input name="subject" class="form-control" placeholder="Subject" required></div>
        <div class="mb-2"><textarea name="description" class="form-control" rows="3"
              placeholder="Describe your project…" required></textarea></div>
        <div class="mb-2"><select name="service_id" class="form-select">
          <option value="">— Select service (optional) —</option>
          <?php foreach ($services as $s): ?>
          <option value="<?= (int)$s['id'] ?>"><?= e($s['title']) ?></option>
          <?php endforeach; ?>
        </select></div>
        <div class="row g-2 mb-2">
          <div class="col"><input type="number" step="0.01" name="budget"
               class="form-control" placeholder="Budget $"></div>
          <div class="col"><input type="date" name="deadline" class="form-control"></div>
        </div>
        <button class="btn btn-primary w-100">Submit Inquiry</button>
      </form>
    </div></div>
  </div>
  <div class="col-lg-7">
    <div class="card"><div class="card-body">
      <h6>My Inquiries</h6>
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>Subject</th><th>Service</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($list as $i): ?>
        <tr>
          <td><a href="?id=<?= (int)$i['id'] ?>"><?= e($i['subject']) ?></a></td>
          <td><?= e($i['service_title']??'—') ?></td>
          <td><span class="badge bg-secondary"><?= e($i['status']) ?></span></td>
          <td class="small text-muted"><?= e(date('Y-m-d', strtotime($i['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$list): ?><tr><td colspan="4" class="text-muted">No inquiries yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div></div>
  </div>
</div>
<?php render_dashboard_footer(); ?>