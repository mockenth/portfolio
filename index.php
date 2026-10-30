<?php
require_once __DIR__ . '/includes/auth.php';

$owner      = get_setting('owner_name', 'Portfolio Owner');
$title      = get_setting('owner_title', 'Developer');
$bio        = get_setting('owner_bio', '');
$email      = get_setting('owner_email', '');
$phone      = get_setting('owner_phone', '');

$featured = db()->query("
    SELECT p.*, c.name AS category_name
    FROM projects p LEFT JOIN categories c ON c.id=p.category_id
    WHERE p.status='published' AND p.is_featured=1
    ORDER BY p.created_at DESC LIMIT 6
")->fetchAll();

// Fetch latest 4 graphic designs for the homepage
$homeDesigns = db()->query("SELECT * FROM designs ORDER BY created_at DESC LIMIT 4")->fetchAll();

// Fetch latest 4 certificates for the homepage
$homeCerts = db()->query("SELECT * FROM certificates ORDER BY issue_date DESC LIMIT 4")->fetchAll();

$skills    = db()->query("SELECT * FROM skills ORDER BY sort_order,id")->fetchAll();
$services  = db()->query("SELECT * FROM services WHERE status='published' ORDER BY id LIMIT 6")->fetchAll();
$tests     = db()->query("SELECT * FROM testimonials WHERE status='published' ORDER BY id DESC LIMIT 6")->fetchAll();
$experience = db()->query("SELECT * FROM experience ORDER BY sort_order,start_date DESC LIMIT 4")->fetchAll();

$pageTitle = get_setting('site_title', 'Portfolio');
include 'includes/header.php'; ?>

<section class="hero rounded-4 mb-5">
  <div class="container py-5">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="display-4 fw-bold"><?= e($owner) ?></h1>
        <p class="lead text-secondary"><?= e($title) ?></p>
        <p class="mb-4"><?= nl2br(e($bio)) ?></p>
        <a href="<?= BASE_URL ?>portfolio.php" class="btn btn-primary btn-lg me-2">
          <i class="bi bi-collection"></i> View Projects
        </a>
        <a href="<?= BASE_URL ?>contact.php" class="btn btn-outline-dark btn-lg">
          <i class="bi bi-envelope"></i> Contact Me
        </a>
      </div>
            <div class="col-lg-5 text-center">
        <?php $heroImg = get_setting('profile_image'); ?>
        
        <!-- Glowing Profile Image Container -->
        <div class="position-relative d-inline-block profile-glow-wrapper">
            <div class="glow-ring"></div>
            <img src="<?= $heroImg ? UPLOAD_URL.e($heroImg) : 'https://ui-avatars.com/api/?name='.urlencode($owner).'&size=320&background=f99d00&color=000&rounded=true' ?>"
                 class="img-fluid rounded-circle position-relative" 
                 style="width: 280px; height: 280px; object-fit: cover; border: 2px solid var(--card-bg); z-index: 2;" 
                 alt="<?= e($owner) ?>">
        </div>

        <!-- Custom CSS for the Glow Effect -->
        <style>
            .profile-glow-wrapper {
                margin: 30px; /* Gives room for the thicker glow to expand */
            }

            /* Thinner ring stroke */
            .glow-ring {
                position: absolute;
                top: -4px; right: -4px; bottom: -4px; left: -4px; /* Reduced from -8px to -4px for a thinner stroke */
                border-radius: 50%;
                background: linear-gradient(45deg, #f99d00, #ffc107, #f99d00);
                background-size: 200% 200%;
                animation: glowPulse 3s ease-in-out infinite alternate;
                z-index: 1;
                opacity: 0.9;
            }

            /* Massive, soft orange glow spreading outward */
            .glow-ring::after {
                content: '';
                position: absolute;
                top: 0; right: 0; bottom: 0; left: 0;
                border-radius: 50%;
                /* Increased blur spread from 40px to 80px, and opacity for a thicker feel */
                box-shadow: 0 0 80px 20px rgba(249, 157, 0, 0.45); 
                z-index: -1;
            }

            @keyframes glowPulse {
                0% { 
                    background-position: 0% 50%; 
                    box-shadow: 0 0 40px 10px rgba(249, 157, 0, 0.2); 
                }
                100% { 
                    background-position: 100% 50%; 
                    box-shadow: 0 0 100px 30px rgba(249, 157, 0, 0.6); 
                }
            }
        </style>
      </div>
    </div>
  </div>
</section>

<!-- Featured Projects -->
<section class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h3 mb-0">Featured Projects</h2>
    <a href="<?= BASE_URL ?>portfolio.php" class="btn btn-sm btn-outline-primary">All Projects →</a>
  </div>
  <?php if (!$featured): ?>
    <div class="alert alert-light border text-center">No featured projects yet.</div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($featured as $p): ?>
    <div class="col-md-6 col-lg-4">
      <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($p['slug']) ?>"
         class="text-decoration-none text-reset">
        <div class="card h-100 shadow-sm">
          <img src="<?= $p['thumbnail'] ? UPLOAD_URL.e($p['thumbnail']) : 'https://via.placeholder.com/600x340?text=Project' ?>"
               class="card-img-top project-thumb" alt="<?= e($p['title']) ?>">
          <div class="card-body">
            <span class="badge bg-primary-subtle text-primary mb-2">
              <?= e($p['category_name'] ?? 'Uncategorized') ?>
            </span>
            <h5 class="card-title"><?= e($p['title']) ?></h5>
            <p class="card-text small text-secondary">
              <?= e(mb_strimwidth(strip_tags($p['description']), 0, 120, '…')) ?>
            </p>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- Graphic Designs Section -->
<?php if ($homeDesigns): ?>
<section class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h3 mb-0">Graphic Design Showcase</h2>
    <a href="<?= BASE_URL ?>designs.php" class="btn btn-sm btn-outline-primary">View All Designs →</a>
  </div>
  <div class="row g-4">
    <?php foreach ($homeDesigns as $d): ?>
    <div class="col-md-6 col-lg-3">
      <a href="<?= BASE_URL ?>designs.php" class="text-decoration-none text-reset">
        <div class="card h-100 shadow-sm border-0">
          <img src="<?= UPLOAD_URL.e($d['image_path']) ?>" class="card-img-top" 
               style="height: 200px; object-fit: cover;" alt="<?= e($d['title']) ?>">
          <div class="card-body p-3">
            <span class="badge bg-secondary mb-2 small"><?= e($d['category']) ?></span>
            <h6 class="card-title mb-0"><?= e($d['title']) ?></h6>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Certificates Section (Placed before Services) -->
<?php if ($homeCerts): ?>
<section class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h3 mb-0">Certifications</h2>
    <a href="<?= BASE_URL ?>about.php" class="btn btn-sm btn-outline-primary">View All →</a>
  </div>
  <div class="row g-4">
    <?php foreach ($homeCerts as $c): ?>
    <div class="col-md-6 col-lg-3">
      <?php if($c['credential_url']): ?>
        <a href="<?= e($c['credential_url']) ?>" target="_blank" class="text-decoration-none text-reset">
      <?php else: ?>
        <div class="text-decoration-none text-reset">
      <?php endif; ?>
          <div class="card h-100 shadow-sm border-0">
            <img src="<?= UPLOAD_URL.e($c['image_path']) ?>" class="card-img-top" 
                 style="height: 150px; object-fit: cover;" alt="<?= e($c['title']) ?>">
            <div class="card-body p-3 text-center">
              <h6 class="card-title mb-1"><?= e($c['title']) ?></h6>
              <small class="text-muted"><?= e($c['issuer']) ?></small>
            </div>
          </div>
      <?php if($c['credential_url']): ?>
        </a>
      <?php else: ?>
        </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Skills -->
<?php if ($skills): ?>
<section class="mb-5">
  <h2 class="h3 mb-3">Skills & Expertise</h2>
  <div class="row g-4">
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

<!-- Services -->
<?php if ($services): ?>
<section class="mb-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h3 mb-0">Services</h2>
    <a href="<?= BASE_URL ?>services.php" class="btn btn-sm btn-outline-primary">All Services →</a>
  </div>
  <div class="row g-4">
    <?php foreach ($services as $s): ?>
    <div class="col-md-6 col-lg-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <i class="<?= e($s['icon']) ?> fs-2 text-primary"></i>
          <h5 class="card-title mt-3"><?= e($s['title']) ?></h5>
          <p class="card-text small text-secondary"><?= e(mb_strimwidth(strip_tags($s['description']),0,120,'…')) ?></p>
          <?php if ($s['starting_price']): ?>
          <p class="fw-bold mb-0">From $<?= number_format((float)$s['starting_price'],2) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Experience -->
<?php if ($experience): ?>
<section class="mb-5">
  <h2 class="h3 mb-3">Experience</h2>
  <div class="row g-3">
    <?php foreach ($experience as $x): ?>
    <div class="col-md-6">
      <div class="p-3 bg-body-tertiary rounded">
        <h6 class="mb-1"><?= e($x['title']) ?></h6>
        <div class="small text-secondary mb-1"><?= e($x['company']) ?></div>
        <div class="small text-muted"><?= e(date('M Y', strtotime($x['start_date']))) ?> —
          <?= $x['end_date'] ? e(date('M Y', strtotime($x['end_date']))) : 'Present' ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<?php if ($tests): ?>
<section class="mb-5">
  <h2 class="h3 mb-3">Testimonials</h2>
  <div class="row g-4">
    <?php foreach ($tests as $t): ?>
    <div class="col-md-4">
      <div class="card h-100 shadow-sm">
        <div class="card-body">
          <div class="text-warning mb-2">
            <?php for ($i=0;$i<(int)$t['rating'];$i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
          </div>
          <p class="card-text">"<?= e($t['content']) ?>"</p>
          <div class="small fw-semibold">— <?= e($t['client_name']) ?></div>
          <?php if ($t['client_title']): ?><div class="small text-muted"><?= e($t['client_title']) ?></div><?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Contact strip -->
<section class="bg-dark text-light rounded-4 p-4">
  <div class="row align-items-center">
    <div class="col-md-8">
      <h4>Let's build something great.</h4>
      <p class="mb-0">
        <?= $email ? '📧 '.e($email) : '' ?>
        <?= $phone ? ' · ☎ '.e($phone) : '' ?>
      </p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
      <a href="<?= BASE_URL ?>contact.php" class="btn btn-light">Get In Touch</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>