<?php
require_once __DIR__ . '/../_init.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create') {
            $title = trim($_POST['title'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $issue_date = $_POST['issue_date'] ?? '';
            $credential_url = trim($_POST['credential_url'] ?? '');
            
            if (empty($_FILES['image']['name'])) {
                throw new Exception("Certificate image is required.");
            }
            $image_path = save_upload($_FILES['image'], 'projects');

            db()->prepare("INSERT INTO certificates (title, issuer, issue_date, image_path, credential_url) VALUES (?, ?, ?, ?, ?)")
                ->execute([$title, $issuer, $issue_date, $image_path, $credential_url ?: null]);
            flash('success', 'Certificate added successfully.');
        } 
        elseif ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $stmt = db()->prepare("SELECT image_path FROM certificates WHERE id=?");
            $stmt->execute([$id]);
            if ($row = $stmt->fetch()) {
                if (is_file(UPLOAD_DIR . $row['image_path'])) {
                    @unlink(UPLOAD_DIR . $row['image_path']);
                }
            }
            db()->prepare("DELETE FROM certificates WHERE id=?")->execute([$id]);
            flash('success', 'Certificate deleted.');
        }
    } catch (Exception $e) {
        flash('error', 'Error: ' . $e->getMessage());
    }

    redirect('admin/certificates/index.php');
}

$certificates = db()->query("SELECT * FROM certificates ORDER BY issue_date DESC")->fetchAll();

render_dashboard_header('Manage Certificates', 'admin'); 
?>

<div class="row g-4">
    <!-- Add New Certificate Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">Add New Certificate</div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-2">
                        <label class="form-label small">Certificate Title</label>
                        <input name="title" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Issuer (e.g. Google, Coursera)</label>
                        <input name="issuer" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control form-control-sm" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small">Certificate Image (JPG/PNG)</label>
                        <input type="file" name="image" class="form-control form-control-sm" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Credential URL (Optional)</label>
                        <input name="credential_url" class="form-control form-control-sm" placeholder="https://...">
                    </div>
                    <button class="btn btn-success w-100">Upload Certificate</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Existing Certificates Grid -->
    <div class="col-lg-8">
        <div class="row g-3">
            <?php foreach ($certificates as $c): ?>
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <img src="<?= UPLOAD_URL . e($c['image_path']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="mb-1"><?= e($c['title']) ?></h6>
                        <div class="small text-muted mb-2"><?= e($c['issuer']) ?> • <?= date('M Y', strtotime($c['issue_date'])) ?></div>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger w-100" data-confirm="Delete this certificate?">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (!$certificates): ?><div class="col-12"><div class="alert alert-info">No certificates uploaded yet.</div></div><?php endif; ?>
        </div>
    </div>
</div>

<?php render_dashboard_footer(); ?>