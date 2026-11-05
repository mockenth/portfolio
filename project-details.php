<?php
require_once __DIR__ . '/includes/auth.php';

$slug = $_GET['slug'] ?? '';
$stmt = db()->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug
    FROM projects p LEFT JOIN categories c ON c.id=p.category_id
    WHERE p.slug=? AND p.status='published' LIMIT 1
");
$stmt->execute([$slug]);
$p = $stmt->fetch();
if (!$p) { http_response_code(404); flash('error','Project not found.'); redirect('portfolio.php'); }

db()->prepare("UPDATE projects SET views=views+1 WHERE id=?")->execute([$p['id']]);

$imgs = db()->prepare("SELECT * FROM project_images WHERE project_id=? ORDER BY sort_order,id");
$imgs->execute([$p['id']]);
$images = $imgs->fetchAll();

$related = db()->prepare("
    SELECT p.* FROM projects p
    WHERE p.status='published' AND p.id<>? AND p.category_id=?
    ORDER BY p.created_at DESC LIMIT 3
");
$related->execute([$p['id'], $p['category_id']]);
$relatedProjects = $related->fetchAll();

$isFav = false;
if ($u = current_user()) {
    $favStmt = db()->prepare("SELECT id FROM favorites WHERE user_id=? AND project_id=?");
    $favStmt->execute([$u['id'], $p['id']]);
    $isFav = (bool)$favStmt->fetch();
}

$pageTitle = $p['title'];
include 'includes/header.php'; ?>

<nav aria-label="breadcrumb">
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>">Home</a></li>
  <li class="breadcrumb-item"><a href="<?= BASE_URL ?>portfolio.php">Portfolio</a></li>
  <li class="breadcrumb-item active"><?= e($p['title']) ?></li>
</ol></nav>

<article class="row g-4">
  <div class="col-lg-8">
    <?php if ($p['thumbnail']): ?>
    <img src="<?= UPLOAD_URL.e($p['thumbnail']) ?>" class="img-fluid rounded shadow-sm mb-3 w-100" alt="">
    <?php endif; ?>
    <h1><?= e($p['title']) ?></h1>
    <p class="text-secondary">
      <?= e($p['category_name'] ?? 'Uncategorized') ?>
      <?php if ($p['project_date']): ?>
        · <?= e(date('M Y', strtotime($p['project_date']))) ?>
      <?php endif; ?>
    </p>
    <div class="mb-3"><?= nl2br(e($p['description'])) ?></div>

    <?php if ($images): ?>
    <h5>Gallery</h5>
    <div class="row g-2 mb-3">
      <?php foreach ($images as $img): ?>
      <div class="col-4">
        <a href="<?= UPLOAD_URL.e($img['image_path']) ?>" target="_blank">
          <img src="<?= UPLOAD_URL.e($img['image_path']) ?>"
               class="img-fluid rounded" alt="<?= e($img['alt_text']) ?>">
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <aside class="col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h6>Tech Stack</h6>
        <p class="small"><?= e($p['technologies'] ?: '—') ?></p>
        <?php if ($p['project_url']): ?>
        <a class="btn btn-primary w-100 mb-2" href="<?= e($p['project_url']) ?>" target="_blank">
          <i class="bi bi-box-arrow-up-right"></i> Live Demo</a>
        <?php endif; ?>
        <?php if ($p['source_code_url']): ?>
        <a class="btn btn-outline-dark w-100 mb-2" href="<?= e($p['source_code_url']) ?>" target="_blank">
          <i class="bi bi-github"></i> Source Code</a>
        <?php endif; ?>

        <?php if ($u && $u['role']==='user'): ?>
        <form method="post" action="<?= BASE_URL ?>user/favorites.php" class="mt-2">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="project_id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="action" value="<?= $isFav?'remove':'add' ?>">
          <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI']) ?>">
          <button class="btn btn-warning w-100">
            <i class="bi bi-star<?= $isFav?'-fill':'' ?>"></i>
            <?= $isFav ? 'Remove from Favorites' : 'Add to Favorites' ?>
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </aside>
</article>

<?php if ($relatedProjects): ?>
<section class="mt-5">
  <h3 class="h5">Related Projects</h3>
  <div class="row g-4">
    <?php foreach ($relatedProjects as $r): ?>
    <div class="col-md-4">
      <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($r['slug']) ?>"
         class="text-decoration-none text-reset">
        <div class="card h-100 shadow-sm">
          <img src="<?= $r['thumbnail'] ? UPLOAD_URL.e($r['thumbnail']) : 'https://via.placeholder.com/600x340?text=Project' ?>"
               class="card-img-top project-thumb">
          <div class="card-body"><h6 class="mb-0"><?= e($r['title']) ?></h6></div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>