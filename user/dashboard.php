<?php
require_once __DIR__ . '/_init.php';

$favCount = (int)db()->prepare("SELECT COUNT(*) FROM favorites WHERE user_id=?")
    ->execute([$user['id']]) ? 0 : 0;
$stmt = db()->prepare("SELECT COUNT(*) FROM favorites WHERE user_id=?");
$stmt->execute([$user['id']]); $favCount = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM project_inquiries WHERE user_id=?");
$stmt->execute([$user['id']]); $inqCount = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0");
$stmt->execute([$user['id']]); $unread = (int)$stmt->fetchColumn();

$stmt = db()->prepare("
    SELECT i.*, s.title AS service_title FROM project_inquiries i
    LEFT JOIN services s ON s.id=i.service_id
    WHERE i.user_id=? ORDER BY i.created_at DESC LIMIT 5");
$stmt->execute([$user['id']]);
$recentInq = $stmt->fetchAll();

$stmt = db()->prepare("
    SELECT p.id, p.title, p.slug, p.thumbnail FROM favorites f
    JOIN projects p ON p.id=f.project_id
    WHERE f.user_id=? ORDER BY f.created_at DESC LIMIT 4");
$stmt->execute([$user['id']]);
$favProjects = $stmt->fetchAll();

render_dashboard_header('Dashboard', 'user'); ?>
<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
    <div class="fs-4 fw-bold"><?= $favCount ?></div>
    <div class="small text-secondary">Saved Projects</div>
  </div></div></div>
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
    <div class="fs-4 fw-bold"><?= $inqCount ?></div>
    <div class="small text-secondary">Inquiries</div>
  </div></div></div>
  <div class="col-md-4"><div class="card shadow-sm"><div class="card-body">
    <div class="fs-4 fw-bold"><?= $unread ?></div>
    <div class="small text-secondary">Unread Notifications</div>
  </div></div></div>
</div>

<p>Welcome back, <strong><?= e($user['name']) ?></strong>.</p>

<div class="row g-3">
  <div class="col-lg-7">
    <div class="card"><div class="card-body">
      <h6>Recent Inquiries</h6>
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>Subject</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($recentInq as $i): ?>
        <tr>
          <td><a href="<?= BASE_URL ?>user/inquiries.php?id=<?= (int)$i['id'] ?>"><?= e($i['subject']) ?></a></td>
          <td><span class="badge bg-secondary"><?= e($i['status']) ?></span></td>
          <td class="small text-muted"><?= e(date('Y-m-d', strtotime($i['created_at']))) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (!$recentInq): ?><tr><td colspan="3" class="text-muted">No inquiries yet.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div></div>
  </div>
  <div class="col-lg-5">
    <div class="card"><div class="card-body">
      <h6>Favorites</h6>
      <?php if (!$favProjects): ?><p class="text-muted small mb-0">No favorites yet.</p><?php endif; ?>
      <ul class="list-unstyled mb-0 small">
      <?php foreach ($favProjects as $p): ?>
        <li class="py-1 border-bottom">
          <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($p['slug']) ?>"><?= e($p['title']) ?></a>
        </li>
      <?php endforeach; ?>
      </ul>
    </div></div>
  </div>
</div>
<?php render_dashboard_footer(); ?>