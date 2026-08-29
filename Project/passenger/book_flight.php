<?php
require_once '../config/database.php';
include 'header.php';

$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;
$user_id = (int)$_SESSION['user_id'];
$message = "";

$flight_res = $conn->query("SELECT * FROM flights WHERE id = $flight_id");
$flight = $flight_res ? $flight_res->fetch_assoc() : null;

if (!$flight) {
    echo "<p>Flight not found. <a href='search_flights.php'>Return to Flight Search</a></p>";
    include 'footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat_number = $conn->real_escape_string(strtoupper(trim($_POST['seat_number'])));

    if (!preg_match("/^[0-9]{1,2}[A-F]$/", $seat_number)) {
        $message = "<div class='alert alert-danger'>Invalid seat format! Use format like 12A, 4B, 22F.</div>";
    } else {
        $seat_check = $conn->query("SELECT id FROM bookings WHERE flight_id = $flight_id AND seat_number = '$seat_number'");
        
        if ($seat_check && $seat_check->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Seat $seat_number is already booked. Please choose another seat.</div>";
        } else {
            $insert_booking = "INSERT INTO bookings (user_id, flight_id, seat_number, payment_status) 
                              VALUES ($user_id, $flight_id, '$seat_number', 'Paid')";
            
            if ($conn->query($insert_booking)) {
                $booking_id = $conn->insert_id;

                // Automatic baggage creation
                $conn->query("INSERT INTO baggage (user_id, booking_id, baggage_status, location) 
                              VALUES ($user_id, $booking_id, 'Checked-in', 'Check-in Desk')");

                header("Location: boarding_pass.php?booking_id=$booking_id");
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Booking failed: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<h2>Book Flight Ticket & Online Check-in</h2>
<?php echo $message; ?>

<div style="background: #f8fafc; padding: 15px; border-radius: 4px; margin-bottom: 20px; border-left: 4px solid #2563eb;">
    <p><strong>Flight:</strong> <?php echo htmlspecialchars($flight['flight_number']); ?></p>
    <p><strong>Route:</strong> <?php echo htmlspecialchars($flight['departure']); ?> → <?php echo htmlspecialchars($flight['destination']); ?></p>
    <p><strong>Departure Time:</strong> <?php echo htmlspecialchars($flight['departure_time']); ?></p>
</div>

<form id="bookingForm" method="POST" action="">
    <div class="form-grid">
        <div class="form-group">
            <label>Select Seat (Format: 12A, 4B, 18C)</label>
            <input type="text" name="seat_number" id="seat_number" required placeholder="e.g. 12A">
        </div>
        <div class="form-group">
            <label>Payment Method</label>
            <select required>
                <option value="card">Credit / Debit Card</option>
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
            </select>
        </div>
    </div>
    <button type="submit" class="btn">Pay & Complete Online Check-in</button>
</form>

<?php include 'footer.php'; ?>