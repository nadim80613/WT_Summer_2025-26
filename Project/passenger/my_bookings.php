<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$msg = "";

// Handle Booking Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = (int)$_GET['cancel_id'];
    
    // Check if valid upcoming booking belongs to user
    $chk = $conn->query("SELECT b.id, b.seat_number, f.flight_number 
                         FROM bookings b 
                         JOIN flights f ON b.flight_id = f.id 
                         WHERE b.id = $cancel_id AND b.user_id = $user_id AND f.departure_time > NOW()");
    if ($chk && $chk->num_rows > 0) {
        $b_info = $chk->fetch_assoc();
        
        // Remove related baggage & booking records
        $conn->query("DELETE FROM baggage WHERE booking_id = $cancel_id");
        $conn->query("DELETE FROM bookings WHERE id = $cancel_id");
        
        // Notification
        $conn->query("INSERT INTO notifications (user_id, message, type, status, created_at) 
                     VALUES ($user_id, 'Booking #$cancel_id for flight {$b_info['flight_number']} (Seat {$b_info['seat_number']}) was cancelled.', 'Cancellation', 'Unread', NOW())");
        
        $msg = "<div style='color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 6px; margin-bottom: 15px;'>Booking #$cancel_id has been successfully cancelled. Seat {$b_info['seat_number']} is now released.</div>";
    }
}

if (isset($_GET['booked'])) {
    $msg = "<div style='color: #059669; background: #ecfdf5; padding: 12px; border-radius: 6px; margin-bottom: 15px;'>Flight ticket booked successfully!</div>";
}
if (isset($_GET['updated'])) {
    $msg = "<div style='color: #059669; background: #ecfdf5; padding: 12px; border-radius: 6px; margin-bottom: 15px;'>Seat number updated successfully!</div>";
}

// ONLY UPCOMING BOOKINGS (departure_time > NOW())
$sql = "SELECT b.id AS booking_id, b.flight_id, b.seat_number, b.booking_date, b.payment_status, 
               f.flight_number, f.departure, f.destination, f.departure_time 
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE b.user_id = $user_id 
          AND f.departure_time > NOW()
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
            <th>Seat</th>
            <th>Status</th>
            <th>Manage</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['booking_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['departure']) . ' → ' . htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><span class="badge" style="font-weight: bold;"><?php echo htmlspecialchars($row['seat_number']); ?></span></td>
                    <td><span class="badge badge-scheduled"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="edit_booking.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn" style="background: #e2e8f0; color: #0f172a; padding: 6px 10px; font-size: 12px;">Edit Seat</a>
                            <a href="my_bookings.php?cancel_id=<?php echo $row['booking_id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?')" class="btn" style="background: #fee2e2; color: #dc2626; padding: 6px 10px; font-size: 12px;">Cancel</a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">
                    No upcoming flight bookings found. <a href="search_flights.php" style="color: #0284c7; font-weight: bold;">Book a flight here</a>.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>