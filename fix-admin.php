<?php
require_once __DIR__ . '/config/database.php';

$email = 'admin@example.com';
$new_password = 'Admin@12345';

// Generate a fresh hash
$hash = password_hash($new_password, PASSWORD_DEFAULT);

// Force update the database
$stmt = db()->prepare("UPDATE users SET password = ?, role = 'admin', status = 'active' WHERE email = ?");
$stmt->execute([$hash, $email]);

if ($stmt->rowCount() > 0) {
    echo "<h2>SUCCESS!</h2> Account restored. <br>";
    echo "<b>Email:</b> $email <br><b>Password:</b> $new_password <br>";
    echo "<a href='login.php'>Go to Login</a>";
} else {
    echo "<h2>ERROR: User not found in database.</h2>";
}