<?php
require_once __DIR__ . '/includes/auth.php';
$msg = null;
$isError = false;

if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf($_POST['csrf']??null);
    $email = trim($_POST['email']??'');
    $stmt = db()->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    
    if ($row = $stmt->fetch()) {
        $token = bin2hex(random_bytes(32));
        $hash = hash('sha256', $token);
        db()->prepare("INSERT INTO password_resets (user_id,token_hash,expires_at) VALUES (?,?,?)")
            ->execute([$row['id'], $hash, date('Y-m-d H:i:s', time()+3600)]);
        
        // DEV ONLY: Display link on screen
        $link = BASE_URL . "reset-password.php?token=$token&id={$row['id']}";
        $msg = "A reset link has been generated. <br><br><a href=\"".e($link)."\" class=\"btn btn-sm btn-dark mt-2\">Click Here to Reset Password</a>";
    } else {
        $msg = 'If that email exists, a reset link has been generated.';
        $isError = true;
    }
}
$pageTitle='Forgot Password'; 
include 'includes/header.php'; 
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h3 class="mb-3">Forgot Password</h3>
                <p class="text-muted small mb-4">Enter your email address and we'll generate a secure link to reset your password.</p>
                
                <?php if($msg): ?>
                    <div class="alert <?= $isError ? 'alert-info' : 'alert-success' ?>">
                        <?= $msg ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                    </div>
                    <button class="btn btn-primary w-100">Send Reset Link</button>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none small">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>