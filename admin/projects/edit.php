<?php
require_once __DIR__ . '/../_init.php';

$id = (int)($_GET['id'] ?? 0);
$project = [
    'title'=>'','slug'=>'','description'=>'','thumbnail'=>'','category_id'=>'',
    'technologies'=>'','project_url'=>'','source_code_url'=>'','project_date'=>'',
    'is_featured'=>0,'status'=>'draft'
];
$errors = [];

if ($id) {
    $stmt = db()->prepare("SELECT * FROM projects WHERE id=?");
    $stmt->execute([$id]);
    $loaded = $stmt->fetch();
    if (!$loaded) { flash('error','Project not found.'); redirect('admin/projects/index.php'); }
    $project = $loaded;
}

$categories = db()->query("SELECT * FROM categories ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf'] ?? null);

    $project['title']           = trim($_POST['title'] ?? '');
    $project['slug']            = trim($_POST['slug'] ?? '') ?: slugify($project['title']);
    $project['description']     = trim($_POST['description'] ?? '');
    $project['category_id']     = $_POST['category_id'] ?: null;
    $project['technologies']    = trim($_POST['technologies'] ?? '');
    $project['project_url']     = trim($_POST['project_url'] ?? '');
    $project['source_code_url'] = trim($_POST['source_code_url'] ?? '');
    $project['project_date']    = $_POST['project_date'] ?: null;
    $project['is_featured']     = isset($_POST['is_featured']) ? 1 : 0;
    $project['status']          = in_array($_POST['status']??'',['draft','published'],true)
                                  ? $_POST['status'] : 'draft';

    if (strlen($project['title'])<3) $errors[]='Title too short.';
    if (strlen($project['description'])<20) $errors[]='Description too short.';

    // Thumbnail
    if (!empty($_FILES['thumbnail']['name'])) {
        try { $project['thumbnail'] = save_upload($_FILES['thumbnail'], 'projects'); }
        catch (Throwable $ex) { $errors[]=$ex->getMessage(); }
    }

    // Gallery uploads
    $gallerySaved = [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $i=>$tmp) {
            if ($_FILES['gallery']['error'][$i]!==UPLOAD_ERR_OK) continue;
            $f = ['tmp_name'=>$tmp,'error'=>UPLOAD_ERR_OK,
                  'size'=>$_FILES['gallery']['size'][$i],'name'=>$_FILES['gallery']['name'][$i]];
            try { $gallerySaved[] = save_upload($f,'projects'); }
            catch (Throwable $ex) { $errors[]='Gallery: '.$ex->getMessage(); }
        }
    }

    if (!$errors) {
        if ($id) {
            $sql = "UPDATE projects SET title=?,slug=?,description=?,thumbnail=?,category_id=?,
                    technologies=?,project_url=?,source_code_url=?,project_date=?,
                    is_featured=?,status=? WHERE id=?";
            $params = [$project['title'],$project['slug'],$project['description'],
                       $project['thumbnail'],$project['category_id'],$project['technologies'],
                       $project['project_url'],$project['source_code_url'],$project['project_date'],
                       $project['is_featured'],$project['status'],$id];
            db()->prepare($sql)->execute($params);
            $projectId = $id;
        } else {
            $sql = "INSERT INTO projects (title,slug,description,thumbnail,category_id,
                    technologies,project_url,source_code_url,project_date,is_featured,status)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            db()->prepare($sql)->execute([$project['title'],$project['slug'],$project['description'],
                $project['thumbnail'],$project['category_id'],$project['technologies'],
                $project['project_url'],$project['source_code_url'],$project['project_date'],
                $project['is_featured'],$project['status']]);
            $projectId = (int)db()->lastInsertId();
        }
        if ($gallerySaved) {
            $ins = db()->prepare("INSERT INTO project_images (project_id,image_path,alt_text,sort_order) VALUES (?,?,?,?)");
            foreach ($gallerySaved as $i=>$path) $ins->execute([$projectId,$path,'', $i]);
        }
        flash('success','Project saved.');
        redirect('admin/projects/edit.php?id='.$projectId);
    }
}

// gallery images
$gallery = [];
if ($id) {
    $g = db()->prepare("SELECT * FROM project_images WHERE project_id=? ORDER BY sort_order,id");
    $g->execute([$id]);
    $gallery = $g->fetchAll();
}

render_dashboard_header($id?'Edit Project':'New Project', 'admin'); ?>
<?php foreach ($errors as $e): ?><div class="alert alert-danger"><?= e($e) ?></div><?php endforeach; ?>
<form method="post" enctype="multipart/form-data" class="row g-3">
<input type="hidden" name="csrf" value="<?= csrf_token() ?>">
<div class="col-md-8">
  <label class="form-label">Title</label>
  <input name="title" class="form-control" required value="<?= e($project['title']) ?>">
</div>
<div class="col-md-4">
  <label class="form-label">Slug</label>
  <input name="slug" class="form-control" value="<?= e($project['slug']) ?>" placeholder="auto">
</div>
<div class="col-12">
  <label class="form-label">Description</label>
  <textarea name="description" class="form-control" rows="6" required><?= e($project['description']) ?></textarea>
</div>
<div class="col-md-4">
  <label class="form-label">Category</label>
  <select name="category_id" class="form-select">
    <option value="">— none —</option>
    <?php foreach ($categories as $c): ?>
    <option value="<?= (int)$c['id'] ?>" <?= $project['category_id']==$c['id']?'selected':'' ?>>
      <?= e($c['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>
<div class="col-md-4">
  <label class="form-label">Project Date</label>
  <input type="date" name="project_date" class="form-control" value="<?= e($project['project_date']) ?>">
</div>
<div class="col-md-4">
  <label class="form-label">Status</label>
  <select name="status" class="form-select">
    <option value="draft" <?= $project['status']==='draft'?'selected':'' ?>>Draft</option>
    <option value="published" <?= $project['status']==='published'?'selected':'' ?>>Published</option>
  </select>
</div>
<div class="col-md-6">
  <label class="form-label">Technologies</label>
  <input name="technologies" class="form-control" value="<?= e($project['technologies']) ?>">
</div>
<div class="col-md-6">
  <label class="form-label">Featured</label>
  <div class="form-check form-switch mt-2">
    <input class="form-check-input" type="checkbox" name="is_featured" id="feat"
           <?= $project['is_featured']?'checked':'' ?>>
    <label class="form-check-label" for="feat">Mark as featured</label>
  </div>
</div>
<div class="col-md-6">
  <label class="form-label">Live URL</label>
  <input name="project_url" class="form-control" value="<?= e($project['project_url']) ?>">
</div>
<div class="col-md-6">
  <label class="form-label">Source Code URL</label>
  <input name="source_code_url" class="form-control" value="<?= e($project['source_code_url']) ?>">
</div>
<div class="col-md-6">
  <label class="form-label">Thumbnail</label>
  <input type="file" name="thumbnail" class="form-control" accept="image/*">
  <?php if ($project['thumbnail']): ?>
  <img src="<?= UPLOAD_URL.e($project['thumbnail']) ?>" class="mt-2 rounded" style="max-height:120px">
  <?php endif; ?>
</div>
<div class="col-md-6">
  <label class="form-label">Gallery Images</label>
  <input type="file" name="gallery[]" class="form-control" accept="image/*" multiple>
</div>
<div class="col-12">
  <button class="btn btn-primary">Save Project</button>
  <a href="<?= BASE_URL ?>admin/projects/index.php" class="btn btn-secondary">Cancel</a>
</div>
</form>

<?php if ($gallery): ?>
<hr><h6>Current Gallery</h6>
<div class="row g-2">
  <?php foreach ($gallery as $img): ?>
  <div class="col-md-2">
    <img src="<?= UPLOAD_URL.e($img['image_path']) ?>" class="img-fluid rounded">
    <form method="post" action="<?= BASE_URL ?>admin/projects/delete-image.php" class="mt-1">
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
      <input type="hidden" name="project_id" value="<?= $id ?>">
      <button class="btn btn-sm btn-outline-danger w-100" data-confirm="Delete image?">Delete</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php render_dashboard_footer(); ?>