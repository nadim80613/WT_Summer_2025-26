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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Portal</title>
    <link rel="stylesheet" href="passenger.css">
</head>
<body>
    <nav class="passenger-nav">
        <div class="logo">✈ Airport Passenger Portal</div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="search_flights.php">Search Flights</a></li>
            <li><a href="my_bookings.php">My Bookings</a></li>
            <li><a href="baggage.php">Track Baggage</a></li>
            <li><a href="lost_found.php">Lost & Found</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="../logout.php" style="color: #f87171;">Logout</a></li>
        </ul>
    </nav>
    <div class="passenger-container">