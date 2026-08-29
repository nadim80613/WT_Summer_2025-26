<?php
require_once '../config/database.php';
include 'header.php';

$result = $conn->query("SELECT * FROM flights ORDER BY departure_time ASC");
?>

<h2>Search & Book Flights</h2>

<table>
    <thead>
        <tr>
            <th>Flight No</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Departure</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['flight_number']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['departure']); ?></td>
                    <td><?php echo htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><span class="badge"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td>
                        <a href="book_flight.php?flight_id=<?php echo $row['id']; ?>" class="btn">Select & Book</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #64748b;">No flights available.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>