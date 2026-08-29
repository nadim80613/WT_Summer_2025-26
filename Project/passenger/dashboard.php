<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

// Fetch user profile
$user_stmt = $conn->query("SELECT name, email FROM users WHERE id = $user_id");
$user = $user_stmt ? $user_stmt->fetch_assoc() : null;

// Metrics count
$booking_stmt = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id");
$total_bookings = $booking_stmt ? $booking_stmt->fetch_assoc()['total'] : 0;

$lost_stmt = $conn->query("SELECT COUNT(*) AS total FROM lost_items WHERE user_id = $user_id");
$total_lost_reports = $lost_stmt ? $lost_stmt->fetch_assoc()['total'] : 0;

$notif_stmt = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND status = 'Unread'");
$unread_notifs = $notif_stmt ? $notif_stmt->fetch_assoc()['total'] : 0;
?>

<h2>Welcome back, <?php echo htmlspecialchars($user['name'] ?? 'Passenger'); ?>!</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Bookings</h3>
        <p><?php echo $total_bookings; ?></p>
    </div>
    <div class="stat-card">
        <h3>Lost Item Claims</h3>
        <p><?php echo $total_lost_reports; ?></p>
    </div>
    <div class="stat-card">
        <h3>Unread Alerts</h3>
        <p><?php echo $unread_notifs; ?></p>
    </div>
</div>

<h3>Quick Actions</h3>
<div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
    <a href="search_flights.php" class="btn">Search & Book Flights</a>
    <a href="my_bookings.php" class="btn">View Boarding Pass</a>
    <a href="baggage.php" class="btn">Track Baggage</a>
    <a href="lost_found.php" class="btn">Report Lost Luggage</a>
</div>

<?php include 'footer.php'; ?>