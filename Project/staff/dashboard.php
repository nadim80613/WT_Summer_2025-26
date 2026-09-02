```php
<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

$user_id = (int) $_SESSION['user_id'];

$user_sql = "
    SELECT id, name, email, role
    FROM users
    WHERE id = '$user_id'
    LIMIT 1
";

$user_result = mysqli_query($conn, $user_sql);

if (!$user_result) {
    die("User query error: " . mysqli_error($conn));
}

$user_data = mysqli_fetch_assoc($user_result);

if (!$user_data) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

$staff_name = $user_data['name'];
$staff_role = $user_data['role'];

$staff_initials = strtoupper(substr($staff_name, 0, 2));

$total_flights_sql = "
    SELECT COUNT(*) AS total
    FROM flights
";

$total_flights_result = mysqli_query($conn, $total_flights_sql);

if (!$total_flights_result) {
    die("Total flights error: " . mysqli_error($conn));
}

$total_flights_data = mysqli_fetch_assoc($total_flights_result);
$total_flights = (int) $total_flights_data['total'];

$delayed_flights_sql = "
    SELECT COUNT(*) AS total
    FROM flights
    WHERE LOWER(status) = 'delayed'
";

$delayed_flights_result = mysqli_query($conn, $delayed_flights_sql);

if (!$delayed_flights_result) {
    die("Delayed flights error: " . mysqli_error($conn));
}

$delayed_flights_data = mysqli_fetch_assoc($delayed_flights_result);
$delayed_flights = (int) $delayed_flights_data['total'];

$passenger_sql = "
    SELECT COUNT(*) AS total
    FROM users
    WHERE LOWER(role) = 'passenger'
";

$passenger_result = mysqli_query($conn, $passenger_sql);

if (!$passenger_result) {
    die("Passenger query error: " . mysqli_error($conn));
}

$passenger_data = mysqli_fetch_assoc($passenger_result);
$total_passengers = (int) $passenger_data['total'];

$active_gates_sql = "
    SELECT COUNT(*) AS total
    FROM gates
    WHERE LOWER(TRIM(availability)) = 'occupied'
";

$active_gates_result = mysqli_query($conn, $active_gates_sql);

if (!$active_gates_result) {
    die("Active gates error: " . mysqli_error($conn));
}

$active_gates_data = mysqli_fetch_assoc($active_gates_result);
$active_gates = (int) $active_gates_data['total'];

$total_gates_sql = "
    SELECT COUNT(*) AS total
    FROM gates
";

$total_gates_result = mysqli_query($conn, $total_gates_sql);

if (!$total_gates_result) {
    die("Total gates error: " . mysqli_error($conn));
}

$total_gates_data = mysqli_fetch_assoc($total_gates_result);
$total_gates = (int) $total_gates_data['total'];

$flights_sql = "
    SELECT
        f.id,
        f.flight_number,
        f.departure,
        f.destination,
        f.departure_time,
        f.arrival_time,
        f.status,
        COALESCE(a.airline_name, 'N/A') AS airline,
        COALESCE(a.model, 'N/A') AS aircraft,
        (
            SELECT g.gate_number
            FROM gates g
            WHERE g.flight_id = f.id
            LIMIT 1
        ) AS gate_number
    FROM flights f
    LEFT JOIN airplanes a
        ON f.airplane_id = a.id
    ORDER BY f.departure_time ASC
    LIMIT 5
";

$flights_result = mysqli_query($conn, $flights_sql);

if (!$flights_result) {
    die("Flight query error: " . mysqli_error($conn));
}

$baggage_sql = "
    SELECT
        b.id,
        b.user_id,
        b.booking_id,
        b.baggage_status,
        b.location,
        b.updated_at,
        u.name AS passenger_name,
        f.flight_number
    FROM baggage b
    LEFT JOIN users u
        ON b.user_id = u.id
    LEFT JOIN bookings bk
        ON b.booking_id = bk.id
    LEFT JOIN flights f
        ON bk.flight_id = f.id
    ORDER BY b.updated_at DESC
    LIMIT 3
";

$baggage_result = mysqli_query($conn, $baggage_sql);

if (!$baggage_result) {
    die("Baggage query error: " . mysqli_error($conn));
}

$gates_sql = "
    SELECT
        id,
        gate_number,
        flight_id,
        availability
    FROM gates
    ORDER BY gate_number ASC
    LIMIT 8
";

$gates_result = mysqli_query($conn, $gates_sql);

if (!$gates_result) {
    die("Gate query error: " . mysqli_error($conn));
}

function getFlightStatusClass($status)
{
    switch (strtolower(trim($status))) {
        case 'boarding':
            return 'status-boarding';

        case 'delayed':
            return 'status-delayed';

        case 'arrived':
            return 'status-arrived';

        case 'departed':
            return 'status-departed';

        case 'cancelled':
            return 'status-cancelled';

        case 'on time':
        case 'scheduled':
        default:
            return 'status-ontime';
    }
}

function getBaggageStatusClass($status)
{
    switch (strtolower(trim($status))) {
        case 'loaded':
            return 'status-loaded';

        case 'in transit':
            return 'status-transit';

        case 'claimed':
            return 'status-claimed';

        case 'checked in':
        case 'checked-in':
        default:
            return 'status-checked';
    }
}

function getGateStatusClass($availability)
{
    $status = strtolower(trim($availability));

    switch ($status) {
        case 'occupied':
            return 'gate-occupied';

        case 'maintenance':
            return 'gate-maintenance';

        case 'available':
        default:
            return 'gate-available';
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Staff Dashboard - AeroPort</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<aside class="sidebar">

    <div class="sidebar-top">

        <div class="logo">

            <div class="logo-icon">
                ✈
            </div>

            <div>

                <h2>AeroPort</h2>

                <p>Management System</p>

            </div>

        </div>

        <div class="profile">

            <div class="avatar">

                <?php echo htmlspecialchars($staff_initials); ?>

            </div>

            <div>

                <h3>
                    <?php echo htmlspecialchars($staff_name); ?>
                </h3>

                <span>
                    <?php echo htmlspecialchars($staff_role); ?>
                </span>

            </div>

        </div>

        <div class="title">
            STAFF OPERATIONS
        </div>

        <nav>

            <a href="dashboard.php" class="menu active">
                ▦ Dashboard
            </a>

            <a href="flight_schedule.php" class="menu">
                📅 Flight Schedules
            </a>

            <a href="gate_assignment.php" class="menu">
                🛫 Gate & Terminal
            </a>

            <a href="baggage_status.php" class="menu">
                🧳 Baggage Handling
            </a>

        </nav>

    </div>

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

            <a href="../logout.php" class="sign-out">
                ↪ Sign Out
            </a>

        </p>

    </div>

</aside>

<main class="main">

    <header class="page-header">

        <h1>
            Operations Dashboard
        </h1>

        <p class="sub">

            <?php echo date("M d, Y"); ?>

            · Hazrat Shahjalal International Airport

        </p>

    </header>

    <section class="cards">

        <div class="card">

            <h4>
                FLIGHTS TODAY
            </h4>

            <h2>
                <?php echo $total_flights; ?>
            </h2>

            <span class="sub-alert danger">

                ↓ <?php echo $delayed_flights; ?> delayed

            </span>

        </div>

        <div class="card">

            <h4>
                PASSENGERS TODAY
            </h4>

            <h2>

                <?php echo number_format($total_passengers); ?>

            </h2>

            <span class="sub-alert success">
                Registered passengers
            </span>

        </div>

        <div class="card">

            <h4>
                ACTIVE GATES
            </h4>

            <h2>

                <?php echo $active_gates; ?>

                /

                <?php echo $total_gates; ?>

            </h2>

            <span class="sub-alert success">
                Live status
            </span>

        </div>

    </section>

    <section class="content">

        <div class="flight">

            <div class="section-header">

                <h2>
                    Today's Flight Operations
                </h2>

                <a href="flight_schedule.php" class="view-link">
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

                <?php if (mysqli_num_rows($flights_result) > 0): ?>

                    <?php while ($flight = mysqli_fetch_assoc($flights_result)): ?>

                        <tr>

                            <td>

                                <strong>
                                    <?php echo htmlspecialchars($flight['flight_number']); ?>
                                </strong>

                            </td>

                            <td>

                                <?php echo htmlspecialchars($flight['departure']); ?>

                                →

                                <?php echo htmlspecialchars($flight['destination']); ?>

                            </td>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    date(
                                        'd M Y, H:i',
                                        strtotime($flight['departure_time'])
                                    )
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    date(
                                        'd M Y, H:i',
                                        strtotime($flight['arrival_time'])
                                    )
                                );
                                ?>

                            </td>

                            <td>

                                <strong class="gate-text">

                                    <?php
                                    echo htmlspecialchars(
                                        $flight['gate_number'] ?? 'TBD'
                                    );
                                    ?>

                                </strong>

                            </td>

                            <td>

                                <span class="status <?php echo getFlightStatusClass($flight['status']); ?>">

                                    •

                                    <?php echo htmlspecialchars($flight['status']); ?>

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" style="text-align:center;">

                            No flights available.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

            <div class="section-header baggage-header">

                <h2>
                    Recent Baggage
                </h2>

                <a href="baggage_status.php" class="view-link">
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

                <?php if (mysqli_num_rows($baggage_result) > 0): ?>

                    <?php while ($bag = mysqli_fetch_assoc($baggage_result)): ?>

                        <tr>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    'BAG-' . $bag['id']
                                );
                                ?>

                            </td>

                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $bag['passenger_name'] ?? 'Unknown'
                                );
                                ?>

                            </td>

                            <td>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $bag['flight_number'] ?? 'N/A'
                                    );
                                    ?>

                                </strong>

                            </td>

                            <td>

                                <span class="status <?php echo getBaggageStatusClass($bag['baggage_status']); ?>">

                                    •

                                    <?php echo htmlspecialchars($bag['baggage_status']); ?>

                                </span>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="4" style="text-align:center;">

                            No baggage data available.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

        <aside class="right">

            <h2>
                Gate Status
            </h2>

            <div class="gates">

                <?php if (mysqli_num_rows($gates_result) > 0): ?>

                    <?php while ($gate = mysqli_fetch_assoc($gates_result)): ?>

                        <?php
                        $gate_class = getGateStatusClass(
                            $gate['availability']
                        );
                        ?>

                        <div class="gate-item">

                            <div class="gate-box <?php echo $gate_class; ?>">

                                <?php
                                echo htmlspecialchars(
                                    $gate['gate_number']
                                );
                                ?>

                            </div>

                            <span class="gate-status-text">

                                <?php
                                echo htmlspecialchars(
                                    ucfirst(
                                        strtolower(
                                            trim(
                                                $gate['availability']
                                            )
                                        )
                                    )
                                );
                                ?>

                            </span>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <p>
                        No gates available.
                    </p>

                <?php endif; ?>

            </div>

            <div class="gate-legend">

                <div class="legend-item">

                    <span class="legend-box occupied"></span>

                    <span>Occupied</span>

                </div>

                <div class="legend-item">

                    <span class="legend-box available"></span>

                    <span>Available</span>

                </div>

                <div class="legend-item">

                    <span class="legend-box maintenance"></span>

                    <span>Maintenance</span>

                </div>

            </div>

            <div class="manage-gates">

                <a href="gate_assignment.php" class="view-link">
                    Manage Gates →
                </a>

            </div>

            <div class="terminal-banner">

                ✈ Terminal Overview

            </div>

        </aside>

    </section>

</main>

<script src="../assets/js/dashboard.js"></script>

</body>

</html>
```
