<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT b.id AS booking_id, b.seat_number, b.payment_status, 
               f.flight_number, f.departure, f.destination, f.departure_time 
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        WHERE b.user_id = $user_id
        ORDER BY b.id DESC";

$result = $conn->query($sql);
?>

<h2>My Bookings</h2>

<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Flight No</th>
            <th>Route</th>
            <th>Departure</th>
            <th>Seat</th>
            <th>Payment Status</th>
            <th>Action</th>
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
                    <td><span class="badge badge-boarding"><?php echo htmlspecialchars($row['seat_number']); ?></span></td>
                    <td><span class="badge badge-scheduled"><?php echo htmlspecialchars($row['payment_status']); ?></span></td>
                    <td>
                        <a href="boarding_pass.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn">View Pass</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">
                    No bookings found. <a href="search_flights.php" style="color: #0284c7; font-weight: bold;">Search flights now</a>.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>