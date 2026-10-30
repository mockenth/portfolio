<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $action = $_POST['action']??'';
    if ($action==='create') {
        $name = trim($_POST['name']??'');
        $slug = slugify($_POST['slug']??$name);
        if ($name) {
            db()->prepare("INSERT INTO categories (name,slug,description) VALUES (?,?,?)")
                ->execute([$name,$slug,trim($_POST['description']??'')]);
            flash('success','Category created.');
        }
    } elseif ($action==='delete') {
        db()->prepare("DELETE FROM categories WHERE id=?")->execute([(int)$_POST['id']]);
        flash('success','Category deleted.');
    }
    redirect('admin/categories/index.php');
}

$cats = db()->query("
    SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id=c.id) AS project_count
    FROM categories c ORDER BY name")->fetchAll();

render_dashboard_header('Categories', 'admin'); ?>
<div class="row g-3">
<div class="col-md-4">
  <div class="card"><div class="card-body">
    <h6>New Category</h6>
    <form method="post">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="action" value="create">
      <div class="mb-2"><input name="name" class="form-control" placeholder="Name" required></div>
      <div class="mb-2"><input name="slug" class="form-control" placeholder="slug (optional)"></div>
      <div class="mb-2"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
      <button class="btn btn-primary w-100">Create</button>
    </form>
  </div></div>
</div>
<div class="col-md-8">
  <div class="card"><div class="card-body">
    <table class="table align-middle">
      <thead><tr><th>Name</th><th>Slug</th><th>Projects</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($cats as $c): ?>
      <tr>
        <td><?= e($c['name']) ?></td>
        <td><code><?= e($c['slug']) ?></code></td>
        <td><?= (int)$c['project_count'] ?></td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse"
                  data-bs-target="#edit-<?= (int)$c['id'] ?>">Edit</button>
          <form method="post" class="d-inline">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button class="btn btn-sm btn-outline-danger" data-confirm="Delete category?">Delete</button>
          </form>
        </td>
      </tr>
      <tr class="collapse" id="edit-<?= (int)$c['id'] ?>"><td colspan="4">
        <form method="post" action="<?= BASE_URL ?>admin/categories/update.php" class="row g-2">
          <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <div class="col"><input name="name" class="form-control" value="<?= e($c['name']) ?>"></div>
          <div class="col"><input name="slug" class="form-control" value="<?= e($c['slug']) ?>"></div>
          <div class="col"><input name="description" class="form-control" value="<?= e($c['description']) ?>"></div>
          <div class="col-auto"><button class="btn btn-success">Save</button></div>
        </form>
      </td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div></div>
</div>
</div>
<?php render_dashboard_footer(); ?>