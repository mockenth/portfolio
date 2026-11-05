<?php
require_once __DIR__ . '/../_init.php';
verify_csrf($_POST['csrf']??null);
$id   = (int)($_POST['id']??0);
$name = trim($_POST['name']??'');
$slug = slugify($_POST['slug']??$name);
if ($id && $name) {
    db()->prepare("UPDATE categories SET name=?,slug=?,description=? WHERE id=?")
        ->execute([$name,$slug,trim($_POST['description']??''),$id]);
    flash('success','Category updated.');
}
redirect('admin/categories/index.php');