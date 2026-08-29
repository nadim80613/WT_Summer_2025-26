<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not authenticated or not a passenger
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Passenger') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Portal - Airport Management System</title>
    <link rel="stylesheet" href="passenger.css">
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand">✈ Passenger Portal</div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="search_flights.php">Search & Book Flights</a></li>
            <li><a href="my_bookings.php">My Bookings</a></li>
            <li><a href="boarding_pass.php">Boarding Pass</a></li>
            <li><a href="baggage.php">Baggage Tracker</a></li>
            <li><a href="lost_found.php">Lost & Found</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h2>Airport Management System</h2>
            <div class="user-badge">
                Logged in as: <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Passenger'); ?></strong>
            </div>
        </div>