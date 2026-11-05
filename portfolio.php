<?php
require_once __DIR__ . '/includes/auth.php';

$q        = trim($_GET['q'] ?? '');
$cat      = (int)($_GET['category'] ?? 0);
$sort     = $_GET['sort'] ?? 'newest';
$page     = max(1, (int)($_GET['page'] ?? 1));

// 1. Fetch standard Projects
$where = ["p.status='published'"];
$params = [];
if ($q !== '') { $where[] = "(p.title LIKE ? OR p.description LIKE ?)"; $params[]="%$q%"; $params[]="%$q%"; }
if ($cat > 0) { $where[] = "p.category_id = ?"; $params[]=$cat; }
$whereSql = 'WHERE '.implode(' AND ', $where);

$stmt = db()->prepare("
    SELECT p.*, c.name AS category_name, 'project' AS item_type 
    FROM projects p LEFT JOIN categories c ON c.id=p.category_id
    $whereSql
");
$stmt->execute($params);
$projects = $stmt->fetchAll();

// 2. Determine if we should fetch Designs
// We fetch designs if "All Categories" is selected (cat == 0)
// OR if the user specifically selected the category named "Graphic Design"
$fetchDesigns = false;
if ($cat == 0) {
    $fetchDesigns = true;
} else {
    // Check if the selected category is "Graphic Design"
    $catNameStmt = db()->prepare("SELECT name FROM categories WHERE id = ?");
    $catNameStmt->execute([$cat]);
    $catName = strtolower($catNameStmt->fetchColumn() ?: '');
    if (str_contains($catName, 'graphic')) {
        $fetchDesigns = true;
    }
}

$designs = [];
if ($fetchDesigns) {
    $designQuery = "SELECT *, 'design' AS item_type FROM designs";
    $designParams = [];
    if ($q !== '') {
        $designQuery .= " WHERE title LIKE ?";
        $designParams[] = "%$q%";
    }
    $stmt = db()->prepare($designQuery);
    $stmt->execute($designParams);
    $designs = $stmt->fetchAll();
}

// 3. Merge them into one list
$allItems = array_merge($projects, $designs);

// 4. Sort the combined list
usort($allItems, function($a, $b) use ($sort) {
    if ($sort === 'title') {
        return strcmp($a['title'], $b['title']);
    } elseif ($sort === 'oldest') {
        $dateA = $a['created_at'] ?? $a['project_date'] ?? '1970-01-01';
        $dateB = $b['created_at'] ?? $b['project_date'] ?? '1970-01-01';
        return strtotime($dateA) - strtotime($dateB);
    } else {
        // newest
        $dateA = $a['created_at'] ?? $a['project_date'] ?? '1970-01-01';
        $dateB = $b['created_at'] ?? $b['project_date'] ?? '1970-01-01';
        return strtotime($dateB) - strtotime($dateA);
    }
});

// 5. Pagination
$total = count($allItems);
$pg = paginate($total, 9, $page);
$paginatedItems = array_slice($allItems, $pg['offset'], $pg['limit']);

$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();

$pageTitle = 'Portfolio';
include 'includes/header.php'; ?>

<h1 class="mb-3">Portfolio & Designs</h1>

<form class="row g-2 mb-4" method="get">
  <div class="col-md-4">
    <input name="q" class="form-control" placeholder="Search portfolio…" value="<?= e($q) ?>">
  </div>
  <div class="col-md-3">
    <select name="category" class="form-select">
      <option value="0">All Categories</option>
      <?php foreach ($categories as $c): ?>
      <option value="<?= (int)$c['id'] ?>" <?= $cat==(int)$c['id']?'selected':'' ?>>
        <?= e($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <select name="sort" class="form-select">
      <option value="newest" <?= $sort==='newest'?'selected':'' ?>>Newest</option>
      <option value="oldest" <?= $sort==='oldest'?'selected':'' ?>>Oldest</option>
      <option value="title"  <?= $sort==='title'?'selected':'' ?>>Title A–Z</option>
    </select>
  </div>
  <div class="col-md-2"><button class="btn btn-primary w-100">Filter</button></div>
</form>

<?php if (!$paginatedItems): ?>
  <div class="alert alert-info">No projects or designs match your search.</div>
<?php else: ?>
<div class="row g-4">
  <?php foreach ($paginatedItems as $item): ?>
    <div class="col-md-6 col-lg-4">
      
      <?php if ($item['item_type'] === 'project'): ?>
        <!-- Standard Project Card -->
        <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($item['slug']) ?>" class="text-decoration-none text-reset">
          <div class="card h-100 shadow-sm">
            <img src="<?= $item['thumbnail'] ? UPLOAD_URL.e($item['thumbnail']) : 'https://via.placeholder.com/600x340?text=Project' ?>"
                 class="card-img-top project-thumb" alt="<?= e($item['title']) ?>">
            <div class="card-body">
              <span class="badge bg-primary-subtle text-primary mb-2"><?= e($item['category_name'] ?? 'Uncategorized') ?></span>
              <h5 class="card-title"><?= e($item['title']) ?></h5>
              <p class="card-text small text-secondary mb-0"><?= e(mb_strimwidth(strip_tags($item['description']),0,100,'…')) ?></p>
            </div>
          </div>
        </a>

      <?php else: ?>
        <!-- Graphic Design Card (Triggers Modal) -->
        <a href="#" data-bs-toggle="modal" data-bs-target="#designModal<?= $item['id'] ?>" class="text-decoration-none text-reset">
          <div class="card h-100 shadow-sm">
            <img src="<?= UPLOAD_URL.e($item['image_path']) ?>" class="card-img-top project-thumb" alt="<?= e($item['title']) ?>">
            <div class="card-body">
              <span class="badge bg-secondary mb-2">Graphic Design</span>
              <h5 class="card-title"><?= e($item['title']) ?></h5>
              <p class="card-text small text-secondary mb-0"><?= e(mb_strimwidth(strip_tags($item['description']),0,100,'…')) ?></p>
            </div>
          </div>
        </a>

        <!-- Design Lightbox Modal -->
        <div class="modal fade" id="designModal<?= $item['id'] ?>" tabindex="-1">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header border-0">
                <h5 class="modal-title"><?= e($item['title']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body text-center">
                <img src="<?= UPLOAD_URL.e($item['image_path']) ?>" class="img-fluid rounded mb-3" alt="<?= e($item['title']) ?>">
                <p><?= nl2br(e($item['description'])) ?></p>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  <?php endforeach; ?>
</div>

<?php if ($pg['pages'] > 1): ?>
<nav class="mt-4"><ul class="pagination justify-content-center">
  <?php for ($i=1;$i<=$pg['pages'];$i++):
    $qs = http_build_query(['q'=>$q,'category'=>$cat,'sort'=>$sort,'page'=>$i]); ?>
  <li class="page-item <?= $i==$pg['current']?'active':'' ?>">
    <a class="page-link" href="?<?= $qs ?>"><?= $i ?></a></li>
  <?php endfor; ?>
</ul></nav>
<?php endif; ?>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>