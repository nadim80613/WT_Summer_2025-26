<?php
session_start();

// Comment these out temporarily so the page loads without logging in:

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: ../login.php");
    exit();
}


// Set fallback values for preview
$staffName = $_SESSION['name'] ?? 'Jem';
$staffInitials = "JR";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroPort - Operations Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body class="dark-theme">

    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon">✈</div>
                <div class="brand-text">
                    <h2>AeroPort</h2>
                    <span>Management System</span>
                </div>
            </div>

            <div class="user-card">
                <div class="user-avatar">JR</div>
                <div class="user-info">
                    <h4>Jemi Rahman</h4>
                    <span>Staff Manager</span>
                </div>
            </div>

            <div class="nav-section-title">STAFF OPERATIONS</div>
            <nav class="nav-menu">
                <a href="dashboard.php" class="nav-item active">
                    <span class="nav-icon">⊞</span> Dashboard
                    <span class="active-dot"></span>
                </a>
                <a href="flight_schedule.php" class="nav-item">
                    <span class="nav-icon">📅</span> Flight Schedules
                </a>
                <a href="gate_assignment.php" class="nav-item">
                    <span class="nav-icon">🛫</span> Gate & Terminal
                </a>
                <a href="baggage_status.php" class="nav-item">
                    <span class="nav-icon">🧳</span> Baggage Handling
                </a>
            </nav>

            <div class="sidebar-footer">
                <button id="themeToggle" class="footer-btn">
                    <span id="themeIcon">☀️</span> <span id="themeText">Light Mode</span>
                </button>
                <a href="../logout.php" class="footer-btn">
                    <span>↻</span> Sign Out
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="main-content">
            <header class="top-header">
                <h1>Operations Dashboard</h1>
                <p>Aug 29, 2024 · Hazrat Shahjalal International Airport</p>
            </header>

            <!-- Metrics Overview -->
            <section class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-title">FLIGHTS TODAY</span>
                        <h2 class="metric-value">24</h2>
                        <span class="metric-sub danger">↓ 3 delayed</span>
                    </div>
                    <div class="metric-icon-box">✈</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-title">PASSENGERS TODAY</span>
                        <h2 class="metric-value">4,821</h2>
                        <span class="metric-sub success">↑ ↑ 8% vs yesterday</span>
                    </div>
                    <div class="metric-icon-box">👥</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-title">ACTIVE GATES</span>
                        <h2 class="metric-value">18 / 24</h2>
                    </div>
                    <div class="metric-icon-box">🛬</div>
                </div>
            </section>

            <!-- Dashboard Split Content -->
            <div class="dashboard-split">
                <!-- Left Operations Column -->
                <div class="primary-col">
                    <!-- Flights Table -->
                    <div class="content-box">
                        <div class="box-header">
                            <h3>Today's Flight Operations</h3>
                            <a href="flight_schedule.php" class="view-all">View all →</a>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>FLIGHT</th>
                                    <th>ROUTE</th>
                                    <th>DEPARTURE</th>
                                    <th>ARRIVAL</th>
                                    <th>GATE</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>BG–401</strong></td>
                                    <td>DAC <span class="route-arrow">→</span> DXB</td>
                                    <td>06:00</td>
                                    <td>08:45</td>
                                    <td><strong class="gate-highlight">G14</strong></td>
                                    <td><span class="badge badge-boarding">• Boarding</span></td>
                                </tr>
                                <tr>
                                    <td><strong>BG–219</strong></td>
                                    <td>CGP <span class="route-arrow">→</span> DAC</td>
                                    <td>07:30</td>
                                    <td>08:10</td>
                                    <td><strong class="gate-highlight">D3</strong></td>
                                    <td><span class="badge badge-ontime">• On Time</span></td>
                                </tr>
                                <tr>
                                    <td><strong>EK–583</strong></td>
                                    <td>DAC <span class="route-arrow">→</span> DXB</td>
                                    <td>09:30</td>
                                    <td>13:45</td>
                                    <td><strong class="gate-highlight">B7</strong></td>
                                    <td><span class="badge badge-delayed">• Delayed</span></td>
                                </tr>
                                <tr>
                                    <td><strong>SQ–447</strong></td>
                                    <td>SIN <span class="route-arrow">→</span> DAC</td>
                                    <td>11:00</td>
                                    <td>17:15</td>
                                    <td><strong class="gate-highlight">G6</strong></td>
                                    <td><span class="badge badge-arrived">• Arrived</span></td>
                                </tr>
                                <tr>
                                    <td><strong>QR–642</strong></td>
                                    <td>DAC <span class="route-arrow">→</span> DOH</td>
                                    <td>21:00</td>
                                    <td>23:45</td>
                                    <td><strong class="gate-highlight">C12</strong></td>
                                    <td><span class="badge badge-scheduled">• Scheduled</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Baggage Table -->
                    <div class="content-box" style="margin-top: 24px;">
                        <div class="box-header">
                            <h3>Recent Baggage</h3>
                            <a href="baggage_status.php" class="view-all">View all →</a>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>TAG</th>
                                    <th>PASSENGER</th>
                                    <th>FLIGHT</th>
                                    <th>STATUS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>DAC–2091847</td>
                                    <td>Rahman Kabir</td>
                                    <td>BG–401</td>
                                    <td><span class="badge badge-loaded">• Loaded</span></td>
                                </tr>
                                <tr>
                                    <td>DAC–2083145</td>
                                    <td>Ahmed Hossain</td>
                                    <td>EK–583</td>
                                    <td><span class="badge badge-checked">• Checked-in</span></td>
                                </tr>
                                <tr>
                                    <td>CGP–1045923</td>
                                    <td>Karim Molla</td>
                                    <td>BG–219</td>
                                    <td><span class="badge badge-transit">• In Transit</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Quick Status Column -->
                <div class="secondary-col">
                    <div class="content-box">
                        <div class="box-header">
                            <h3>Gate Status</h3>
                        </div>
                        <div class="gate-status-grid">
                            <div class="gate-tag gate-active">G14</div>
                            <div class="gate-tag gate-idle">G10</div>
                            <div class="gate-tag gate-active">B7</div>
                            <div class="gate-tag gate-active">D3</div>
                            <div class="gate-tag gate-idle">C12</div>
                            <div class="gate-tag gate-active">G6</div>
                            <div class="gate-tag gate-alert">A2</div>
                            <div class="gate-tag gate-idle">F5</div>
                        </div>
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="gate_assignment.php" class="view-all">Manage Gates →</a>
                        </div>
                    </div>

                    <div class="terminal-banner">
                        <div class="terminal-overlay">
                            <span>✈ Terminal Overview</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/dashboard.js"></script>
</body>
</html>