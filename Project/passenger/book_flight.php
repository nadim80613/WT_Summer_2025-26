<?php
require_once '../config/database.php';
include 'header.php';

$flight_id = isset($_GET['flight_id']) ? (int)$_GET['flight_id'] : 0;
$user_id = (int)$_SESSION['user_id'];
$message = "";

// Handle Single Combined Booking Record for Multiple Seats
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats_str = trim($_POST['selected_seats'] ?? '');
    $flight_id = (int)$_POST['flight_id'];
    $card_number = trim($_POST['card_number'] ?? '');

    if (!empty($selected_seats_str) && $flight_id > 0 && !empty($card_number)) {
        $seats_array = array_filter(array_map('trim', explode(',', $selected_seats_str)));
        
        if (!empty($seats_array)) {
            $already_taken = [];

            // Duplicate Seat Check against existing bookings
            $existing_res = $conn->query("SELECT seat_number FROM bookings WHERE flight_id = $flight_id");
            if ($existing_res) {
                while ($r = $existing_res->fetch_assoc()) {
                    $booked_in_db = array_map('trim', explode(',', $r['seat_number']));
                    foreach ($seats_array as $st) {
                        if (in_array($st, $booked_in_db)) {
                            $already_taken[] = $st;
                        }
                    }
                }
            }

            if (!empty($already_taken)) {
                $taken_seats_str = implode(', ', array_unique($already_taken));
                $message = "<div style='color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 6px; margin-bottom: 15px;'>Seat(s) <strong>$taken_seats_str</strong> already booked! Please select other seats.</div>";
            } else {
                // Insert 1 Single Booking Record with all selected seats
                $clean_seats_str = $conn->real_escape_string(implode(', ', $seats_array));
                $ins = "INSERT INTO bookings (user_id, flight_id, seat_number, booking_date, payment_status) 
                        VALUES ($user_id, $flight_id, '$clean_seats_str', NOW(), 'Paid')";
                
                if ($conn->query($ins)) {
                    $count_seats = count($seats_array);
                    $conn->query("INSERT INTO notifications (user_id, message, type, status, created_at) 
                                 VALUES ($user_id, 'Payment Successful! Ticket confirmed for $count_seats seat(s): $clean_seats_str.', 'Payment', 'Unread', NOW())");

                    echo "<script>
                        window.onload = function() {
                            document.getElementById('successModal').style.display = 'flex';
                        };
                    </script>";
                } else {
                    $message = "<div style='color: #dc2626;'>Booking Failed: " . $conn->error . "</div>";
                }
            }
        }
    }
}

$flight = null;
$booked_seats = [];

if ($flight_id > 0) {
    $f_res = $conn->query("SELECT * FROM flights WHERE id = $flight_id");
    if ($f_res && $f_res->num_rows > 0) {
        $flight = $f_res->fetch_assoc();
        $b_res = $conn->query("SELECT seat_number FROM bookings WHERE flight_id = $flight_id");
        if ($b_res) {
            while ($r = $b_res->fetch_assoc()) {
                $seats_split = explode(',', $r['seat_number']);
                foreach ($seats_split as $s_item) {
                    $booked_seats[] = trim($s_item);
                }
            }
        }
    }
}
?>

<style>
.cabin-container {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 25px;
    max-width: 650px;
    margin-top: 20px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.03);
}
.legend-bar {
    display: flex;
    justify-content: center;
    gap: 20px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 15px;
    margin-bottom: 20px;
    font-size: 13px;
    font-weight: 500;
}
.legend-item { display: flex; align-items: center; gap: 8px; }
.legend-dot { width: 16px; height: 16px; border-radius: 4px; }
.dot-biz { background: #fef3c7; border: 1.5px solid #d97706; }
.dot-eco { background: #e0f2fe; border: 1.5px solid #0284c7; }
.dot-sel { background: #0284c7; border: 1.5px solid #0369a1; }
.dot-occ { background: #fee2e2; border: 1.5px solid #ef4444; }

.section-label {
    text-align: center;
    font-weight: 800;
    font-size: 12px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: #64748b;
    margin: 15px 0 10px 0;
    position: relative;
}
.section-label::before, .section-label::after {
    content: "";
    position: absolute;
    top: 50%;
    width: 22%;
    height: 1px;
    background: #e2e8f0;
}
.section-label::before { left: 0; }
.section-label::after { right: 0; }

.plane-cabin {
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
}
.seat-row { display: flex; align-items: center; gap: 8px; }
.row-number { width: 20px; font-weight: bold; font-size: 13px; color: #94a3b8; text-align: center; }
.aisle-gap { width: 32px; text-align: center; font-size: 10px; font-weight: bold; color: #cbd5e1; }

.seat {
    width: 42px;
    height: 42px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.seat-eco { background: #e0f2fe; border: 1.5px solid #0284c7; color: #0369a1; }
.seat-eco:hover:not(.disabled) { background: #bae6fd; transform: scale(1.08); }
.seat-biz { background: #fef3c7; border: 1.5px solid #d97706; color: #b45309; width: 48px; height: 48px; }
.seat-biz:hover:not(.disabled) { background: #fde68a; transform: scale(1.08); }
.seat.selected { background: #0284c7 !important; border-color: #0369a1 !important; color: #ffffff !important; box-shadow: 0 4px 8px rgba(2, 132, 199, 0.35); }
.seat.disabled { background: #fee2e2 !important; border-color: #fca5a5 !important; color: #ef4444 !important; cursor: not-allowed; opacity: 0.5; }

.fare-summary { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-top: 25px; }

/* MODAL STYLES (PAYMENT & SUCCESS) */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.payment-card-box {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 460px;
    padding: 28px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    position: relative;
    animation: slideUp 0.3s ease-out;
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; margin-bottom: 18px; }
.modal-header h3 { font-size: 18px; color: #0f172a; font-weight: 800; }
.close-btn { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; }

.card-ui-preview {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 12px;
    padding: 18px;
    color: #ffffff;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.card-ui-preview .chip { width: 36px; height: 26px; background: #fbbf24; border-radius: 4px; margin-bottom: 15px; }
.card-ui-preview .card-num { font-size: 16px; letter-spacing: 2px; font-family: monospace; }
.card-ui-preview .card-btm { display: flex; justify-content: space-between; margin-top: 15px; font-size: 11px; text-transform: uppercase; color: #94a3b8; }

.input-grp { margin-bottom: 14px; }
.input-grp label { display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 4px; text-transform: uppercase; }
.input-grp input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; background: #f8fafc; }
.input-grp input:focus { border-color: #0284c7; background: #fff; }

.success-card-box {
    background: #ffffff;
    border-radius: 16px;
    max-width: 400px;
    width: 90%;
    padding: 30px 24px;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
    animation: slideUp 0.3s ease-out;
}
.success-icon {
    width: 60px; height: 60px; background: #ecfdf5; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px; color: #059669; margin: 0 auto 16px auto;
    border: 2px solid #a7f3d0;
}
</style>

<h2>Select Your Seats</h2>
<?php echo $message; ?>

<?php if ($flight): ?>
    <div style="background: #ffffff; border: 1px solid #e2e8f0; padding: 18px 24px; border-radius: 10px; max-width: 650px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <strong style="font-size: 18px; color: #0f172a;"><?php echo htmlspecialchars($flight['flight_number']); ?></strong>
                <span style="color: #64748b; font-size: 14px; margin-left: 10px;"><?php echo htmlspecialchars($flight['departure']); ?> ➔ <?php echo htmlspecialchars($flight['destination']); ?></span>
            </div>
            <span class="badge"><?php echo htmlspecialchars($flight['status']); ?></span>
        </div>
        <small style="color: #64748b; display: block; margin-top: 6px;">Departure: <strong><?php echo htmlspecialchars($flight['departure_time']); ?></strong></small>
    </div>

    <div class="cabin-container">
        
        <div class="legend-bar">
            <div class="legend-item"><div class="legend-dot dot-biz"></div> Business</div>
            <div class="legend-item"><div class="legend-dot dot-eco"></div> Economy</div>
            <div class="legend-item"><div class="legend-dot dot-sel"></div> Selected</div>
            <div class="legend-item"><div class="legend-dot dot-occ"></div> Booked</div>
        </div>

        <div class="plane-cabin">
            
            <!-- BUSINESS CLASS ($250) -->
            <div class="section-label">👑 Business Class ($250/seat)</div>
            <?php foreach ([1, 2] as $r): ?>
                <div class="seat-row">
                    <span class="row-number"><?php echo $r; ?></span>
                    <div style="display: flex; gap: 8px;">
                        <?php foreach (['A', 'B'] as $c): 
                            $sc = $r . $c;
                            $is_occ = in_array($sc, $booked_seats);
                        ?>
                            <button type="button" 
                                    class="seat seat-biz <?php echo $is_occ ? 'disabled' : ''; ?>"
                                    <?php echo $is_occ ? 'disabled' : ''; ?>
                                    onclick="toggleSeat('<?php echo $sc; ?>', 250, this)">
                                <?php echo $sc; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="aisle-gap">AISLE</div>
                    <div style="display: flex; gap: 8px;">
                        <?php foreach (['C', 'D'] as $c): 
                            $sc = $r . $c;
                            $is_occ = in_array($sc, $booked_seats);
                        ?>
                            <button type="button" 
                                    class="seat seat-biz <?php echo $is_occ ? 'disabled' : ''; ?>"
                                    <?php echo $is_occ ? 'disabled' : ''; ?>
                                    onclick="toggleSeat('<?php echo $sc; ?>', 250, this)">
                                <?php echo $sc; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- ECONOMY CLASS ($120) -->
            <div class="section-label" style="margin-top: 25px;">✈ Economy Class ($120/seat)</div>
            <?php foreach ([3, 4, 5, 6, 7] as $r): ?>
                <div class="seat-row">
                    <span class="row-number"><?php echo $r; ?></span>
                    <div style="display: flex; gap: 6px;">
                        <?php foreach (['A', 'B', 'C'] as $c): 
                            $sc = $r . $c;
                            $is_occ = in_array($sc, $booked_seats);
                        ?>
                            <button type="button" 
                                    class="seat seat-eco <?php echo $is_occ ? 'disabled' : ''; ?>"
                                    <?php echo $is_occ ? 'disabled' : ''; ?>
                                    onclick="toggleSeat('<?php echo $sc; ?>', 120, this)">
                                <?php echo $sc; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="aisle-gap">|</div>
                    <div style="display: flex; gap: 6px;">
                        <?php foreach (['D', 'E', 'F'] as $c): 
                            $sc = $r . $c;
                            $is_occ = in_array($sc, $booked_seats);
                        ?>
                            <button type="button" 
                                    class="seat seat-eco <?php echo $is_occ ? 'disabled' : ''; ?>"
                                    <?php echo $is_occ ? 'disabled' : ''; ?>
                                    onclick="toggleSeat('<?php echo $sc; ?>', 120, this)">
                                <?php echo $sc; ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="fare-summary">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: #64748b; font-size: 14px;">Selected Seats:</span>
                <strong id="display_seat" style="color: #0284c7; font-size: 15px;">None selected</strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="color: #64748b; font-size: 14px;">Total Passengers:</span>
                <strong id="display_count" style="color: #0f172a; font-size: 14px;">0 Ticket(s)</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 10px;">
                <span style="font-weight: 700; color: #0f172a;">Total Fare:</span>
                <strong id="display_price" style="font-size: 18px; color: #059669;">$0.00</strong>
            </div>
        </div>

        <button type="button" id="open_pay_btn" onclick="openPaymentModal()" class="btn" disabled style="width: 100%; padding: 12px; margin-top: 15px; font-size: 15px; opacity: 0.6;">
            Proceed to Payment ($0.00)
        </button>

    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" class="modal-overlay">
        <div class="payment-card-box">
            <div class="modal-header">
                <h3>💳 Secure Checkout</h3>
                <button type="button" class="close-btn" onclick="closePaymentModal()">&times;</button>
            </div>

            <div class="card-ui-preview">
                <div class="chip"></div>
                <div class="card-num" id="card_preview_num">•••• •••• •••• ••••</div>
                <div class="card-btm">
                    <div>
                        <span style="display: block; font-size: 9px;">Card Holder</span>
                        <strong id="card_preview_name" style="color:#fff; font-size: 12px;"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'PASSENGER NAME'); ?></strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 9px;">Expires</span>
                        <strong id="card_preview_exp" style="color:#fff; font-size: 12px;">MM/YY</strong>
                    </div>
                </div>
            </div>

            <form method="POST" action="book_flight.php?flight_id=<?php echo $flight_id; ?>" onsubmit="return validatePaymentForm()">
                <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
                <input type="hidden" id="selected_seats_input" name="selected_seats" required>

                <div class="input-grp">
                    <label>Cardholder Name</label>
                    <input type="text" id="card_name" name="card_name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" placeholder="Full Name on Card" required oninput="document.getElementById('card_preview_name').innerText = this.value.toUpperCase() || 'PASSENGER NAME'">
                </div>

                <div class="input-grp">
                    <label>Card Number (16 Digits)</label>
                    <input type="text" id="card_number" name="card_number" maxlength="19" placeholder="4123 4567 8901 2345" required oninput="formatCardNumber(this)">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="input-grp">
                        <label>Expiry Date (MM/YY)</label>
                        <input type="text" id="card_exp" name="card_exp" maxlength="5" placeholder="MM/YY" required oninput="formatExp(this)">
                    </div>
                    <div class="input-grp">
                        <label>CVV / CVC (3 Digits)</label>
                        <input type="password" id="card_cvv" name="card_cvv" maxlength="3" placeholder="123" required>
                    </div>
                </div>

                <button type="submit" id="pay_now_btn" class="btn" style="width: 100%; padding: 12px; font-size: 15px; margin-top: 10px; background: #059669;">
                    Pay & Confirm Booking
                </button>
            </form>
        </div>
    </div>

    <!-- SUCCESS POPUP MODAL -->
    <div id="successModal" class="modal-overlay">
        <div class="success-card-box">
            <div class="success-icon">✓</div>
            <h3 style="font-size: 20px; color: #0f172a; margin-bottom: 8px;">Payment Successful!</h3>
            <p style="color: #64748b; font-size: 14px; margin-bottom: 20px;">
                Your flight ticket has been confirmed with your selected seats. You can view your bookings or print your boarding pass (1 hr prior to departure).
            </p>
            <a href="my_bookings.php" class="btn" style="width: 100%; display: block; padding: 12px; box-sizing: border-box; text-decoration: none;">
                Go to My Bookings
            </a>
        </div>
    </div>

    <script>
    let selectedSeatsMap = new Map();

    function toggleSeat(seatCode, farePrice, btn) {
        if (selectedSeatsMap.has(seatCode)) {
            selectedSeatsMap.delete(seatCode);
            btn.classList.remove('selected');
        } else {
            selectedSeatsMap.set(seatCode, farePrice);
            btn.classList.add('selected');
        }
        updateSummary();
    }

    function updateSummary() {
        const seatsArray = Array.from(selectedSeatsMap.keys());
        let totalPrice = 0;
        selectedSeatsMap.forEach(price => totalPrice += price);

        document.getElementById('selected_seats_input').value = seatsArray.join(',');

        const openPayBtn = document.getElementById('open_pay_btn');
        if (seatsArray.length > 0) {
            document.getElementById('display_seat').innerText = seatsArray.join(', ');
            document.getElementById('display_count').innerText = seatsArray.length + ' Seat(s)';
            document.getElementById('display_price').innerText = '$' + totalPrice + '.00';

            openPayBtn.disabled = false;
            openPayBtn.style.opacity = '1';
            openPayBtn.innerText = 'Proceed to Payment ($' + totalPrice + '.00)';
            document.getElementById('pay_now_btn').innerText = 'Pay $' + totalPrice + '.00 & Confirm';
        } else {
            document.getElementById('display_seat').innerText = 'None selected';
            document.getElementById('display_count').innerText = '0 Ticket(s)';
            document.getElementById('display_price').innerText = '$0.00';

            openPayBtn.disabled = true;
            openPayBtn.style.opacity = '0.6';
            openPayBtn.innerText = 'Proceed to Payment ($0.00)';
        }
    }

    function openPaymentModal() {
        document.getElementById('paymentModal').style.display = 'flex';
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').style.display = 'none';
    }

    function formatCardNumber(input) {
        let val = input.value.replace(/\D/g, '').substring(0, 16);
        let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
        input.value = formatted;
        document.getElementById('card_preview_num').innerText = formatted || '•••• •••• •••• ••••';
    }

    function formatExp(input) {
        let val = input.value.replace(/\D/g, '').substring(0, 4);
        if (val.length === 1 && val > '1') {
            val = '0' + val;
        }
        if (val.length >= 2) {
            let month = parseInt(val.substring(0, 2), 10);
            if (month > 12) {
                val = '12' + val.substring(2);
            } else if (month === 0) {
                val = '01' + val.substring(2);
            }
            val = val.substring(0, 2) + '/' + val.substring(2);
        }
        input.value = val;
        document.getElementById('card_preview_exp').innerText = val || 'MM/YY';
    }

    function validatePaymentForm() {
        const cardNum = document.getElementById('card_number').value.replace(/\s+/g, '');
        const expVal = document.getElementById('card_exp').value;
        const cvv = document.getElementById('card_cvv').value;

        if (cardNum.length < 16) {
            alert('Please enter a valid 16-digit card number.');
            return false;
        }
        if (expVal.length < 5) {
            alert('Please enter a complete expiry date (MM/YY).');
            return false;
        }
        const parts = expVal.split('/');
        const month = parseInt(parts[0], 10);
        const year = parseInt('20' + parts[1], 10);

        if (month < 1 || month > 12) {
            alert('Invalid month! Expiry month must be between 01 and 12.');
            return false;
        }
        const now = new Date();
        const currentYear = now.getFullYear();
        const currentMonth = now.getMonth() + 1;

        if (year < currentYear || (year === currentYear && month < currentMonth)) {
            alert('This card is expired! Please use a valid card.');
            return false;
        }
        if (cvv.length < 3) {
            alert('Please enter a 3-digit CVV.');
            return false;
        }
        return true;
    }
    </script>
<?php else: ?>
    <p style="margin-top: 20px;">Flight not found. <a href="search_flights.php">Browse flights</a>.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>