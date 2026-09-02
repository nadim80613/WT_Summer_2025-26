<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$user_name = htmlspecialchars($_SESSION['user_name'] ?? 'Passenger');


$bookings_count = 0;
$b_res = $conn->query("SELECT COUNT(*) AS total FROM bookings b 
                       JOIN flights f ON b.flight_id = f.id 
                       WHERE b.user_id = $user_id AND f.departure_time > NOW()");
if ($b_res) {
    $bookings_count = (int)$b_res->fetch_assoc()['total'];
}


$baggage_count = 0;
$bg_res = $conn->query("SELECT COUNT(*) AS total FROM baggage WHERE user_id = $user_id");
if ($bg_res) {
    $baggage_count = (int)$bg_res->fetch_assoc()['total'];
}


$alerts_count = 0;
$al_res = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND status = 'Unread'");
if ($al_res) {
    $alerts_count = (int)$al_res->fetch_assoc()['total'];
}


$upcoming_flight = null;
$next_res = $conn->query("SELECT b.id AS booking_id, b.seat_number, f.flight_number, f.departure, f.destination, f.departure_time, f.status 
                          FROM bookings b 
                          JOIN flights f ON b.flight_id = f.id 
                          WHERE b.user_id = $user_id AND f.departure_time > NOW() 
                          ORDER BY f.departure_time ASC LIMIT 1");
if ($next_res && $next_res->num_rows > 0) {
    $upcoming_flight = $next_res->fetch_assoc();
}


$latest_bag = null;
$l_bg = $conn->query("SELECT bg.*, f.flight_number 
                      FROM baggage bg 
                      LEFT JOIN bookings b ON bg.booking_id = b.id 
                      LEFT JOIN flights f ON b.flight_id = f.id 
                      WHERE bg.user_id = $user_id 
                      ORDER BY bg.id DESC LIMIT 1");
if ($l_bg && $l_bg->num_rows > 0) {
    $latest_bag = $l_bg->fetch_assoc();
}
?>

<style>

.welcome-banner {
    background: #0284c7;
    color: #ffffff;
    border-radius: 12px;
    padding: 24px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}
.welcome-banner h2 { font-size: 22px; font-weight: 800; margin-bottom: 6px; }
.welcome-banner p { font-size: 13px; color: #e0f2fe; }
.terminal-status-card {
    background: rgba(15, 23, 42, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 10px 18px;
    border-radius: 8px;
    text-align: right;
}
.terminal-status-card strong { display: block; font-size: 14px; }
.terminal-status-card small { font-size: 11px; color: #cbd5e1; }


.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}
.stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
}
.stat-title {
    font-size: 11px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 8px;
}
.stat-val { font-size: 28px; font-weight: 900; color: #0f172a; }


.journey-section {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 25px;
}
.journey-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.journey-title { font-size: 16px; font-weight: 800; color: #0f172a; }
.journey-body {
    background: #f8fafc;
    border-radius: 10px;
    padding: 20px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #f1f5f9;
}
.airport-title { font-size: 26px; font-weight: 900; color: #0f172a; }
.flight-mid { text-align: center; }
.flight-mid strong { color: #0284c7; font-size: 14px; }
.journey-arrow { color: #94a3b8; font-weight: bold; letter-spacing: 4px; margin: 4px 0; }
.journey-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 18px;
    font-size: 13px;
    color: #475569;
}


.bottom-grid {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 20px;
}
.panel-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 22px 24px;
}
.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}
.panel-head h3 { font-size: 15px; font-weight: 800; color: #0f172a; }


.tracker-stepper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: relative;
    margin: 25px 10px;
}
.tracker-stepper::before {
    content: "";
    position: absolute;
    top: 14px;
    left: 10px;
    right: 10px;
    height: 2px;
    background: #e2e8f0;
    z-index: 1;
}
.step-item {
    position: relative;
    z-index: 2;
    background: #ffffff;
    text-align: center;
    padding: 0 4px;
}
.step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 800;
    margin: 0 auto 6px auto;
    border: 2px solid #cbd5e1;
}
.step-circle.active {
    background: #0284c7;
    color: #ffffff;
    border-color: #0284c7;
}
.step-label { font-size: 11px; font-weight: 600; color: #64748b; }

.live-location-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 12px;
    color: #334155;
    margin-top: 15px;
}


.quick-services-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.service-btn-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
    text-decoration: none;
    transition: all 0.2s;
    display: block;
}
.service-btn-box:hover {
    background: #ffffff;
    border-color: #0284c7;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.service-btn-box strong {
    display: block;
    color: #0f172a;
    font-size: 13px;
    margin-bottom: 2px;
}
.service-btn-box small {
    color: #64748b;
    font-size: 11px;
}
</style>


<div class="welcome-banner">
    <div>
        <h2>Welcome Back, <?php echo $user_name; ?></h2>
        <p>Manage your upcoming journeys, boarding passes, and live airport luggage tracking.</p>
    </div>
    <div class="terminal-status-card">
        <strong>Terminal 1 Active</strong>
        <small><?php echo date('D, d M Y'); ?></small>
    </div>
</div>


<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Upcoming Bookings</div>
        <div class="stat-val"><?php echo $bookings_count; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Tracked Baggage</div>
        <div class="stat-val"><?php echo $baggage_count; ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Unread Alerts</div>
        <div class="stat-val"><?php echo $alerts_count; ?></div>
    </div>
</div>

<div class="journey-section">
    <div class="journey-top">
        <span class="journey-title">Upcoming Journey</span>
        <?php if ($upcoming_flight): ?>
            <span class="badge"><?php echo htmlspecialchars($upcoming_flight['status']); ?> Flight</span>
        <?php endif; ?>
    </div>

    <?php if ($upcoming_flight): ?>
        <div class="journey-body">
            <div>
                <div class="airport-title"><?php echo htmlspecialchars($upcoming_flight['departure']); ?></div>
                <small style="color: #64748b; font-weight: 600;">ORIGIN AIRPORT</small>
            </div>
            <div class="flight-mid">
                <strong><?php echo htmlspecialchars($upcoming_flight['flight_number']); ?></strong>
                <div class="journey-arrow">------&gt;</div>
                <small style="color: #64748b;">Seat: <strong><?php echo htmlspecialchars($upcoming_flight['seat_number']); ?></strong></small>
            </div>
            <div style="text-align: right;">
                <div class="airport-title"><?php echo htmlspecialchars($upcoming_flight['destination']); ?></div>
                <small style="color: #64748b; font-weight: 600;">DESTINATION</small>
            </div>
        </div>

        <div class="journey-footer">
            <div>Departure Time: <strong><?php echo htmlspecialchars($upcoming_flight['departure_time']); ?></strong></div>
            <a href="boarding_pass.php?booking_id=<?php echo $upcoming_flight['booking_id']; ?>" class="btn">View Boarding Pass</a>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 25px; color: #64748b; font-size: 14px;">
            No upcoming scheduled flights. <a href="search_flights.php" style="color: #0284c7; font-weight: bold;">Book a flight</a>
        </div>
    <?php endif; ?>
</div>


<div class="bottom-grid">
    
   
    <div class="panel-card">
        <div class="panel-head">
            <h3>Live Baggage Status</h3>
            <a href="baggage.php" style="color: #0284c7; font-size: 12px; font-weight: 700; text-decoration: none;">Details &rarr;</a>
        </div>

        <?php if ($latest_bag): ?>
            <small style="color: #64748b; display: block; margin-bottom: 15px;">
                Tag: <strong>#BG-<?php echo $latest_bag['id']; ?></strong> | Flight: <strong><?php echo htmlspecialchars($latest_bag['flight_number'] ?? 'N/A'); ?></strong>
            </small>

            <div class="tracker-stepper">
                <div class="step-item">
                    <div class="step-circle active">1</div>
                    <div class="step-label">Check-In</div>
                </div>
                <div class="step-item">
                    <div class="step-circle active">2</div>
                    <div class="step-label">Security</div>
                </div>
                <div class="step-item">
                    <div class="step-circle active">3</div>
                    <div class="step-label">Loaded</div>
                </div>
                <div class="step-item">
                    <div class="step-circle">4</div>
                    <div class="step-label">Claim Area</div>
                </div>
            </div>

            <div class="live-location-badge">
                Current Location: <strong><?php echo htmlspecialchars($latest_bag['location']); ?> (<?php echo htmlspecialchars($latest_bag['baggage_status']); ?>)</strong>
            </div>
        <?php else: ?>
            <p style="color: #64748b; font-size: 13px; text-align: center; padding: 20px;">No baggage currently in transit.</p>
        <?php endif; ?>
    </div>

   
    <div class="panel-card">
        <div class="panel-head">
            <h3>Quick Services</h3>
        </div>

        <div class="quick-services-grid">
            <a href="search_flights.php" class="service-btn-box">
                <strong>Book Flights</strong>
                <small>Browse routes</small>
            </a>

            <a href="my_bookings.php" class="service-btn-box">
                <strong>My Trips</strong>
                <small>Manage seats</small>
            </a>

            <a href="lost_found.php" class="service-btn-box">
                <strong>Lost & Found</strong>
                <small>Report items</small>
            </a>

            <a href="notifications.php" class="service-btn-box">
                <strong>Alerts</strong>
                <small>Flight updates</small>
            </a>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>