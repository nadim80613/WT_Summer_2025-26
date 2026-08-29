<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

$query = "SELECT b.*, f.flight_number, f.departure, f.destination, f.departure_time, f.arrival_time, u.name AS passenger_name 
          FROM bookings b
          JOIN flights f ON b.flight_id = f.id
          JOIN users u ON b.user_id = u.id
          WHERE b.user_id = $user_id " . ($booking_id > 0 ? "AND b.id = $booking_id " : "") . "ORDER BY b.id DESC LIMIT 1";

$result = $conn->query($query);
$pass = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
?>

<h2>Digital Boarding Pass</h2>

<?php if ($pass): ?>
    <div style="background: #ffffff; border: 2px dashed #0284c7; border-radius: 12px; padding: 24px; max-width: 550px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px; margin-bottom: 15px;">
            <div>
                <h3 style="color: #0284c7; margin: 0;">AIRPORT BOARDING PASS</h3>
                <small style="color: #64748b;">Booking Reference: #<?php echo $pass['id']; ?></small>
            </div>
            <div style="text-align: right;">
                <span class="badge badge-scheduled"><?php echo htmlspecialchars($pass['payment_status']); ?></span>
            </div>
        </div>

        <p style="margin-bottom: 8px;"><strong>Passenger Name:</strong> <?php echo htmlspecialchars($pass['passenger_name']); ?></p>
        <p style="margin-bottom: 8px;"><strong>Flight Number:</strong> <?php echo htmlspecialchars($pass['flight_number']); ?></p>
        <p style="margin-bottom: 8px;"><strong>Route:</strong> <?php echo htmlspecialchars($pass['departure']); ?> → <?php echo htmlspecialchars($pass['destination']); ?></p>
        <p style="margin-bottom: 8px;"><strong>Seat Number:</strong> <span style="font-size: 18px; color: #0284c7; font-weight: bold;"><?php echo htmlspecialchars($pass['seat_number']); ?></span></p>
        <p style="margin-bottom: 8px;"><strong>Departure:</strong> <?php echo htmlspecialchars($pass['departure_time']); ?></p>
        <p><strong>Arrival:</strong> <?php echo htmlspecialchars($pass['arrival_time']); ?></p>
    </div>
<?php else: ?>
    <div style="background: #ffffff; padding: 20px; border-radius: 8px; margin-top: 20px;">
        <p>No active boarding pass found. <a href="search_flights.php" style="color: #0284c7;">Book a flight here</a>.</p>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>