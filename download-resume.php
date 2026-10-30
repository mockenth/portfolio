<?php
require_once __DIR__ . '/includes/auth.php';

// 1. Get the file path
$resumePath = get_setting('resume_file');

if (!$resumePath) {
    flash('error', 'Resume file not found.');
    redirect('about.php');
}

// 2. Increment the download counter
// Note: Since site_settings is cached, we update it directly in the DB
$stmt = db()->prepare("UPDATE site_settings SET setting_value = setting_value + 1 WHERE setting_key = 'resume_downloads'");
$stmt->execute();

// 3. Force the browser to download the file
$file = UPLOAD_DIR . $resumePath;
if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Resume.pdf"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    flash('error', 'File not found on server.');
    redirect('about.php');
}