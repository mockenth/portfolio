<?php require_once __DIR__ . '/includes/auth.php';
if ($u = current_user()) log_action((int)$u['id'], 'logout');
logout_user(); flash('success','You have been logged out.'); redirect('index.php');