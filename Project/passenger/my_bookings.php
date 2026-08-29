<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$sql = "SELECT b.id, b.seat_number, b.payment_status, f.flight_number, f.departure, f.destination, f.departure_time 
        FROM bookings b 
        JOIN flights f ON b.flight_id = f.id 
        WHERE b.user_id = $user_id ORDER BY b.id DESC";
$res = $conn->query($sql);
?>

<h2>My Bookings</h2>

<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Flight</th>
            <th>Route</th>
            <th>Seat</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['departure']) . ' → ' . htmlspecialchars($row['destination']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['seat_number']); ?></strong></td>
                    <td><span class="badge"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
                    <td><a href="boarding_pass.php?booking_id=<?php echo $row['id']; ?>" class="btn">Boarding Pass</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align: center; color: #64748b;">No bookings found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>