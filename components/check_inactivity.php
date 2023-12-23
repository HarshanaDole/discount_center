<?php
session_start();

// Set the inactivity timeout in seconds (3 hours = 180 minutes)
$inactivityTimeout = 30 * 60; // 180 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactivityTimeout) {
    session_unset();
    session_destroy();
    header('location: admin_login.php');
    exit;
}

$_SESSION['last_activity'] = time();
?>
