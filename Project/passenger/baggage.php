<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

$sql = "SELECT bg.*, b.seat_number, f.flight_number 
        FROM baggage bg
        JOIN bookings b ON bg.booking_id = b.id
        JOIN flights f ON b.flight_id = f.id
        WHERE bg.user_id = $user_id
        ORDER BY bg.id DESC";

$result = $conn->query($sql);
?>

<h2>Baggage Tracker</h2>

<table>
    <thead>
        <tr>
            <th>Tag / Baggage ID</th>
            <th>Booking ID</th>
            <th>Flight No</th>
            <th>Seat</th>
            <th>Current Location</th>
            <th>Baggage Status</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#BG-<?php echo $row['id']; ?></td>
                    <td>#<?php echo $row['booking_id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($row['baggage_status']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">
                    No baggage records found.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>