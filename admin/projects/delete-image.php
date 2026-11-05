<?php
require_once __DIR__ . '/../_init.php';
verify_csrf($_POST['csrf']??null);
$imageId = (int)($_POST['image_id']??0);
$projectId = (int)($_POST['project_id']??0);
$stmt = db()->prepare("SELECT image_path FROM project_images WHERE id=? AND project_id=?");
$stmt->execute([$imageId,$projectId]);
if ($row = $stmt->fetch()) {
    if (is_file(UPLOAD_DIR.$row['image_path'])) @unlink(UPLOAD_DIR.$row['image_path']);
    db()->prepare("DELETE FROM project_images WHERE id=?")->execute([$imageId]);
}
flash('success','Image deleted.');
redirect('admin/projects/edit.php?id='.$projectId);