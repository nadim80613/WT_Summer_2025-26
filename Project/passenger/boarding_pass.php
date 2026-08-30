<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$requested_booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

// Fetch all bookings for this user to build a ticket switcher
$all_bookings_res = $conn->query("SELECT b.id AS booking_id, b.seat_number, f.flight_number, f.departure, f.destination, f.departure_time 
                                  FROM bookings b
                                  JOIN flights f ON b.flight_id = f.id
                                  WHERE b.user_id = $user_id 
                                  ORDER BY b.id DESC");

$bookings_list = [];
if ($all_bookings_res) {
    while ($row = $all_bookings_res->fetch_assoc()) {
        $bookings_list[] = $row;
    }
}

// Select active booking
$active_booking_id = $requested_booking_id;
if ($active_booking_id === 0 && !empty($bookings_list)) {
    // If none specified in URL, pick the first available or unlocked one
    $active_booking_id = (int)$bookings_list[0]['booking_id'];
}

// Fetch details for the selected booking
$pass = null;
if ($active_booking_id > 0) {
    $sql = "SELECT b.*, f.flight_number, f.departure, f.destination, f.departure_time, f.arrival_time, u.name AS passenger_name 
            FROM bookings b
            JOIN flights f ON b.flight_id = f.id
            JOIN users u ON b.user_id = u.id
            WHERE b.user_id = $user_id AND b.id = $active_booking_id 
            LIMIT 1";
    $res = $conn->query($sql);
    if ($res && $res->num_rows > 0) {
        $pass = $res->fetch_assoc();
    }
}

// Check 1-hour unlock condition
$is_unlocked = false;
$unlock_time_str = "";
if ($pass) {
    $current_timestamp = time();
    $departure_timestamp = strtotime($pass['departure_time']);
    $unlock_timestamp = $departure_timestamp - 3600; // 1 hour prior
    
    $is_unlocked = ($current_timestamp >= $unlock_timestamp);
    $unlock_time_str = date('Y-m-d H:i:s', $unlock_timestamp);
}
?>

<style>
.ticket-selector-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 15px 20px;
    margin: 20px 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 820px;
    gap: 15px;
}
.ticket-wrapper { margin-top: 20px; max-width: 820px; }
.ticket-card { background: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08); display: flex; overflow: hidden; border: 1px solid #e2e8f0; position: relative; }
.ticket-main { flex: 2.3; padding: 24px 28px; border-right: 2px dashed #cbd5e1; }
.ticket-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 18px; }
.airline-title { font-size: 18px; font-weight: 800; color: #0284c7; }
.route-display { display: flex; align-items: center; justify-content: space-between; background: #f8fafc; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; }
.airport-code { font-size: 26px; font-weight: 900; color: #0f172a; }
.info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.info-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; }
.info-val { font-size: 14px; font-weight: 700; color: #1e293b; }
.ticket-stub { flex: 1; background: #f8fafc; padding: 24px 20px; display: flex; flex-direction: column; justify-content: space-between; text-align: center; }
.seat-highlight { background: #0284c7; color: #ffffff; padding: 12px; border-radius: 10px; margin: 15px 0; }
.seat-highlight strong { font-size: 26px; font-weight: 900; }
.barcode { height: 40px; background: repeating-linear-gradient(90deg, #1e293b, #1e293b 2px, transparent 2px, transparent 4px, #1e293b 4px, #1e293b 7px, transparent 7px, transparent 9px); border-radius: 4px; }
.locked-box { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 35px 25px; text-align: center; max-width: 820px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.03); }
.btn-print { margin-top: 18px; background: #0284c7; color: #ffffff; border: none; padding: 10px 20px; font-weight: 600; border-radius: 6px; cursor: pointer; }
@media print {
    .sidebar, .topbar, .ticket-selector-bar, .btn-print, h2 { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
}
</style>

<h2>Digital Boarding Pass</h2>

<?php if (!empty($bookings_list)): ?>
    <!-- Switcher to pick which booked flight pass to view -->
    <div class="ticket-selector-bar">
        <div>
            <strong style="font-size: 14px; color: #0f172a;">Select Booked Ticket:</strong>
            <small style="display: block; color: #64748b;">Switch between your active bookings</small>
        </div>
        <form method="GET" action="boarding_pass.php" style="display: flex; gap: 10px; align-items: center;">
            <select name="booking_id" onchange="this.form.submit()" style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600; font-size: 13px; outline: none; background: #f8fafc;">
                <?php foreach ($bookings_list as $b): 
                    $dep_ts = strtotime($b['departure_time']);
                    $unlocked = (time() >= ($dep_ts - 3600));
                    $status_tag = $unlocked ? "🟢 Unlocked" : "🔒 Locked";
                ?>
                    <option value="<?php echo $b['booking_id']; ?>" <?php echo ($b['booking_id'] == $active_booking_id) ? 'selected' : ''; ?>>
                        #<?php echo $b['booking_id']; ?>: <?php echo htmlspecialchars($b['flight_number']); ?> (<?php echo htmlspecialchars($b['departure']); ?> → <?php echo htmlspecialchars($b['destination']); ?>) [Seat: <?php echo htmlspecialchars($b['seat_number']); ?>] - <?php echo $status_tag; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($pass && $is_unlocked): ?>
        <!-- UNLOCKED PASS -->
        <div class="ticket-wrapper">
            <div class="ticket-card">
                <div class="ticket-main">
                    <div class="ticket-header">
                        <span class="airline-title">✈ AIRPORT BOARDING PASS</span>
                        <span class="badge badge-scheduled"><?php echo htmlspecialchars($pass['payment_status']); ?></span>
                    </div>

                    <div class="route-display">
                        <div>
                            <div class="airport-code"><?php echo htmlspecialchars($pass['departure']); ?></div>
                            <small style="color: #64748b; font-weight: 600;">ORIGIN</small>
                        </div>
                        <div style="font-size: 20px; color: #0284c7; font-weight: bold;">✈ ➔</div>
                        <div style="text-align: right;">
                            <div class="airport-code"><?php echo htmlspecialchars($pass['destination']); ?></div>
                            <small style="color: #64748b; font-weight: 600;">DESTINATION</small>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div>
                            <div class="info-label">Passenger</div>
                            <div class="info-val"><?php echo htmlspecialchars($pass['passenger_name']); ?></div>
                        </div>
                        <div>
                            <div class="info-label">Flight</div>
                            <div class="info-val"><?php echo htmlspecialchars($pass['flight_number']); ?></div>
                        </div>
                        <div>
                            <div class="info-label">Gate</div>
                            <div class="info-val">G-04</div>
                        </div>
                        <div>
                            <div class="info-label">Departure</div>
                            <div class="info-val"><?php echo htmlspecialchars($pass['departure_time']); ?></div>
                        </div>
                        <div>
                            <div class="info-label">Class</div>
                            <div class="info-val">Economy</div>
                        </div>
                        <div>
                            <div class="info-label">Booking Ref</div>
                            <div class="info-val">#BK-<?php echo $pass['id']; ?></div>
                        </div>
                    </div>
                </div>

                <div class="ticket-stub">
                    <div>
                        <h4 style="color: #0f172a; margin-bottom: 2px;">BOARDING PASS</h4>
                        <small style="color: #64748b; font-weight: bold;"><?php echo htmlspecialchars($pass['flight_number']); ?></small>
                    </div>

                    <div class="seat-highlight">
                        <span style="display:block; font-size: 11px;">SEAT</span>
                        <strong><?php echo htmlspecialchars($pass['seat_number']); ?></strong>
                    </div>

                    <div>
                        <div class="barcode"></div>
                        <small style="font-size: 10px; color: #94a3b8; letter-spacing: 2px;">*BK<?php echo $pass['id']; ?>-<?php echo $pass['seat_number']; ?>*</small>
                    </div>
                </div>
            </div>

            <button onclick="window.print()" class="btn-print">🖨 Print Boarding Pass</button>
        </div>

    <?php elseif ($pass && !$is_unlocked): ?>
        <!-- LOCKED PASS NOTICE -->
        <div class="locked-box">
            <div style="font-size: 44px; margin-bottom: 12px;">🔒</div>
            <h3 style="color: #0f172a; margin-bottom: 8px;">Boarding Pass Not Available Yet for Flight <?php echo htmlspecialchars($pass['flight_number']); ?></h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 15px;">
                As per airport policy, your digital boarding pass for Seat <strong><?php echo htmlspecialchars($pass['seat_number']); ?></strong> will unlock <strong>1 hour prior to departure</strong>.
            </p>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 20px; border-radius: 8px; display: inline-block; font-size: 13px; text-align: left;">
                <strong>Booking Ref:</strong> #BK-<?php echo $pass['id']; ?><br>
                <strong>Departure Time:</strong> <?php echo htmlspecialchars($pass['departure_time']); ?><br>
                <strong>Boarding Pass Unlocks At:</strong> <span style="color: #0284c7; font-weight: bold;"><?php echo htmlspecialchars($unlock_time_str); ?></span>
            </div>
            <p style="margin-top: 20px;">
                <a href="my_bookings.php" class="btn" style="background: #64748b;">View All Bookings</a>
            </p>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div style="background: #ffffff; padding: 25px; border-radius: 8px; margin-top: 20px; border: 1px solid #e2e8f0;">
        <p style="color: #64748b;">No flight tickets booked yet. <a href="search_flights.php" style="color: #0284c7; font-weight: bold;">Search & Book Flights here</a>.</p>
    </div>
<?php endif; ?>

<?php include 'footer.php'; ?>