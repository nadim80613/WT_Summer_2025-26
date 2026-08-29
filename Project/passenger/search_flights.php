<?php
require_once '../config/database.php';
include 'header.php';

$where = "WHERE 1=1";
if (!empty($_GET['from'])) {
    $from = $conn->real_escape_string($_GET['from']);
    $where .= " AND departure LIKE '%$from%'";
}
if (!empty($_GET['to'])) {
    $to = $conn->real_escape_string($_GET['to']);
    $where .= " AND destination LIKE '%$to%'";
}

$sql = "SELECT * FROM flights $where ORDER BY departure_time ASC";
$result = $conn->query($sql);
?>

<h2>Search Available Flights</h2>

<form method="GET" action="search_flights.php" class="form-grid">
    <div class="form-group">
        <label>From (Departure)</label>
        <input type="text" name="from" placeholder="e.g. DAC" value="<?php echo htmlspecialchars($_GET['from'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label>To (Destination)</label>
        <input type="text" name="to" placeholder="e.g. JFK" value="<?php echo htmlspecialchars($_GET['to'] ?? ''); ?>">
    </div>
    <div class="form-group" style="align-self: flex-end;">
        <button type="submit" class="btn">Filter Flights</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Flight No</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
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
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td><span class="badge badge-scheduled"><?php echo htmlspecialchars($row['status']); ?></span></td>
                    <td>
                        <a href="book_flight.php?flight_id=<?php echo $row['id']; ?>" class="btn">Book Now</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b;">No flights available matching your criteria.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>