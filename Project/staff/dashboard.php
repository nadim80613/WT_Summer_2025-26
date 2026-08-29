
<?php
session_start();

/* =========================
   LOGIN CHECK
========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

/* =========================
   STAFF INFORMATION
========================= */
$staff_name = $_SESSION['name'] ?? 'Staff';
$staff_role = $_SESSION['role'] ?? 'Staff Manager';

/* =========================
   DASHBOARD STATISTICS
========================= */
$dashboard = [
    'total_flights'    => 24,
    'delayed_flights'  => 3,
    'total_passengers' => 4821,
    'active_gates'     => 18
];

/* =========================
   FLIGHT DATA
========================= */
$flights = [
    [
        'flight'     => 'BG-401',
        'route'      => 'DAC → DXB',
        'departure'  => '06:00',
        'arrival'    => '08:45',
        'gate'       => 'G14',
        'status'     => 'Boarding'
    ],
    [
        'flight'     => 'BG-219',
        'route'      => 'CGP → DAC',
        'departure'  => '07:30',
        'arrival'    => '08:10',
        'gate'       => 'D3',
        'status'     => 'On Time'
    ],
    [
        'flight'     => 'EK-583',
        'route'      => 'DAC → DXB',
        'departure'  => '09:30',
        'arrival'    => '13:45',
        'gate'       => 'B7',
        'status'     => 'Delayed'
    ],
    [
        'flight'     => 'SQ-447',
        'route'      => 'SIN → DAC',
        'departure'  => '11:00',
        'arrival'    => '17:15',
        'gate'       => 'G6',
        'status'     => 'Arrived'
    ],
    [
        'flight'     => 'QR-642',
        'route'      => 'DAC → DOH',
        'departure'  => '21:00',
        'arrival'    => '23:45',
        'gate'       => 'C12',
        'status'     => 'Scheduled'
    ]
];

/* =========================
   BAGGAGE DATA
========================= */
$baggages = [
    [
        'tag'       => 'DAC–2091847',
        'passenger' => 'Rahman Kabir',
        'flight'    => 'BG-401',
        'status'    => 'Loaded'
    ],
    [
        'tag'       => 'DAC–2083145',
        'passenger' => 'Ahmed Hossain',
        'flight'    => 'EK-583',
        'status'    => 'Checked-in'
    ],
    [
        'tag'       => 'CGP–1045923',
        'passenger' => 'Karim Molla',
        'flight'    => 'BG-219',
        'status'    => 'In Transit'
    ]
];

/* =========================
   GATE DATA
========================= */
$gates = [
    'G14',
    'G10',
    'B7',
    'D3',
    'C12',
    'G6',
    'A2',
    'F5'
];

/* =========================
   HELPER FUNCTIONS
========================= */

function getFlightStatusClass($status)
{
    switch ($status) {
        case 'Boarding':
            return 'status-boarding';

        case 'Delayed':
            return 'status-delayed';

        case 'Arrived':
            return 'status-arrived';

        case 'On Time':
        case 'Scheduled':
        default:
            return 'status-ontime';
    }
}

function getBaggageStatusClass($status)
{
    switch ($status) {
        case 'Loaded':
            return 'status-loaded';

        case 'In Transit':
            return 'status-transit';

        case 'Checked-in':
        default:
            return 'status-checked';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Staff Dashboard - AeroPort</title>

    <link rel="stylesheet"
          href="../assets/css/dashboard.css">

</head>

<body>

<!-- ==================================================
     SIDEBAR
================================================== -->

<aside class="sidebar">

    <div class="sidebar-top">

        <!-- Logo -->
        <div class="logo">

            <div class="logo-icon">
                ✈
            </div>

            <div>
                <h2>AeroPort</h2>
                <p>Management System</p>
            </div>

        </div>


        <!-- Staff Profile -->
        <div class="profile">

            <div class="avatar">
                <?php
                echo strtoupper(
                    substr($staff_name, 0, 2)
                );
                ?>
            </div>

            <div>

                <h3>
                    <?php
                    echo htmlspecialchars($staff_name);
                    ?>
                </h3>

                <span>
                    <?php
                    echo htmlspecialchars($staff_role);
                    ?>
                </span>

            </div>

        </div>


        <!-- Navigation Title -->
        <div class="title">
            STAFF OPERATIONS
        </div>


        <!-- Navigation -->
        <nav>

            <a href="dashboard.php"
               class="menu active">

                ▦ Dashboard

            </a>


            <a href="flight_schedule.php"
               class="menu">

                📅 Flight Schedules

            </a>


            <a href="gate_assignment.php"
               class="menu">

                🛫 Gate & Terminal

            </a>


            <a href="baggage_status.php"
               class="menu">

                🧳 Baggage Handling

            </a>

        </nav>

    </div>


    <!-- Sidebar Bottom -->
    <div class="sidebar-bottom">

        <p id="themeToggle">

            <span id="themeIcon">
                ☀️
            </span>

            <span id="themeText">
                Light Mode
            </span>

        </p>


        <p>

            <a href="../logout.php"
               class="sign-out">

                ↪ Sign Out

            </a>

        </p>

    </div>

</aside>


<!-- ==================================================
     MAIN CONTENT
================================================== -->

<main class="main">

    <!-- Page Heading -->

    <header class="page-header">

        <h1>
            Operations Dashboard
        </h1>

        <p class="sub">
            <?php echo date("M d, Y"); ?>
            · Hazrat Shahjalal International Airport
        </p>

    </header>


    <!-- ==================================================
         STATISTICS CARDS
    ================================================== -->

    <section class="cards">


        <!-- Flights -->
        <div class="card">

            <h4>
                FLIGHTS TODAY
            </h4>

            <h2>
                <?php
                echo $dashboard['total_flights'];
                ?>
            </h2>

            <span class="sub-alert danger">

                ↓
                <?php
                echo $dashboard['delayed_flights'];
                ?>
                delayed

            </span>

        </div>


        <!-- Passengers -->
        <div class="card">

            <h4>
                PASSENGERS TODAY
            </h4>

            <h2>
                <?php
                echo number_format(
                    $dashboard['total_passengers']
                );
                ?>
            </h2>

            <span class="sub-alert success">

                ↑ 8% vs yesterday

            </span>

        </div>


        <!-- Gates -->
        <div class="card">

            <h4>
                ACTIVE GATES
            </h4>

            <h2>

                <?php
                echo $dashboard['active_gates'];
                ?>
                / 24

            </h2>

            <span class="sub-alert success">

                Live status

            </span>

        </div>

    </section>


    <!-- ==================================================
         DASHBOARD CONTENT
    ================================================== -->

    <section class="content">


        <!-- ==================================================
             LEFT COLUMN
        ================================================== -->

        <div class="flight">


            <!-- Flight Operations -->

            <div class="section-header">

                <h2>
                    Today's Flight Operations
                </h2>

                <a href="flight_schedule.php"
                   class="view-link">

                    View all →

                </a>

            </div>


            <table>

                <thead>

                    <tr>

                        <th>Flight</th>
                        <th>Route</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Gate</th>
                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($flights as $flight): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $flight['flight']
                                    );
                                    ?>
                                </strong>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $flight['route']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $flight['departure']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $flight['arrival']
                                );
                                ?>
                            </td>


                            <td>

                                <strong class="gate-text">

                                    <?php
                                    echo htmlspecialchars(
                                        $flight['gate']
                                    );
                                    ?>

                                </strong>

                            </td>


                            <td>

                                <span class="status
                                    <?php
                                    echo getFlightStatusClass(
                                        $flight['status']
                                    );
                                    ?>">

                                    •

                                    <?php
                                    echo htmlspecialchars(
                                        $flight['status']
                                    );
                                    ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>


            <!-- ==================================================
                 BAGGAGE
            ================================================== -->

            <div class="section-header baggage-header">

                <h2>
                    Recent Baggage
                </h2>

                <a href="baggage_status.php"
                   class="view-link">

                    View all →

                </a>

            </div>


            <table>

                <thead>

                    <tr>

                        <th>Tag</th>
                        <th>Passenger</th>
                        <th>Flight</th>
                        <th>Status</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($baggages as $bag): ?>

                        <tr>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $bag['tag']
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $bag['passenger']
                                );
                                ?>
                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $bag['flight']
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <span class="status
                                    <?php
                                    echo getBaggageStatusClass(
                                        $bag['status']
                                    );
                                    ?>">

                                    •

                                    <?php
                                    echo htmlspecialchars(
                                        $bag['status']
                                    );
                                    ?>

                                </span>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>


        <!-- ==================================================
             RIGHT COLUMN
        ================================================== -->

        <aside class="right">

            <h2>
                Gate Status
            </h2>


            <!-- Gates -->

            <div class="gates">

                <?php foreach ($gates as $gate): ?>

                    <div class="gate-box">

                        <?php
                        echo htmlspecialchars($gate);
                        ?>

                    </div>

                <?php endforeach; ?>

            </div>


            <!-- Manage Gates -->

            <div class="manage-gates">

                <a href="gate_assignment.php"
                   class="view-link">

                    Manage Gates →

                </a>

            </div>


            <!-- Terminal -->

            <div class="terminal-banner">

                <span>
                    ✈ Terminal Overview
                </span>

            </div>

        </aside>

    </section>

</main>


<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script src="../assets/js/dashboard.js"></script>

</body>
</html>

