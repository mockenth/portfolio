<?php
require_once __DIR__ . '/../_init.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $title = trim($_POST['title']);
            $category = trim($_POST['category']);
            $description = trim($_POST['description']);
            $image_path = $_POST['existing_image'] ?? '';

            // Handle Image Upload
            if (!empty($_FILES['image']['name'])) {
                $image_path = save_upload($_FILES['image'], 'projects'); 
                
                // If updating, delete the old image to save space
                if ($action === 'update' && !empty($_POST['existing_image'])) {
                    $oldFile = UPLOAD_DIR . $_POST['existing_image'];
                    if (is_file($oldFile)) @unlink($oldFile);
                }
            }

            if ($action === 'create') {
                if (!$image_path) throw new Exception("Image is required for new designs.");
                db()->prepare("INSERT INTO designs (title, category, image_path, description) VALUES (?, ?, ?, ?)")
                    ->execute([$title, $category, $image_path, $description]);
                flash('success', 'Design added successfully.');
            } else {
                db()->prepare("UPDATE designs SET title=?, category=?, image_path=?, description=? WHERE id=?")
                    ->execute([$title, $category, $image_path, $description, (int)$_POST['id']]);
                flash('success', 'Design updated successfully.');
            }
        } 
        elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = db()->prepare("SELECT image_path FROM designs WHERE id=?");
            $stmt->execute([$id]);
            if ($row = $stmt->fetch()) {
                if (is_file(UPLOAD_DIR . $row['image_path'])) @unlink(UPLOAD_DIR . $row['image_path']);
            }
            db()->prepare("DELETE FROM designs WHERE id=?")->execute([$id]);
            flash('success', 'Design deleted.');
        }
    } catch (Exception $e) {
        flash('error', 'Error: ' . $e->getMessage());
    }

    redirect('admin/designs/index.php');
}

$designs = db()->query("SELECT * FROM designs ORDER BY created_at DESC")->fetchAll();

render_dashboard_header('Manage Graphic Designs', 'admin'); 
?>

<div class="row g-4">
    <!-- Add New Design Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">Upload New Design</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Design Title</label>
                        <input name="title" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Category</label>
                        <input name="category" class="form-control form-control-sm" placeholder="e.g. Logo, Poster, UI" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Image (JPG/PNG)</label>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control form-control-sm" rows="3"></textarea>
                    </div>
                    <button class="btn btn-success w-100">Upload Design</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing Designs Grid -->
    <div class="col-lg-8">
        <div class="row g-3">
            <?php foreach ($designs as $d): ?>
            <div class="col-md-6">
                <div class="card shadow-sm h-100 border-0">
                    <img src="<?= UPLOAD_URL . e($d['image_path']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="mb-1 fw-bold"><?= e($d['title']) ?></h6>
                        <span class="badge bg-secondary mb-3 align-self-start"><?= e($d['category']) ?></span>
                        
                        <div class="mt-auto d-flex gap-2">
                            <!-- Button triggers Edit Modal -->
                            <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $d['id'] ?>">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            
                            <!-- Delete Form -->
                            <form method="post" class="d-inline">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <button class="btn btn-sm btn-outline-danger" data-confirm="Delete this design?"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal for this specific design -->
            <div class="modal fade" id="editModal<?= $d['id'] ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Design</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                <input type="hidden" name="existing_image" value="<?= e($d['image_path']) ?>">
                                
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Title</label>
                                    <input name="title" class="form-control form-control-sm" value="<?= e($d['title']) ?>" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Category</label>
                                    <input name="category" class="form-control form-control-sm" value="<?= e($d['category']) ?>" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">New Image (Optional)</label>
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                    <small class="text-muted">Leave blank to keep the current image.</small>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-bold">Description</label>
                                    <textarea name="description" class="form-control form-control-sm" rows="3"><?= e($d['description']) ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary btn-sm">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Edit Modal -->

            <?php endforeach; ?>
            
            <?php if (!$designs): ?>
                <div class="col-12"><div class="alert alert-info">No designs uploaded yet.</div></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php render_dashboard_footer(); ?>