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
        $check = $conn->query("SELECT id FROM bookings WHERE flight_id = $flight_id AND seat_number = '$seat_number'");
        if ($check && $check->num_rows > 0) {
            $message = "<div style='color: red; margin-bottom: 15px;'>Seat $seat_number is already booked!</div>";
        } else {
            $conn->query("INSERT INTO bookings (user_id, flight_id, seat_number, payment_status) VALUES ($user_id, $flight_id, '$seat_number', 'Paid')");
            $new_id = $conn->insert_id;
            header("Location: boarding_pass.php?booking_id=$new_id");
            exit();
        }
    }
}

$flight = $conn->query("SELECT * FROM flights WHERE id = $flight_id")->fetch_assoc();

$booked_seats = [];
$b_res = $conn->query("SELECT seat_number FROM bookings WHERE flight_id = $flight_id");
if ($b_res) {
    while ($r = $b_res->fetch_assoc()) {
        $booked_seats[] = $r['seat_number'];
    }
}
?>

<style>
.seat-plan { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin-top: 15px; }
.cabin { display: flex; flex-direction: column; gap: 8px; align-items: center; margin: 15px 0; }
.seat-row { display: flex; gap: 6px; }
.seat-btn { width: 38px; height: 38px; border: 1.5px solid #0284c7; background: #e0f2fe; color: #0284c7; font-weight: bold; border-radius: 4px; cursor: pointer; }
.seat-btn.selected { background: #0284c7 !important; color: #fff !important; }
.seat-btn.disabled { background: #fee2e2; border-color: #ef4444; color: #ef4444; cursor: not-allowed; }
</style>

<h2>Choose Your Seat - <?php echo htmlspecialchars($flight['flight_number'] ?? ''); ?></h2>
<?php echo $message; ?>

<div class="seat-plan">
    <p>Route: <strong><?php echo htmlspecialchars($flight['departure'] ?? ''); ?> → <?php echo htmlspecialchars($flight['destination'] ?? ''); ?></strong></p>
    
    <div class="cabin">
        <?php foreach ([1,2,3,4,5] as $r): ?>
            <div class="seat-row">
                <?php foreach (['A','B','C','D'] as $c): 
                    $s = $r.$c;
                    $is_booked = in_array($s, $booked_seats);
                ?>
                    <button type="button" class="seat-btn <?php echo $is_booked ? 'disabled' : ''; ?>" 
                            <?php echo $is_booked ? 'disabled' : ''; ?>
                            onclick="selectSeat('<?php echo $s; ?>', this)"><?php echo $s; ?></button>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="book_flight.php?flight_id=<?php echo $flight_id; ?>">
        <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
        <input type="text" id="seat_box" name="seat_number" readonly placeholder="Click a seat" required style="width: 100%; padding: 10px; margin-bottom: 12px; text-align: center; font-weight: bold;">
        <button type="submit" class="btn" style="width: 100%;">Confirm & Book</button>
    </form>
</div>

<script>
function selectSeat(code, el) {
    document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('seat_box').value = code;
}
</script>

<?php include 'footer.php'; ?>