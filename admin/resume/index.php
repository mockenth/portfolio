<?php
require_once __DIR__ . '/../_init.php';

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    $action = $_POST['action'] ?? '';
    $type   = $_POST['type'] ?? ''; // 'skill', 'experience', 'education'

    try {
        if ($action === 'create') {
            if ($type === 'skill') {
                db()->prepare("INSERT INTO skills (name, proficiency, category) VALUES (?, ?, ?)")
                    ->execute([trim($_POST['name']), (int)$_POST['proficiency'], trim($_POST['category'])]);
            } elseif ($type === 'experience') {
                db()->prepare("INSERT INTO experience (title, company, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)")
                    ->execute([trim($_POST['title']), trim($_POST['company']), $_POST['start_date'], $_POST['end_date'] ?: null, trim($_POST['description'])]);
            } elseif ($type === 'education') {
                db()->prepare("INSERT INTO education (degree, institution, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)")
                    ->execute([trim($_POST['degree']), trim($_POST['institution']), $_POST['start_date'], $_POST['end_date'] ?: null, trim($_POST['description'])]);
            }
            flash('success', ucfirst($type) . ' added successfully.');
        } 
        elseif ($action === 'delete') {
            $id = (int)$_POST['id'];
            if ($type === 'skill') db()->prepare("DELETE FROM skills WHERE id=?")->execute([$id]);
            if ($type === 'experience') db()->prepare("DELETE FROM experience WHERE id=?")->execute([$id]);
            if ($type === 'education') db()->prepare("DELETE FROM education WHERE id=?")->execute([$id]);
            flash('success', ucfirst($type) . ' deleted.');
        }
        elseif ($action === 'update') {
            $id = (int)$_POST['id'];
            if ($type === 'skill') {
                db()->prepare("UPDATE skills SET name=?, proficiency=?, category=? WHERE id=?")
                    ->execute([trim($_POST['name']), (int)$_POST['proficiency'], trim($_POST['category']), $id]);
            } elseif ($type === 'experience') {
                db()->prepare("UPDATE experience SET title=?, company=?, start_date=?, end_date=?, description=? WHERE id=?")
                    ->execute([trim($_POST['title']), trim($_POST['company']), $_POST['start_date'], $_POST['end_date'] ?: null, trim($_POST['description']), $id]);
            } elseif ($type === 'education') {
                db()->prepare("UPDATE education SET degree=?, institution=?, start_date=?, end_date=?, description=? WHERE id=?")
                    ->execute([trim($_POST['degree']), trim($_POST['institution']), $_POST['start_date'], $_POST['end_date'] ?: null, trim($_POST['description']), $id]);
            }
            flash('success', ucfirst($type) . ' updated.');
        }
    } catch (Exception $e) {
        flash('error', 'Database error: ' . $e->getMessage());
    }

    redirect('admin/resume/index.php');
}

// Fetch existing data
$skills     = db()->query("SELECT * FROM skills ORDER BY sort_order, id")->fetchAll();
$experience = db()->query("SELECT * FROM experience ORDER BY start_date DESC")->fetchAll();
$education  = db()->query("SELECT * FROM education ORDER BY start_date DESC")->fetchAll();

render_dashboard_header('Resume & Skills Management', 'admin'); 
?>

<div class="row g-4">
    <!-- SKILLS SECTION -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">Manage Skills</div>
            <div class="card-body">
                <form method="post" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="type" value="skill">
                    <input name="name" class="form-control form-control-sm mb-2" placeholder="Skill (e.g. PHP)" required>
                    <input name="category" class="form-control form-control-sm mb-2" placeholder="Category (e.g. Backend)">
                    <input type="number" name="proficiency" class="form-control form-control-sm mb-2" placeholder="Proficiency % (0-100)" min="1" max="100" required>
                    <button class="btn btn-sm btn-success w-100">Add Skill</button>
                </form>
                
                <ul class="list-group list-group-flush">
                    <?php foreach ($skills as $s): ?>
                    <li class="list-group-item px-0">
                        <form method="post" class="d-flex gap-1 align-items-center">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="type" value="skill">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <input name="name" class="form-control form-control-sm" value="<?= e($s['name']) ?>" style="width:100px">
                            <input name="category" class="form-control form-control-sm" value="<?= e($s['category']) ?>" style="width:90px">
                            <input type="number" name="proficiency" class="form-control form-control-sm" value="<?= $s['proficiency'] ?>" style="width:70px">
                            <button class="btn btn-sm btn-outline-primary">💾</button>
                        </form>
                        <form method="post" class="text-end mt-1">
                            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="type" value="skill">
                            <input type="hidden" name="id" value="<?= $s['id'] ?>">
                            <button class="btn btn-sm btn-outline-danger" data-confirm="Delete skill?">🗑️ Delete</button>
                        </form>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- EXPERIENCE SECTION -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">Manage Experience</div>
            <div class="card-body">
                <form method="post" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="type" value="experience">
                    <input name="title" class="form-control form-control-sm mb-2" placeholder="Job Title" required>
                    <input name="company" class="form-control form-control-sm mb-2" placeholder="Company" required>
                    <div class="d-flex gap-1 mb-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" required title="Start Date">
                        <input type="date" name="end_date" class="form-control form-control-sm" title="End Date (leave blank if current)">
                    </div>
                    <textarea name="description" class="form-control form-control-sm mb-2" rows="2" placeholder="Description"></textarea>
                    <button class="btn btn-sm btn-success w-100">Add Experience</button>
                </form>
                
                <?php foreach ($experience as $x): ?>
                <div class="border rounded p-2 mb-2 bg-light">
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="type" value="experience">
                        <input type="hidden" name="id" value="<?= $x['id'] ?>">
                        <input name="title" class="form-control form-control-sm mb-1" value="<?= e($x['title']) ?>">
                        <input name="company" class="form-control form-control-sm mb-1" value="<?= e($x['company']) ?>">
                        <div class="d-flex gap-1 mb-1">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($x['start_date']) ?>">
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($x['end_date']) ?>">
                        </div>
                        <textarea name="description" class="form-control form-control-sm mb-1" rows="2"><?= e($x['description']) ?></textarea>
                        <button class="btn btn-sm btn-outline-primary w-100">Save Changes</button>
                    </form>
                    <form method="post" class="mt-1">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="type" value="experience">
                        <input type="hidden" name="id" value="<?= $x['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger w-100" data-confirm="Delete experience?">Delete</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- EDUCATION SECTION -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">Manage Education</div>
            <div class="card-body">
                <form method="post" class="mb-3 border-bottom pb-3">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="type" value="education">
                    <input name="degree" class="form-control form-control-sm mb-2" placeholder="Degree / Certificate" required>
                    <input name="institution" class="form-control form-control-sm mb-2" placeholder="Institution / School" required>
                    <div class="d-flex gap-1 mb-2">
                        <input type="date" name="start_date" class="form-control form-control-sm" required title="Start Date">
                        <input type="date" name="end_date" class="form-control form-control-sm" title="End Date (or expected)">
                    </div>
                    <textarea name="description" class="form-control form-control-sm mb-2" rows="2" placeholder="Description"></textarea>
                    <button class="btn btn-sm btn-success w-100">Add Education</button>
                </form>
                
                <?php foreach ($education as $ed): ?>
                <div class="border rounded p-2 mb-2 bg-light">
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="type" value="education">
                        <input type="hidden" name="id" value="<?= $ed['id'] ?>">
                        <input name="degree" class="form-control form-control-sm mb-1" value="<?= e($ed['degree']) ?>">
                        <input name="institution" class="form-control form-control-sm mb-1" value="<?= e($ed['institution']) ?>">
                        <div class="d-flex gap-1 mb-1">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?= e($ed['start_date']) ?>">
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?= e($ed['end_date']) ?>">
                        </div>
                        <textarea name="description" class="form-control form-control-sm mb-1" rows="2"><?= e($ed['description']) ?></textarea>
                        <button class="btn btn-sm btn-outline-primary w-100">Save Changes</button>
                    </form>
                    <form method="post" class="mt-1">
                        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="type" value="education">
                        <input type="hidden" name="id" value="<?= $ed['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger w-100" data-confirm="Delete education?">Delete</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php render_dashboard_footer(); ?>