<?php
function render_dashboard_header(string $title, string $role): void {
    $u = current_user();
    $isAdmin = $role === 'admin';
    $current = $_SERVER['REQUEST_URI'];
    $base = $isAdmin ? BASE_URL.'admin/' : BASE_URL.'user/';
    
    $items = $isAdmin ? [
        'dashboard.php'         => ['Dashboard', 'bi-speedometer2'],
        'projects/index.php'    => ['Projects', 'bi-folder'],
        'designs/index.php'     => ['Graphic Designs', 'bi-palette'],
        'categories/index.php'  => ['Categories', 'bi-tags'],
        'services/index.php'    => ['Services', 'bi-briefcase'],
        'resume/index.php'      => ['Resume & Skills', 'bi-file-earmark-person'],
        'certificates/index.php'=> ['Certificates', 'bi-award'],
        'users/index.php'       => ['Users', 'bi-people'],
        'messages/index.php'    => ['Messages', 'bi-envelope'],
        'inquiries/index.php'   => ['Inquiries', 'bi-chat-dots'],
        'content/index.php'     => ['Content', 'bi-file-text'],
        'settings/index.php'    => ['Settings', 'bi-gear']
    ] : [
        'dashboard.php'     => ['Dashboard', 'bi-speedometer2'],
        'profile.php'       => ['Profile', 'bi-person'],
        'favorites.php'     => ['Favorites', 'bi-star'],
        'inquiries.php'     => ['Inquiries', 'bi-chat-dots'],
        'notifications.php' => ['Notifications', 'bi-bell'],
        'settings.php'      => ['Settings', 'bi-gear']
    ];
    ?>
    <!DOCTYPE html>
    <html lang="en" data-bs-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> — <?= $isAdmin?'Admin':'User' ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
        <style>
            /* Mobile top bar */
            .mobile-topbar {
                display: none;
                background-color: var(--footer-bg);
                border-bottom: 1px solid var(--border-color);
                padding: 10px 15px;
            }
            @media (max-width: 991.98px) {
                .mobile-topbar { display: flex; align-items: center; justify-content: space-between; }
            }
            /* Sidebar Styling */
            .sidebar-desktop {
                width: 260px;
                min-height: 100vh;
                background-color: var(--footer-bg);
            }
            .dashboard-sidebar .nav-link { 
                color: #a0a0a0; 
                border-radius: 6px;
                margin-bottom: 2px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .dashboard-sidebar .nav-link:hover {
                color: #fff;
                background-color: rgba(255,255,255,0.05);
            }
            .dashboard-sidebar .nav-link.active { 
                background: var(--brand-primary); 
                color: #000 !important; 
                font-weight: 600;
            }
            .dashboard-sidebar .nav-link i {
                font-size: 1.1rem;
            }
        </style>
    </head>
    <body>
    
    <!-- Mobile Top Bar (Hamburger Menu) -->
    <div class="mobile-topbar text-white">
        <h5 class="mb-0 fs-6 fw-bold"><?= $isAdmin?'Admin Panel':'My Account' ?></h5>
        <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <i class="bi bi-list fs-5"></i>
        </button>
    </div>

    <!-- Desktop Wrapper -->
    <div class="d-flex">
      
      <!-- DESKTOP SIDEBAR (Hidden on Mobile) -->
      <aside class="sidebar-desktop d-none d-lg-block flex-shrink-0 text-white p-3 dashboard-sidebar">
        <h4 class="mb-4 text-white fw-bold ps-2"><?= $isAdmin?'Admin':'My Account' ?></h4>
        <ul class="nav flex-column">
            <?php foreach ($items as $href=>$data): 
                $active = str_contains($current,$href)?'active':''; 
                $label = $data[0]; $icon = $data[1];
            ?>
            <li class="nav-item">
                <a class="nav-link <?= $active ?>" href="<?= $base.$href ?>">
                    <i class="bi <?= $icon ?>"></i> <?= e($label) ?>
                </a>
            </li>
            <?php endforeach; ?>
            <hr class="text-secondary">
            <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php"><i class="bi bi-box-arrow-up-right"></i> View Site</a></li>
            <li class="nav-item"><a class="nav-link text-warning" href="<?= BASE_URL ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
      </aside>

      <!-- MOBILE SIDEBAR (Offcanvas Slide-out) -->
      <div class="offcanvas offcanvas-start text-bg-dark dashboard-sidebar" tabindex="-1" id="mobileSidebar">
        <div class="offcanvas-header border-bottom border-secondary">
            <h5 class="offcanvas-title fw-bold"><?= $isAdmin?'Admin Menu':'My Account' ?></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-3">
            <ul class="nav flex-column">
                <?php foreach ($items as $href=>$data): 
                    $active = str_contains($current,$href)?'active':''; 
                    $label = $data[0]; $icon = $data[1];
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= $active ?>" href="<?= $base.$href ?>">
                        <i class="bi <?= $icon ?>"></i> <?= e($label) ?>
                    </a>
                </li>
                <?php endforeach; ?>
                <hr class="text-secondary">
                <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>index.php"><i class="bi bi-box-arrow-up-right"></i> View Site</a></li>
                <li class="nav-item"><a class="nav-link text-warning" href="<?= BASE_URL ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </div>
      </div>
      
      <!-- Main Content Area -->
      <div class="flex-grow-1 p-3 p-md-4 bg-body" style="min-height: 100vh;">
        <h1 class="h3 mb-4 fw-bold"><?= e($title) ?></h1>
        <?php if ($m=flash('success')): ?><div class="alert alert-success shadow-sm"><?= e($m) ?></div><?php endif; ?>
        <?php if ($m=flash('error')): ?><div class="alert alert-danger shadow-sm"><?= e($m) ?></div><?php endif; ?>
    <?php
}

function render_dashboard_footer(): void {
    ?>
      </div> <!-- End Main Content -->
    </div> <!-- End Wrapper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/app.js"></script>
    </body>
    </html>
    <?php
}
?>