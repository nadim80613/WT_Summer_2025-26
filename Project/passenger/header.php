<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: If not logged in or role is not passenger, kick to login page
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'passenger') {
    header("Location: ../login.php");
    exit();
}

// Current logged in user details
$logged_user_name = $_SESSION['user_name'] ?? 'Passenger';
$logged_user_id   = (int)$_SESSION['user_id'];
?>