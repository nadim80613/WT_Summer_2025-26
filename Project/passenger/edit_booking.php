<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;
$message = "";


$stmt = $conn->query("SELECT b.*, f.flight_number, f.departure, f.destination, f.departure_time 
                  FROM bookings b 
                      JOIN flights f ON b.flight_id = f.id 
                      WHERE b.id = $booking_id AND b.user_id = $user_id");

$booking = ($stmt && $stmt->num_rows > 0) ? $stmt->fetch_assoc() : null;

if (!$booking) {
    echo "<p>Booking not found. <a href='my_bookings.php'>Back to My Bookings</a></p>";
    include 'footer.php';
    exit();
}

$flight_id = (int)$booking['flight_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_seat = $conn->real_escape_string(trim($_POST['seat_number'] ?? ''));

    if (!empty($new_seat)) {
       
        $chk = $conn->query("SELECT id FROM bookings WHERE flight_id = $flight_id AND seat_number = '$new_seat' AND id != $booking_id");
        
        if ($chk && $chk->num_rows > 0) {
            $message = "<div style='color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 6px; margin-bottom: 15px;'>Seat $new_seat is already taken! Pick another one.</div>";
        } else {
            $conn->query("UPDATE bookings SET seat_number = '$new_seat' WHERE id = $booking_id");
            
            
            $conn->query("INSERT INTO notifications (user_id, message, type, status, created_at) 
                         VALUES ($user_id, 'Seat for booking #$booking_id updated to $new_seat.', 'Update', 'Unread', NOW())");

            header("Location: my_bookings.php?updated=success");
            exit();
        }
    }
}


$booked_seats = [];
$b_res = $conn->query("SELECT seat_number FROM bookings WHERE flight_id = $flight_id AND id != $booking_id");
if ($b_res) {
    while ($r = $b_res->fetch_assoc()) {
        $booked_seats[] = $r['seat_number'];
    }
}
?>

<style>
.seat-plan-box { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; max-width: 550px; margin-top: 15px; }
.cabin { display: flex; flex-direction: column; gap: 10px; align-items: center; margin: 20px 0; }
.seat-row { display: flex; gap: 8px; align-items: center; }
.seat-btn { width: 40px; height: 40px; border: 1.5px solid #0284c7; background: #e0f2fe; color: #0369a1; font-weight: bold; border-radius: 6px; cursor: pointer; transition: 0.2s; }
.seat-btn:hover:not(.disabled) { background: #bae6fd; }
.seat-btn.selected { background: #0284c7 !important; color: #fff !important; }
.seat-btn.disabled { background: #fee2e2; border-color: #ef4444; color: #dc2626; cursor: not-allowed; opacity: 0.6; }
</style>

<h2>Change Seat for Booking #<?php echo $booking['id']; ?></h2>
<?php echo $message; ?>

<div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; max-width: 550px; margin-top: 15px;">
    <p><strong>Flight:</strong> <?php echo htmlspecialchars($booking['flight_number']); ?></p>
    <p><strong>Route:</strong> <?php echo htmlspecialchars($booking['departure']); ?> → <?php echo htmlspecialchars($booking['destination']); ?></p>
    <p><strong>Current Seat:</strong> <span style="color: #0284c7; font-weight: bold;"><?php echo htmlspecialchars($booking['seat_number']); ?></span></p>
</div>

<div class="seat-plan-box">
    <h3 style="text-align: center; margin-bottom: 10px;">Select New Seat</h3>
    
    <div class="cabin">
        <?php foreach ([1, 2, 3, 4, 5, 6] as $r): ?>
            <div class="seat-row">
                <span style="width: 20px; font-weight: bold; color: #64748b;"><?php echo $r; ?></span>
                <?php foreach (['A', 'B', 'C', 'D'] as $c): 
                    $s_code = $r . $c;
                    $is_booked = in_array($s_code, $booked_seats);
                    $is_current = ($s_code === $booking['seat_number']);
                ?>
                    <button type="button" class="seat-btn <?php echo $is_booked ? 'disabled' : ''; ?> <?php echo $is_current ? 'selected' : ''; ?>"
                            <?php echo $is_booked ? 'disabled' : ''; ?>
                            onclick="pickSeat('<?php echo $s_code; ?>', this)">
                        <?php echo $s_code; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="edit_booking.php?booking_id=<?php echo $booking_id; ?>">
        <div style="margin-bottom: 15px;">
            <label style="font-size: 13px; font-weight: 600;">Selected New Seat:</label>
            <input type="text" id="seat_box" name="seat_number" value="<?php echo htmlspecialchars($booking['seat_number']); ?>" readonly required style="width: 100%; padding: 10px; text-align: center; font-weight: bold; margin-top: 5px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn" style="flex: 1; padding: 10px;">Update Seat</button>
            <a href="my_bookings.php" class="btn" style="background: #64748b; padding: 10px 20px;">Cancel</a>
        </div>
    </form>
</div>

<script>
function pickSeat(seat, btn) {
    document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('seat_box').value = seat;
}
</script>

<?php include 'footer.php'; ?>