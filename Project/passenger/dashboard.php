<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

// 1. Fetch Metrics (Only Upcoming Bookings)
$b_cnt_res = $conn->query("SELECT COUNT(*) AS total 
                           FROM bookings b 
                           JOIN flights f ON b.flight_id = f.id 
                           WHERE b.user_id = $user_id 
                             AND f.departure_time > NOW()");
$b_cnt = ($b_cnt_res) ? ($b_cnt_res->fetch_assoc()['total'] ?? 0) : 0;

$bg_cnt = $conn->query("SELECT COUNT(*) AS total FROM baggage WHERE user_id = $user_id")->fetch_assoc()['total'] ?? 0;
$notif_cnt = $conn->query("SELECT COUNT(*) AS total FROM notifications WHERE user_id = $user_id AND status = 'Unread'")->fetch_assoc()['total'] ?? 0;

// 2. Fetch Next Upcoming Flight
$next_flight_query = "SELECT b.id AS booking_id, b.seat_number, f.flight_number, f.departure, f.destination, f.departure_time, f.arrival_time, f.status 
                      FROM bookings b
                      JOIN flights f ON b.flight_id = f.id
                      WHERE b.user_id = $user_id AND f.departure_time > NOW()
                      ORDER BY f.departure_time ASC LIMIT 1";
$next_flight_res = $conn->query($next_flight_query);
$next_flight = ($next_flight_res && $next_flight_res->num_rows > 0) ? $next_flight_res->fetch_assoc() : null;

// 3. Fetch Recent Baggage Status
$baggage_query = "SELECT bg.*, f.flight_number 
                  FROM baggage bg 
                  JOIN bookings b ON bg.booking_id = b.id 
                  JOIN flights f ON b.flight_id = f.id 
                  WHERE bg.user_id = $user_id 
                  ORDER BY bg.id DESC LIMIT 1";
$baggage_res = $conn->query($baggage_query);
$latest_baggage = ($baggage_res && $baggage_res->num_rows > 0) ? $baggage_res->fetch_assoc() : null;

// 4. Fetch Recent Notifications
$notifs_res = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY id DESC LIMIT 3");
?>

<style>
.dash-hero {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #0f172a 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #ffffff;
    margin-bottom: 24px;
    box-shadow: 0 10px 20px rgba(2, 132, 199, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.dash-hero h1 { font-size: 24px; font-weight: 800; margin-bottom: 6px; }
.dash-hero p { color: #e0f2fe; font-size: 14px; }
.hero-badge {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    padding: 10px 18px;
    border-radius: 10px;
    text-align: right;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 24px;
}
.prem-stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: transform 0.2s, box-shadow 0.2s;
}
.prem-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.06);
}
.stat-info span { font-size: 12px; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
.stat-info h3 { font-size: 28px; font-weight: 800; color: #0f172a; margin-top: 4px; }
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
}
.icon-blue { background: #e0f2fe; color: #0284c7; }
.icon-purple { background: #f3e8ff; color: #9333ea; }
.icon-amber { background: #fef3c7; color: #d97706; }

.flight-highlight-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}
.route-visual {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    border-radius: 12px;
    padding: 18px 25px;
    margin: 16px 0;
}
.code-box h2 { font-size: 28px; font-weight: 900; color: #0f172a; line-height: 1; }
.code-box small { color: #64748b; font-weight: 600; font-size: 11px; text-transform: uppercase; }

.dashboard-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 20px;
}
.panel-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.panel-title {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.track-step-bar {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin: 25px 0 15px 0;
}
.track-step-bar::before {
    content: '';
    position: absolute;
    top: 14px;
    left: 10px;
    right: 10px;
    height: 3px;
    background: #e2e8f0;
    z-index: 1;
}
.step-item {
    position: relative;
    z-index: 2;
    text-align: center;
    width: 70px;
}
.step-circle {
    width: 30px;
    height: 30px;
    background: #e2e8f0;
    color: #64748b;
    border-radius: 50%;
    margin: 0 auto 6px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
}
.step-item.active .step-circle {
    background: #0284c7;
    color: #ffffff;
    box-shadow: 0 0 0 4px #e0f2fe;
}
.step-label { font-size: 11px; font-weight: 600; color: #64748b; }

.actions-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}
.action-tile {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 16px;
    text-decoration: none;
    color: #0f172a;
    display: flex;
    flex-direction: column;
    gap: 6px;
    transition: 0.2s;
}
.action-tile:hover {
    background: #e0f2fe;
    border-color: #0284c7;
}
.action-tile span.icon { font-size: 20px; }
.action-tile strong { font-size: 13px; }
.action-tile small { color: #64748b; font-size: 11px; }
</style>

<!-- Hero Section -->
<div class="dash-hero">
    <div>
        <h1>Welcome Back, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Passenger'); ?> ✈</h1>
        <p>Manage your upcoming journeys, boarding passes, and live airport luggage tracking.</p>
    </div>
    <div class="hero-badge">
        <strong style="font-size: 15px; display: block;">Terminal 1 Active</strong>
        <small><?php echo date('D, d M Y'); ?></small>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="stats-container">
    <div class="prem-stat-card">
        <div class="stat-info">
            <span>UPCOMING BOOKINGS</span>
            <h3><?php echo $b_cnt; ?></h3>
        </div>
        <div class="stat-icon icon-blue">🎫</div>
    </div>
    <div class="prem-stat-card">
        <div class="stat-info">
            <span>TRACKED BAGGAGE</span>
            <h3><?php echo $bg_cnt; ?></h3>
        </div>
        <div class="stat-icon icon-purple">🧳</div>
    </div>
    <div class="prem-stat-card">
        <div class="stat-info">
            <span>UNREAD ALERTS</span>
            <h3><?php echo $notif_cnt; ?></h3>
        </div>
        <div class="stat-icon icon-amber">🔔</div>
    </div>
</div>

<!-- Highlighted Next Flight -->
<div class="flight-highlight-card">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; color: #0f172a; font-weight: 700;">Upcoming Journey</h3>
        <?php if ($next_flight): ?>
            <span class="badge" style="background: #e0f2fe; color: #0284c7;">Scheduled Flight</span>
        <?php endif; ?>
    </div>

    <?php if ($next_flight): ?>
        <div class="route-visual">
            <div class="code-box">
                <h2><?php echo htmlspecialchars($next_flight['departure']); ?></h2>
                <small>Origin Airport</small>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 13px; font-weight: 700; color: #0284c7;"><?php echo htmlspecialchars($next_flight['flight_number']); ?></div>
                <div style="font-size: 18px; color: #94a3b8; margin: 4px 0;">✈ ➔ ➔ ➔</div>
                <small style="color: #64748b; font-size: 11px;">Seat: <strong><?php echo htmlspecialchars($next_flight['seat_number']); ?></strong></small>
            </div>
            <div class="code-box" style="text-align: right;">
                <h2><?php echo htmlspecialchars($next_flight['destination']); ?></h2>
                <small>Destination</small>
            </div>
        </div>
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 12px;">
            <small style="color: #64748b;">Departure Time: <strong><?php echo htmlspecialchars($next_flight['departure_time']); ?></strong></small>
            <a href="boarding_pass.php?booking_id=<?php echo $next_flight['booking_id']; ?>" class="btn">View Boarding Pass</a>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 25px 0; color: #64748b;">
            <p>No scheduled flights right now.</p>
            <a href="search_flights.php" class="btn" style="margin-top: 10px;">Search & Book Flights</a>
        </div>
    <?php endif; ?>
</div>

<!-- 2-Column Details Section -->
<div class="dashboard-grid">
    
    <!-- Left: Baggage Live Progress -->
    <div class="panel-card">
        <div class="panel-title">
            <span>🧳 Live Baggage Status</span>
            <a href="baggage.php" style="font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 600;">Details ➔</a>
        </div>

        <?php if ($latest_baggage): ?>
            <p style="font-size: 13px; color: #64748b;">Tag: <strong>#BG-<?php echo $latest_baggage['id']; ?></strong> | Flight: <strong><?php echo htmlspecialchars($latest_baggage['flight_number']); ?></strong></p>
            
            <div class="track-step-bar">
                <div class="step-item active">
                    <div class="step-circle">✓</div>
                    <div class="step-label">Check-In</div>
                </div>
                <div class="step-item active">
                    <div class="step-circle">✓</div>
                    <div class="step-label">Security</div>
                </div>
                <div class="step-item active">
                    <div class="step-circle">✓</div>
                    <div class="step-label">Loaded</div>
                </div>
                <div class="step-item">
                    <div class="step-circle">4</div>
                    <div class="step-label">Claim Area</div>
                </div>
            </div>

            <div style="background: #f8fafc; padding: 10px 14px; border-radius: 8px; font-size: 12px; border: 1px solid #e2e8f0; margin-top: 12px;">
                <strong>Current Location:</strong> <?php echo htmlspecialchars($latest_baggage['location']); ?> (<?php echo htmlspecialchars($latest_baggage['baggage_status']); ?>)
            </div>
        <?php else: ?>
            <p style="font-size: 13px; color: #64748b; padding: 15px 0;">No active baggage checked-in.</p>
        <?php endif; ?>
    </div>

    <!-- Right: Quick Shortcuts -->
    <div class="panel-card">
        <div class="panel-title">
            <span>⚡ Quick Services</span>
        </div>

        <div class="actions-grid">
            <a href="search_flights.php" class="action-tile">
                <span class="icon">✈</span>
                <strong>Book Flights</strong>
                <small>Browse routes</small>
            </a>
            <a href="my_bookings.php" class="action-tile">
                <span class="icon">📋</span>
                <strong>My Trips</strong>
                <small>Manage seats</small>
            </a>
            <a href="lost_found.php" class="action-tile">
                <span class="icon">🔍</span>
                <strong>Lost & Found</strong>
                <small>Report items</small>
            </a>
            <a href="notifications.php" class="action-tile">
                <span class="icon">🔔</span>
                <strong>Alerts</strong>
                <small>Flight updates</small>
            </a>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>