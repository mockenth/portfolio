<?php
require_once __DIR__ . '/_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $projectId = (int)($_POST['project_id']??0);
    $action = $_POST['action']??'add';
    if ($projectId) {
        if ($action==='add') {
            db()->prepare("INSERT IGNORE INTO favorites (user_id,project_id) VALUES (?,?)")
                ->execute([$user['id'],$projectId]);
            flash('success','Added to favorites.');
        } else {
            db()->prepare("DELETE FROM favorites WHERE user_id=? AND project_id=?")
                ->execute([$user['id'],$projectId]);
            flash('success','Removed from favorites.');
        }
    }
    $redir = $_POST['redirect'] ?? (BASE_URL.'user/favorites.php');
    header('Location: '.$redir); exit;
}

$stmt = db()->prepare("
    SELECT p.*, c.name AS category_name, f.created_at AS fav_at
    FROM favorites f JOIN projects p ON p.id=f.project_id
    LEFT JOIN categories c ON c.id=p.category_id
    WHERE f.user_id=? AND p.status='published'
    ORDER BY f.created_at DESC");
$stmt->execute([$user['id']]);
$favs = $stmt->fetchAll();

render_dashboard_header('Favorite Projects','user'); ?>
<?php if (!$favs): ?><div class="alert alert-info">You haven't saved any projects yet.</div><?php endif; ?>
<div class="row g-4">
<?php foreach ($favs as $p): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
      <img src="<?= $p['thumbnail']?UPLOAD_URL.e($p['thumbnail']):'https://via.placeholder.com/600x340' ?>"
           class="card-img-top project-thumb">
      <div class="card-body">
        <h6><?= e($p['title']) ?></h6>
        <p class="small text-muted mb-2"><?= e($p['category_name']??'Uncategorized') ?></p>
        <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($p['slug']) ?>"
           class="btn btn-sm btn-outline-primary">View</a>
        <form method="post" class="d-inline">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="project_id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="action" value="remove">
          <button class="btn btn-sm btn-outline-danger">Remove</button>
        </form>
      </div>
    </div>
  </div>
<?php endforeach; ?>
</div>
<?php render_dashboard_footer(); ?>