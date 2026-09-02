<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$msg = "";


if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    $del = $conn->query("DELETE FROM bookings WHERE id = $cancel_id AND user_id = $user_id");
    if ($del) {
        $conn->query("DELETE FROM baggage WHERE booking_id = $cancel_id");
        $conn->query("INSERT INTO notifications (user_id, message, type, status, created_at) 
                     VALUES ($user_id, 'Booking #$cancel_id has been cancelled.', 'Cancellation', 'Unread', NOW())");
        $msg = "<div style='color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 12px; border-radius: 8px; margin-bottom: 15px;'>Booking #$cancel_id cancelled successfully.</div>";
    }
}


$sql = "SELECT b.id AS booking_id, b.seat_number, b.payment_status, f.flight_number, f.departure, f.destination, f.departure_time 
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE b.user_id = $user_id AND f.departure_time > NOW()
        ORDER BY f.departure_time ASC";
$result = $conn->query($sql);
?>

<h2>Upcoming Bookings</h2>
<?php echo $msg; ?>

<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Flight No</th>
            <th>Route</th>
            <th>Departure</th>
            <th>Seat(s)</th>
            <th>Status</th>
            <th>Manage</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $row['booking_id']; ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['departure']); ?> → <?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td>
                        <span class="badge" style="background: #e0f2fe; color: #0284c7; font-weight: 800; font-size: 13px;">
                            <?php echo htmlspecialchars($row['seat_number']); ?>
                        </span>
                    </td>
                    <td><span class="badge" style="background: #ecfdf5; color: #059669;"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
                    <td>
                        <a href="boarding_pass.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">Pass</a>
                        <a href="my_bookings.php?cancel_id=<?php echo $row['booking_id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?');" class="btn" style="background: #fee2e2; color: #dc2626; padding: 5px 10px; font-size: 12px;">Cancel</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b; padding: 25px;">
                    No upcoming flight bookings found. <a href="search_flights.php" style="color: #0284c7; font-weight: bold;">Book now</a>.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>