<?php
require_once __DIR__ . '/includes/auth.php';
$services = db()->query("SELECT * FROM services WHERE status='published' ORDER BY id")->fetchAll();
$pageTitle = 'Services';
include 'includes/header.php'; ?>
<h1 class="mb-4">Services</h1>
<?php if (!$services): ?>
  <div class="alert alert-info">No services published yet.</div>
<?php else: ?>
<div class="row g-4">
  <?php foreach ($services as $s): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
      <div class="card-body">
        <i class="<?= e($s['icon']) ?> fs-2 text-primary"></i>
        <h5 class="mt-3"><?= e($s['title']) ?></h5>
        <p class="small text-secondary"><?= nl2br(e($s['description'])) ?></p>
        <?php if ($s['starting_price']): ?>
        <p class="fw-bold">From $<?= number_format((float)$s['starting_price'],2) ?></p>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>contact.php?service=<?= urlencode($s['title']) ?>"
           class="btn btn-sm btn-outline-primary">Request This Service</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>