<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$b_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

$sql = "SELECT b.*, f.flight_number, f.departure, f.destination, f.departure_time, u.name 
        FROM bookings b
        JOIN flights f ON b.flight_id = f.id
        JOIN users u ON b.user_id = u.id
        WHERE b.user_id = $user_id " . ($b_id > 0 ? "AND b.id = $b_id " : "") . "ORDER BY b.id DESC LIMIT 1";

$pass = $conn->query($sql)->fetch_assoc();
?>

<h2>Digital Boarding Pass</h2>

<?php if ($pass): ?>
    <div style="background: #fff; border: 2px dashed #0284c7; padding: 20px; border-radius: 10px; max-width: 480px; margin-top: 15px;">
        <h3 style="color: #0284c7; margin-bottom: 10px;">BOARDING PASS</h3>
        <p><strong>Passenger:</strong> <?php echo htmlspecialchars($pass['name']); ?></p>
        <p><strong>Flight:</strong> <?php echo htmlspecialchars($pass['flight_number']); ?></p>
        <p><strong>Route:</strong> <?php echo htmlspecialchars($pass['departure']); ?> → <?php echo htmlspecialchars($pass['destination']); ?></p>
        <p><strong>Seat:</strong> <span style="color: #0284c7; font-size: 18px; font-weight: bold;"><?php echo htmlspecialchars($pass['seat_number']); ?></span></p>
        <p><strong>Departure:</strong> <?php echo htmlspecialchars($pass['departure_time']); ?></p>
    </div>
<?php else: ?>
    <p style="margin-top: 15px;">No boarding pass found.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>