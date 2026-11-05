<?php
require_once __DIR__ . '/includes/auth.php';
$bio       = get_setting('owner_bio','');
$owner     = get_setting('owner_name','');
$title     = get_setting('owner_title','');
$experience = db()->query("SELECT * FROM experience ORDER BY sort_order,start_date DESC")->fetchAll();
$education  = db()->query("SELECT * FROM education ORDER BY sort_order,start_date DESC")->fetchAll();
$skills     = db()->query("SELECT * FROM skills ORDER BY sort_order,id")->fetchAll();

// Fetch certificates to show on the About page as well
$certificates = db()->query("SELECT * FROM certificates ORDER BY issue_date DESC")->fetchAll();

$pageTitle = 'About — ' . $owner;
include 'includes/header.php'; ?>

<section class="mb-4">
  <h1>About <?= e($owner) ?></h1>
  <p class="lead text-secondary"><?= e($title) ?></p>
  <p><?= nl2br(e($bio)) ?></p>
  
  <!-- ATS Resume Download Button with Tracking -->
  <?php if(get_setting('resume_file')): ?>
      <form action="<?= BASE_URL ?>download-resume.php" method="post" class="mt-3 d-inline">
          <button class="btn btn-primary">
              <i class="bi bi-file-earmark-arrow-down"></i> Download ATS Resume (PDF)
          </button>
      </form>
      <div class="small text-muted mt-2">
          <?= (int)get_setting('resume_downloads') ?> downloads
      </div>
  <?php else: ?>
      <button class="btn btn-outline-secondary mt-3" disabled>Resume Not Available</button>
  <?php endif; ?>
</section>

<div class="row g-4">
  <div class="col-lg-6">
    <h3 class="h5">Experience</h3>
    <?php if (!$experience): ?><p class="text-muted small">No experience listed.</p><?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($experience as $x): ?>
      <li class="mb-3">
        <div class="fw-semibold"><?= e($x['title']) ?></div>
        <div class="small text-secondary"><?= e($x['company']) ?></div>
        <div class="small text-muted">
          <?= e(date('M Y', strtotime($x['start_date']))) ?> —
          <?= $x['end_date'] ? e(date('M Y', strtotime($x['end_date']))) : 'Present' ?>
        </div>
        <?php if ($x['description']): ?><div class="small"><?= e($x['description']) ?></div><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
  <div class="col-lg-6">
    <h3 class="h5">Education</h3>
    <?php if (!$education): ?><p class="text-muted small">No education listed.</p><?php endif; ?>
    <ul class="list-unstyled">
      <?php foreach ($education as $x): ?>
      <li class="mb-3">
        <div class="fw-semibold"><?= e($x['degree']) ?></div>
        <div class="small text-secondary"><?= e($x['institution']) ?></div>
        <div class="small text-muted">
          <?= e(date('M Y', strtotime($x['start_date']))) ?> —
          <?= $x['end_date'] ? e(date('M Y', strtotime($x['end_date']))) : 'Present' ?>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<?php if ($skills): ?>
<section class="mt-4">
  <h3 class="h5">Skills</h3>
  <div class="row g-3">
    <?php foreach ($skills as $s): ?>
    <div class="col-md-6">
      <div class="d-flex justify-content-between small fw-semibold">
        <span><?= e($s['name']) ?></span><span><?= (int)$s['proficiency'] ?>%</span>
      </div>
      <div class="skill-bar mb-2"><div style="width:<?= (int)$s['proficiency'] ?>%"></div></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Certificates Section on About Page -->
<?php if ($certificates): ?>
<section class="mt-5">
  <h3 class="h5 mb-3">Certifications</h3>
  <div class="row g-4">
    <?php foreach ($certificates as $c): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm border-0">
        <img src="<?= UPLOAD_URL.e($c['image_path']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;" alt="<?= e($c['title']) ?>">
        <div class="card-body p-3">
          <h6 class="mb-1"><?= e($c['title']) ?></h6>
          <div class="small text-muted mb-2"><?= e($c['issuer']) ?> • <?= date('M Y', strtotime($c['issue_date'])) ?></div>
          <?php if($c['credential_url']): ?>
            <a href="<?= e($c['credential_url']) ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100">View Credential</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>