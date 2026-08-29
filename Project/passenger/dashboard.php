<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

$booking_res = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id");
$total_bookings = $booking_res ? $booking_res->fetch_assoc()['total'] : 0;
?>

<h2>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>

<div class="stats-grid" style="margin-top: 20px;">
    <div class="stat-card">
        <h3>Total Bookings</h3>
        <p><?php echo $total_bookings; ?></p>
    </div>
</div>

<h3 style="margin-top: 25px;">Quick Actions</h3>
<p style="margin-top: 10px;">
    <a href="search_flights.php" class="btn">Book a Flight</a>
    <a href="my_bookings.php" class="btn" style="background: #475569;">My Bookings</a>
</p>

<?php include 'footer.php'; ?>