<?php
require_once __DIR__ . '/includes/auth.php';

$categoryFilter = $_GET['category'] ?? '';
$where = "WHERE 1=1";
$params = [];

if ($categoryFilter !== '') {
    $where .= " AND category = ?";
    $params[] = $categoryFilter;
}

$stmt = db()->prepare("SELECT * FROM designs $where ORDER BY created_at DESC");
$stmt->execute($params);
$designs = $stmt->fetchAll();

// Get unique categories for the filter buttons
$categories = db()->query("SELECT DISTINCT category FROM designs ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Graphic Design Portfolio';
include 'includes/header.php'; 
?>

<style>
.design-card { transition: transform 0.3s ease, box-shadow 0.3s ease; overflow: hidden; }
.design-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); }
.design-img { width: 100%; height: 100%; object-fit: cover; aspect-ratio: 4/3; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Graphic Design Gallery</h1>
    <a href="index.php" class="btn btn-outline-secondary">← Back to Home</a>
</div>

<!-- Filter Buttons -->
<div class="mb-4">
    <a href="designs.php" class="btn btn-sm <?= $categoryFilter===''?'btn-primary':'btn-outline-primary' ?> me-2 mb-2">All Designs</a>
    <?php foreach($categories as $c): ?>
        <a href="designs.php?category=<?= urlencode($c) ?>" class="btn btn-sm <?= $categoryFilter===$c?'btn-primary':'btn-outline-primary' ?> me-2 mb-2">
            <?= e($c) ?>
        </a>
    <?php endforeach; ?>
</div>

<?php if (!$designs): ?>
    <div class="alert alert-info">No designs uploaded in this category yet.</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($designs as $d): ?>
    <div class="col-md-6 col-lg-4">
        <div class="card design-card h-100 shadow-sm border-0">
            <!-- Triggers Bootstrap Modal -->
            <a href="#" data-bs-toggle="modal" data-bs-target="#designModal<?= $d['id'] ?>">
                <img src="<?= UPLOAD_URL . e($d['image_path']) ?>" class="design-img" alt="<?= e($d['title']) ?>">
            </a>
            <div class="card-body">
                <span class="badge bg-secondary mb-2"><?= e($d['category']) ?></span>
                <h5 class="card-title"><?= e($d['title']) ?></h5>
                <p class="card-text small text-muted"><?= e(mb_strimwidth(strip_tags($d['description']), 0, 80, '...')) ?></p>
            </div>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div class="modal fade" id="designModal<?= $d['id'] ?>" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= e($d['title']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?= UPLOAD_URL . e($d['image_path']) ?>" class="img-fluid rounded mb-3" alt="<?= e($d['title']) ?>">
                    <p><?= nl2br(e($d['description'])) ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>