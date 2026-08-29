<?php
require_once '../config/database.php';
include 'header.php';

$departure = isset($_GET['departure']) ? trim($_GET['departure']) : '';
$destination = isset($_GET['destination']) ? trim($_GET['destination']) : '';

$sql = "SELECT * FROM flights WHERE 1=1";

if (!empty($departure)) {
    $departure_safe = $conn->real_escape_string($departure);
    $sql .= " AND departure LIKE '%$departure_safe%'";
}

if (!empty($destination)) {
    $destination_safe = $conn->real_escape_string($destination);
    $sql .= " AND destination LIKE '%$destination_safe%'";
}

$sql .= " ORDER BY departure_time ASC";
$result = $conn->query($sql);
?>

<h2>Search Flights & Real-Time Status</h2>

<form method="GET" action="search_flights.php" class="form-grid">
    <div class="form-group">
        <label>Departure City / Airport Code</label>
        <input type="text" name="departure" value="<?php echo htmlspecialchars($departure); ?>" placeholder="e.g. DAC">
    </div>
    <div class="form-group">
        <label>Destination City / Airport Code</label>
        <input type="text" name="destination" value="<?php echo htmlspecialchars($destination); ?>" placeholder="e.g. JFK">
    </div>
    <div class="form-group" style="align-self: flex-end;">
        <button type="submit" class="btn">Search Flights</button>
    </div>
</form>

<div class="form-group" style="margin-bottom: 15px;">
    <input type="text" id="tableFilterInput" placeholder="Quick filter search results...">
</div>

<table id="flightTable">
    <thead>
        <tr>
            <th>Flight No</th>
            <th>Route</th>
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
                    <td><?php echo htmlspecialchars($row['departure']) . ' → ' . htmlspecialchars($row['destination']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_time']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo strtolower($row['status']); ?>">
                            <?php echo htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($row['status'] !== 'Cancelled'): ?>
                            <a href="book_flight.php?flight_id=<?php echo $row['id']; ?>" class="btn">Book Now</a>
                        <?php else: ?>
                            <span style="color: #94a3b8; font-size: 13px;">Cancelled</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #64748b;">No flights found matching your search.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
