<?php
require_once '../config/database.php';
include 'header.php';

$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to   = isset($_GET['to']) ? trim($_GET['to']) : '';


$sql = "SELECT * FROM flights 
        WHERE departure_time > NOW() 
        AND status NOT IN ('Departed', 'Cancelled')";

if (!empty($from)) {
    $from_safe = $conn->real_escape_string($from);
    $sql .= " AND departure LIKE '%$from_safe%'";
}
if (!empty($to)) {
    $to_safe = $conn->real_escape_string($to);
    $sql .= " AND destination LIKE '%$to_safe%'";
}

$sql .= " ORDER BY departure_time ASC";

$result = $conn->query($sql);
?>

<h2>Search Available Flights</h2>

<form method="GET" action="search_flights.php" style="display: flex; gap: 15px; margin: 20px 0; align-items: flex-end;">
    <div style="flex: 1;">
        <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">From (Departure)</label>
        <input type="text" name="from" value="<?php echo htmlspecialchars($from); ?>" placeholder="e.g. DAC" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
    </div>
    <div style="flex: 1;">
        <label style="display: block; margin-bottom: 5px; font-size: 13px; font-weight: 600;">To (Destination)</label>
        <input type="text" name="to" value="<?php echo htmlspecialchars($to); ?>" placeholder="e.g. JFK / Raj" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
    </div>
    <button type="submit" class="btn" style="padding: 10px 20px;">Filter Flights</button>
    <?php if (!empty($from) || !empty($to)): ?>
        <a href="search_flights.php" class="btn" style="background: #64748b; padding: 10px 15px;">Clear</a>
    <?php endif; ?>
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
                        <a href="book_flight.php?flight_id=<?php echo $row['id']; ?>" class="btn">Select & Book</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748b; padding: 20px;">
                    No active upcoming flights available at this moment.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>