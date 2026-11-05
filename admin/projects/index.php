<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    if (($_POST['action']??'')==='delete') {
        $id = (int)$_POST['id'];
        $row = db()->prepare("SELECT thumbnail FROM projects WHERE id=?"); $row->execute([$id]);
        if ($t = $row->fetch()) {
            if ($t['thumbnail'] && is_file(UPLOAD_DIR.$t['thumbnail'])) @unlink(UPLOAD_DIR.$t['thumbnail']);
        }
        db()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        flash('success','Project deleted.');
    }
    redirect('admin/projects/index.php');
}

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$where = ["1=1"]; $params=[];
if ($q!=='') { $where[]="p.title LIKE ?"; $params[]="%$q%"; }
if (in_array($status,['draft','published'],true)) { $where[]="p.status=?"; $params[]=$status; }
$whereSql = 'WHERE '.implode(' AND ',$where);

$stmt = db()->prepare("
    SELECT p.*, c.name AS category_name FROM projects p
    LEFT JOIN categories c ON c.id=p.category_id
    $whereSql ORDER BY p.created_at DESC");
$stmt->execute($params);
$projects = $stmt->fetchAll();

render_dashboard_header('Projects', 'admin'); ?>
<div class="d-flex justify-content-between mb-3">
  <form class="d-flex gap-2" method="get">
    <input name="q" class="form-control" placeholder="Search…" value="<?= e($q) ?>">
    <select name="status" class="form-select">
      <option value="">All</option>
      <option value="published" <?= $status==='published'?'selected':'' ?>>Published</option>
      <option value="draft" <?= $status==='draft'?'selected':'' ?>>Draft</option>
    </select>
    <button class="btn btn-outline-secondary">Filter</button>
  </form>
  <a href="<?= BASE_URL ?>admin/projects/edit.php" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> New Project</a>
</div>

<div class="table-responsive">
<table class="table align-middle">
<thead><tr><th>Thumb</th><th>Title</th><th>Category</th><th>Status</th><th>Featured</th><th></th></tr></thead>
<tbody>
<?php foreach ($projects as $p): ?>
<tr>
  <td style="width:80px">
    <img src="<?= $p['thumbnail']?UPLOAD_URL.e($p['thumbnail']):'https://via.placeholder.com/80x50' ?>"
         style="width:80px;height:50px;object-fit:cover" class="rounded"></td>
  <td><?= e($p['title']) ?><div class="small text-muted"><?= e($p['slug']) ?></div></td>
  <td><?= e($p['category_name'] ?? '—') ?></td>
  <td><span class="badge bg-<?= $p['status']==='published'?'success':'secondary' ?>">
      <?= e($p['status']) ?></span></td>
  <td><?= $p['is_featured']?'⭐':'' ?></td>
  <td class="text-end">
    <a href="<?= BASE_URL ?>project-details.php?slug=<?= urlencode($p['slug']) ?>"
       target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
    <a href="<?= BASE_URL ?>admin/projects/edit.php?id=<?= (int)$p['id'] ?>"
       class="btn btn-sm btn-outline-primary">Edit</a>
    <form method="post" class="d-inline">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <button class="btn btn-sm btn-outline-danger" data-confirm="Delete this project?">Delete</button>
    </form>
  </td>
</tr>
<?php endforeach; ?>
<?php if (!$projects): ?><tr><td colspan="6" class="text-center text-muted py-4">
  No projects yet.</td></tr><?php endif; ?>
</tbody></table></div>
<?php render_dashboard_footer(); ?>