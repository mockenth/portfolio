<?php
require_once __DIR__ . '/auth.php';
$pageTitle = $pageTitle ?? get_setting('site_title');
$metaDesc  = $metaDesc  ?? get_setting('site_description');
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/navbar.php'; ?>
<main class="container py-4">
<?php if ($msg = flash('success')): ?>
  <div class="alert alert-success alert-dismissible fade show"><?= e($msg) ?>
    <button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
  <div class="alert alert-danger alert-dismissible fade show"><?= e($msg) ?>
    <button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?php if ($msg = flash('guest_notice')): ?>
  <!-- Special Guest Notification -->
  <div class="alert alert-warning alert-dismissible fade show shadow-sm border-start border-4 border-warning" role="alert">
    <div class="d-flex align-items-start">
      <i class="bi bi-person-exclamation fs-3 me-3 text-warning"></i>
      <div>
        <h5 class="alert-heading fw-bold">Message Received!</h5>
        <p class="mb-2"><?= e($msg) ?></p>
        <div class="mt-2">
            <a href="<?= BASE_URL ?>register.php" class="btn btn-sm btn-dark fw-bold"><i class="bi bi-person-plus"></i> Register Now</a>
        </div>
      </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>