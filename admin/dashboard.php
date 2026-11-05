<?php
require_once __DIR__ . '/_init.php';

$stats = [
    'users'          => (int)db()->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
    'projects'       => (int)db()->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'published'      => (int)db()->query("SELECT COUNT(*) FROM projects WHERE status='published'")->fetchColumn(),
    'drafts'         => (int)db()->query("SELECT COUNT(*) FROM projects WHERE status='draft'")->fetchColumn(),
    'categories'     => (int)db()->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'services'       => (int)db()->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'messages'       => (int)db()->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'unread_msgs'    => (int)db()->query("SELECT COUNT(*) FROM contact_messages WHERE status='unread'")->fetchColumn(),
    'inquiries'      => (int)db()->query("SELECT COUNT(*) FROM project_inquiries")->fetchColumn(),
    'pending_inq'    => (int)db()->query("SELECT COUNT(*) FROM project_inquiries WHERE status='pending'")->fetchColumn(),
];

$recentUsers = db()->query("SELECT id,name,email,created_at FROM users WHERE role='user' ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recentInq   = db()->query("
    SELECT i.*, u.name AS user_name FROM project_inquiries i
    JOIN users u ON u.id=i.user_id ORDER BY i.created_at DESC LIMIT 5")->fetchAll();

render_dashboard_header('Admin Dashboard', 'admin'); ?>

<div class="row g-3 mb-4">
  <?php
  $cards = [
    ['Users',$stats['users'],'bi-people','users/index.php','primary'],
    ['Published Projects',$stats['published'],'bi-collection','projects/index.php','success'],
    ['Draft Projects',$stats['drafts'],'bi-file-earmark','projects/index.php?status=draft','warning'],
    ['Categories',$stats['categories'],'bi-tags','categories/index.php','info'],
    ['Services',$stats['services'],'bi-briefcase','services/index.php','secondary'],
    ['Messages ('.$stats['unread_msgs'].' new)',$stats['messages'],'bi-envelope','messages/index.php','danger'],
    ['Inquiries ('.$stats['pending_inq'].' pending)',$stats['inquiries'],'bi-chat-dots','inquiries/index.php','dark'],
  ];
  foreach ($cards as [$label,$val,$icon,$link,$color]): ?>
  <div class="col-md-6 col-xl-3">
    <a class="text-decoration-none" href="<?= BASE_URL ?>admin/<?= $link ?>">
      <div class="card border-<?= $color ?> shadow-sm h-100">
        <div class="card-body d-flex align-items-center">
          <i class="bi <?= $icon ?> fs-2 text-<?= $color ?> me-3"></i>
          <div>
            <div class="fs-4 fw-bold"><?= (int)$val ?></div>
            <div class="small text-secondary"><?= e($label) ?></div>
          </div>
        </div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</div>

<div class="row g-3">
  <div class="col-lg-6">
    <div class="card shadow-sm"><div class="card-body">
      <h6>Recent Registrations</h6>
      <ul class="list-unstyled mb-0 small">
        <?php foreach ($recentUsers as $u): ?>
        <li class="d-flex justify-content-between py-1 border-bottom">
          <span><?= e($u['name']) ?> <span class="text-muted">(<?= e($u['email']) ?>)</span></span>
          <span class="text-muted"><?= e(date('Y-m-d', strtotime($u['created_at']))) ?></span>
        </li>
        <?php endforeach; ?>
        <?php if (!$recentUsers): ?><li class="text-muted">No registrations yet.</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm"><div class="card-body">
      <h6>Recent Inquiries</h6>
      <ul class="list-unstyled mb-0 small">
        <?php foreach ($recentInq as $i): ?>
        <li class="py-1 border-bottom">
          <div class="d-flex justify-content-between">
            <span><strong><?= e($i['subject']) ?></strong> — <?= e($i['user_name']) ?></span>
            <span class="badge bg-secondary"><?= e($i['status']) ?></span>
          </div>
        </li>
        <?php endforeach; ?>
        <?php if (!$recentInq): ?><li class="text-muted">No inquiries yet.</li><?php endif; ?>
      </ul>
    </div></div>
  </div>
</div>

<?php render_dashboard_footer(); ?>