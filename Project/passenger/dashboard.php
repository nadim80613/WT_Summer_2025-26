<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

// Fetch user details
$user_stmt = $conn->query("SELECT name, email FROM users WHERE id = $user_id");
$user = $user_stmt ? $user_stmt->fetch_assoc() : null;

// Fetch metrics
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

<h3 style="margin-top: 30px; margin-bottom: 15px;">Recent Bookings</h3>
<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Flight</th>
            <th>Route</th>
            <th>Departure</th>
            <th>Seat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $recent_sql = "SELECT b.id, b.seat_number, b.payment_status, f.flight_number, f.departure, f.destination, f.departure_time
                       FROM bookings b
                       JOIN flights f ON b.flight_id = f.id
                       WHERE b.user_id = $user_id
                       ORDER BY b.id DESC LIMIT 5";
        $recent_res = $conn->query($recent_sql);

        if ($recent_res && $recent_res->num_rows > 0):
            while ($row = $recent_res->fetch_assoc()):
        ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['departure']) . ' → ' . htmlspecialchars($row['destination']); ?></td>
                <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                <td><span class="badge badge-scheduled"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
            </tr>
        <?php 
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #64748b;">No recent bookings found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>