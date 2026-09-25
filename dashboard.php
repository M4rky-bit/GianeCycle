<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

if (($_SESSION['user']['role'] ?? '') === 'admin') {
    header('Location: admin-dashboard.php');
    exit;
}

header('Location: user-dashboard.php');
exit;
