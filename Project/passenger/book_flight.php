<?php
require_once '../config/database.php';
include 'header.php';

$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;
$user_id = (int)$_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_number = $conn->real_escape_string(trim($_POST['seat_number']));
    $flight_id = (int)$_POST['flight_id'];

    if (!empty($seat_number) && $flight_id > 0) {
        $insert = "INSERT INTO bookings (user_id, flight_id, seat_number, payment_status) 
                   VALUES ($user_id, $flight_id, '$seat_number', 'Paid')";
        if ($conn->query($insert)) {
            $new_booking_id = $conn->insert_id;
            header("Location: boarding_pass.php?booking_id=$new_booking_id");
            exit();
        } else {
            $message = "<div style='color: red; margin-bottom: 15px;'>Booking failed: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='color: red; margin-bottom: 15px;'>Please provide a valid seat number.</div>";
    }
}

$flight = null;
if ($flight_id > 0) {
    $flight_res = $conn->query("SELECT * FROM flights WHERE id = $flight_id");
    if ($flight_res && $flight_res->num_rows > 0) {
        $flight = $flight_res->fetch_assoc();
    }
}
?>

<h2>Book Flight Ticket</h2>
<?php echo $message; ?>

<?php if ($flight): ?>
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 6px; margin-bottom: 20px; max-width: 500px;">
        <p><strong>Flight:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
        <p><strong>Route:</strong> <?php echo htmlspecialchars($flight['departure']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></p>
        <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($flight['departure_time']); ?></p>
    </div>

    <form method="POST" action="book_flight.php" style="max-width: 500px;">
        <input type="hidden" name="flight_id" value="<?php echo $flight['id']; ?>">
        <div class="form-group">
            <label>Select Seat Number (e.g. 14A, 12B)</label>
            <input type="text" name="seat_number" required placeholder="14A">
        </div>
        <button type="submit" class="btn">Confirm & Pay</button>
    </form>
<?php else: ?>
    <p>Flight not found. <a href="search_flights.php">Browse flights</a>.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>