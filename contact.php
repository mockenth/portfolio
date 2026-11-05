<?php
require_once __DIR__ . '/includes/auth.php';
$errors = [];
$ok = false;
$u = current_user(); // Fetched once, used in the form below

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf($_POST['csrf'] ?? null);
    if (!rate_limit('contact', 5, 300)) $errors[] = 'Too many submissions. Please wait a few minutes.';
    
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (strlen($name) < 2) $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
    if (strlen($subject) < 3) $errors[] = 'Subject is too short.';
    if (strlen($message) < 10) $errors[] = 'Message is too short.';

    if (!$errors) {
        // 1. Save to database
        db()->prepare("INSERT INTO contact_messages (name,email,subject,message) VALUES (?,?,?,?)")
            ->execute([$name,$email,$subject,$message]);
        
        // 2. Check user status
        if (!$u) {
            // Guest notification
            flash('guest_notice', "Thank you, " . e($name) . "! Your message was sent. However, since you are not logged in, you won't be able to track this message or receive updates. Register an account to stay connected.");
        } else {
            // Logged-in notification
            flash('success', 'Thank you! Your message has been sent.');
        }
        
        $ok = true;
    }
}
$prefill = $_GET['service'] ?? '';
$pageTitle = 'Contact';
include 'includes/header.php'; 
?>

<div class="row g-4 mt-2">
  <div class="col-lg-6">
    <h1 class="mb-3">Contact Me</h1>
    
    <?php if ($ok): ?>
      <div class="alert alert-success shadow-sm border-0">
        <i class="bi bi-check-circle-fill me-2"></i> Your message has been successfully sent!
      </div>
    <?php endif; ?>
    
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-danger shadow-sm border-0"><?= e($e) ?></div>
    <?php endforeach; ?>
    
    <form method="post" class="card p-4 shadow-sm border-0" novalidate>
      <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
      <div class="mb-3">
        <label class="form-label fw-semibold">Your Name</label>
        <input name="name" class="form-control" required value="<?= e($_POST['name'] ?? ($u ? $u['name'] : '')) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Your Email</label>
        <input type="email" name="email" class="form-control" required value="<?= e($_POST['email'] ?? ($u ? $u['email'] : '')) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Subject</label>
        <input name="subject" class="form-control" required value="<?= e($_POST['subject'] ?? $prefill) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Message</label>
        <textarea name="message" class="form-control" rows="5" required><?= e($_POST['message']??'') ?></textarea>
      </div>
      <button class="btn btn-primary w-100">Send Message</button>
    </form>
  </div>
  
  <div class="col-lg-6">
    <div class="card p-4 shadow-sm border-0 mb-3">
      <h5 class="mb-3">Contact Information</h5>
      <ul class="list-unstyled mb-0">
        <li class="mb-2"><i class="bi bi-envelope me-2 text-primary"></i> <?= e(get_setting('owner_email')) ?></li>
        <li><i class="bi bi-telephone me-2 text-primary"></i> <?= e(get_setting('owner_phone')) ?></li>
      </ul>
    </div>
    
    <div class="card p-4 shadow-sm border-0">
      <h5 class="mb-3">Follow Me</h5>
      <?php foreach (['social_github'=>'GitHub','social_linkedin'=>'LinkedIn','social_twitter'=>'Twitter'] as $k=>$lbl): ?>
        <?php if ($v=get_setting($k)): ?>
          <a class="btn btn-sm btn-outline-secondary me-2 mb-2" href="<?= e($v) ?>" target="_blank"><?= e($lbl) ?></a>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>