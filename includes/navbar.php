<?php
$u = current_user();
$current = basename($_SERVER['PHP_SELF']);
$links = ['index.php'=>'Home',
          'about.php'=>'About',
          'portfolio.php'=>'Portfolio',
          'services.php'=>'Services',
          'contact.php'=>'Contact'];
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>index.php">
      <?= e(get_setting('owner_name','Portfolio')) ?>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <?php foreach ($links as $href=>$label): ?>
        <li class="nav-item">
          <a class="nav-link <?= $current===$href?'active':'' ?>" href="<?= BASE_URL.$href ?>">
            <?= e($label) ?>
          </a>
        </li>
        <?php endforeach; ?>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item"><button id="themeToggle" class="btn btn-sm btn-outline-light me-2">
          <i class="bi bi-moon"></i></button></li>
        <?php if ($u): ?>
          <?php if ($u['role']==='admin'): ?>
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>admin/dashboard.php">Admin</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>user/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>login.php">Login</a></li>
          <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="<?= BASE_URL ?>register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>