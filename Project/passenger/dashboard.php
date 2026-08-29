<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

// প্রোফাইল ডাটা
$user_stmt = $conn->query("SELECT name, email FROM users WHERE id = $user_id");
$user = $user_stmt ? $user_stmt->fetch_assoc() : null;

// কাউন্টার মেট্রিকস
$booking_stmt = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE user_id = $user_id");
$total_bookings = $booking_stmt ? $booking_stmt->fetch_assoc()['total'] : 0;

$lost_stmt = $conn->query("SELECT COUNT(*) AS total FROM lost_items WHERE user_id = $user_id");
$total_lost_reports = $lost_stmt ? $lost_stmt->fetch_assoc()['total'] : 0;

$notif_stmt = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND status = 'Unread'");
$unread_notifs = $notif_stmt ? $notif_stmt->fetch_assoc()['total'] : 0;
?>

<h2>স্বাগতম, <?php echo htmlspecialchars($user['name'] ?? 'Passenger'); ?>!</h2>

<div class="stats-grid">
    <div class="stat-card">
        <h3>মোট বুকিং</h3>
        <p><?php echo $total_bookings; ?></p>
    </div>
    <div class="stat-card">
        <h3>লস্ট আইটেম রিপোর্ট</h3>
        <p><?php echo $total_lost_reports; ?></p>
    </div>
    <div class="stat-card">
        <h3>নতুন নোটিফিকেশন</h3>
        <p><?php echo $unread_notifs; ?></p>
    </div>
</div>

<h3>কুইক লিঙ্ক</h3>
<div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
    <a href="search_flights.php" class="btn">ফ্লাইট সার্চ ও বুকিং</a>
    <a href="my_bookings.php" class="btn">বোর্ডিং পাস দেখুন</a>
    <a href="baggage.php" class="btn">ব্যাগেজ ট্র্যাক করুন</a>
    <a href="lost_found.php" class="btn">হারানো জিনিসের রিপোর্ট</a>
</div>

<?php include 'footer.php'; ?>