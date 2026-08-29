<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Portal</title>
    <link rel="stylesheet" href="../assets/css/passenger.css">
</head>
<body>
    <nav class="passenger-nav">
        <div class="logo">✈ Airport Passenger Portal</div>
        <ul>
            <li><a href="dashboard.php">ড্যাশবোর্ড</a></li>
            <li><a href="search_flights.php">ফ্লাইট সার্চ</a></li>
            <li><a href="my_bookings.php">আমার বুকিং</a></li>
            <li><a href="baggage.php">ব্যাগেজ ট্র্যাকিং</a></li>
            <li><a href="lost_found.php">লস্ট অ্যান্ড ফাউন্ড</a></li>
            <li><a href="notifications.php">নোটিফিকেশন</a></li>
            <li><a href="../logout.php" style="color: #f87171;">লগআউট</a></li>
        </ul>
    </nav>
    <div class="passenger-container">