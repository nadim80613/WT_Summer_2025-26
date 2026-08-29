```php
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

/* ==================================================
   STAFF INFORMATION
================================================== */

$user_id = $_SESSION['user_id'];

$user_sql = "SELECT name, role FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);
$user_data = mysqli_fetch_assoc($user_result);

$staff_name = $user_data['name'] ?? $_SESSION['name'] ?? 'Staff';
$staff_role = $user_data['role'] ?? $_SESSION['role'] ?? 'Staff';

$staff_initials = strtoupper(substr($staff_name, 0, 2));


/* ==================================================
   ASSIGN / CHANGE GATE
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_gate'])) {

    $flight_id = (int)$_POST['flight_id'];
    $gate_number = mysqli_real_escape_string(
        $conn,
        trim($_POST['gate_number'])
    );

    if ($flight_id > 0 && !empty($gate_number)) {

        /* Check gate */
        $gate_check_sql = "
            SELECT availability
            FROM gates
            WHERE gate_number = '$gate_number'
            LIMIT 1
        ";

        $gate_check_result = mysqli_query($conn, $gate_check_sql);
        $gate_data = mysqli_fetch_assoc($gate_check_result);

        if ($gate_data && $gate_data['availability'] === 'Available') {

            /* Remove previous gate from this flight */
            mysqli_query(
                $conn,
                "UPDATE gates
                 SET flight_id = NULL,
                     availability = 'Available'
                 WHERE flight_id = '$flight_id'"
            );

            /* Assign new gate */
            mysqli_query(
                $conn,
                "UPDATE gates
                 SET flight_id = '$flight_id',
                     availability = 'Occupied'
                 WHERE gate_number = '$gate_number'"
            );
        }

        header("Location: gate_assignment.php");
        exit();
    }
}


/* ==================================================
   CHANGE GATE STATUS
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {

    $gate_number = mysqli_real_escape_string(
        $conn,
        trim($_POST['gate_number'])
    );

    $new_status = mysqli_real_escape_string(
        $conn,
        trim($_POST['new_status'])
    );

    /* Only these statuses are allowed */
    $allowed_statuses = ['Available', 'Maintenance'];

    if (
        !empty($gate_number) &&
        in_array($new_status, $allowed_statuses)
    ) {

        /*
         * Do not allow changing an occupied gate
         * directly to maintenance/available.
         */
        $check_sql = "
            SELECT flight_id
            FROM gates
            WHERE gate_number = '$gate_number'
            LIMIT 1
        ";

        $check_result = mysqli_query($conn, $check_sql);
        $check_data = mysqli_fetch_assoc($check_result);

        if ($check_data && empty($check_data['flight_id'])) {

            mysqli_query(
                $conn,
                "UPDATE gates
                 SET availability = '$new_status'
                 WHERE gate_number = '$gate_number'"
            );
        }

        header("Location: gate_assignment.php");
        exit();
    }
}


/* ==================================================
   RELEASE GATE / UNASSIGN FLIGHT
================================================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['release_gate'])) {

    $gate_number = mysqli_real_escape_string(
        $conn,
        trim($_POST['gate_number'])
    );

    if (!empty($gate_number)) {

        mysqli_query(
            $conn,
            "UPDATE gates
             SET flight_id = NULL,
                 availability = 'Available'
             WHERE gate_number = '$gate_number'"
        );
    }

    header("Location: gate_assignment.php");
    exit();
}


/* ==================================================
   GATE STATISTICS
================================================== */

$stats_sql = "
    SELECT

        COUNT(*) AS total_gates,

        SUM(
            CASE
                WHEN availability = 'Available'
                THEN 1 ELSE 0
            END
        ) AS available_gates,

        SUM(
            CASE
                WHEN availability = 'Occupied'
                THEN 1 ELSE 0
            END
        ) AS occupied_gates,

        SUM(
            CASE
                WHEN availability = 'Maintenance'
                THEN 1 ELSE 0
            END
        ) AS maintenance_gates

    FROM gates
";

$stats_result = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_result);

$total_gates = $stats['total_gates'] ?? 0;
$available_gates = $stats['available_gates'] ?? 0;
$occupied_gates = $stats['occupied_gates'] ?? 0;
$maintenance_gates = $stats['maintenance_gates'] ?? 0;


/* ==================================================
   GET ALL GATES
================================================== */

$gates_sql = "
    SELECT

        g.id,
        g.gate_number,
        COALESCE(g.terminal, 'Terminal 1') AS terminal,
        g.availability,
        g.flight_id,

        f.flight_number,

        COALESCE(
            a.airline_name,
            'Airline'
        ) AS airline,

        f.departure,
        f.destination,

        DATE_FORMAT(
            f.departure_time,
            '%H:%i'
        ) AS dep_time

    FROM gates g

    LEFT JOIN flights f
        ON g.flight_id = f.id

    LEFT JOIN airplanes a
        ON f.airplane_id = a.id

    ORDER BY
        g.terminal ASC,
        g.id ASC
";

$gates_result = mysqli_query($conn, $gates_sql);

$gates_by_terminal = [];
$all_gates = [];

if ($gates_result) {

    while ($gate = mysqli_fetch_assoc($gates_result)) {

        $gates_by_terminal[$gate['terminal']][] = $gate;

        $all_gates[] = $gate;
    }
}


/* ==================================================
   GET UNASSIGNED FLIGHTS
================================================== */

$unassigned_sql = "
    SELECT

        f.id,
        f.flight_number,

        COALESCE(
            a.airline_name,
            'Airline'
        ) AS airline,

        f.departure,
        f.destination,

        DATE_FORMAT(
            f.departure_time,
            '%H:%i'
        ) AS flight_time,

        f.status

    FROM flights f

    LEFT JOIN airplanes a
        ON f.airplane_id = a.id

    LEFT JOIN gates g
        ON f.id = g.flight_id

    WHERE g.flight_id IS NULL

    ORDER BY f.departure_time ASC
";

$unassigned_result = mysqli_query($conn, $unassigned_sql);

$unassigned_flights = [];

if ($unassigned_result) {

    while ($flight = mysqli_fetch_assoc($unassigned_result)) {
        $unassigned_flights[] = $flight;
    }
}

$unassigned_count = count($unassigned_flights);


/* ==================================================
   HELPER FUNCTION
================================================== */

function getGateClass($availability)
{
    switch (strtolower($availability)) {

        case 'occupied':
            return 'gate-occupied';

        case 'maintenance':
            return 'gate-maintenance';

        default:
            return 'gate-available';
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Gate & Terminal - AeroPort</title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >

</head>

<body>

<div class="app-layout">


    <!-- ==================================================
         SIDEBAR
    ================================================== -->

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
                    <?= htmlspecialchars($staff_initials); ?>
                </div>

                <div>

                    <h3>
                        <?= htmlspecialchars($staff_name); ?>
                    </h3>

                    <span>
                        <?= htmlspecialchars($staff_role); ?>
                    </span>

                </div>

            </div>


            <div class="title">
                STAFF OPERATIONS
            </div>


            <nav>

                <a
                    href="dashboard.php"
                    class="menu"
                >
                    ▦ Dashboard
                </a>

                <a
                    href="flight_schedule.php"
                    class="menu"
                >
                    📅 Flight Schedules
                </a>

                <a
                    href="gate_assignment.php"
                    class="menu active"
                >
                    🛫 Gate & Terminal
                </a>

                <a
                    href="baggage_status.php"
                    class="menu"
                >
                    🧳 Baggage Handling
                </a>

            </nav>

        </div>


        <div class="sidebar-bottom">

            <p id="themeToggle">
                <span id="themeIcon">🌙</span>
                <span id="themeText">Dark Mode</span>
            </p>

            <p>
                <a
                    href="../logout.php"
                    class="sign-out"
                >
                    ↪ Sign Out
                </a>
            </p>

        </div>

    </aside>



    <!-- ==================================================
         MAIN
    ================================================== -->

    <main class="main">


        <!-- PAGE HEADER -->

        <header class="page-header">

            <h1>
                Gate & Terminal Assignment
            </h1>

            <p class="sub">
                Manage gates, assign flights, and track terminal availability
            </p>

        </header>



        <!-- ==================================================
             STATISTICS
        ================================================== -->

        <section class="gate-stat-cards">


            <div class="stat-card total">

                <h4>
                    Total Gates
                </h4>

                <h2>
                    <?= $total_gates; ?>
                </h2>

            </div>


            <div class="stat-card available">

                <h4>
                    Available
                </h4>

                <h2>
                    <?= $available_gates; ?>
                </h2>

            </div>


            <div class="stat-card occupied">

                <h4>
                    Occupied
                </h4>

                <h2>
                    <?= $occupied_gates; ?>
                </h2>

            </div>


            <div class="stat-card maintenance">

                <h4>
                    Maintenance
                </h4>

                <h2>
                    <?= $maintenance_gates; ?>
                </h2>

            </div>


        </section>



        <!-- ==================================================
             GATE MAP + UNASSIGNED FLIGHTS
        ================================================== -->

        <section class="gate-operations">


            <!-- GATE MAP -->

            <div class="terminal-map-card">

                <div class="section-title">

                    <div>
                        <h2>Terminal Gate Map</h2>

                        <p>
                            Click a gate to manage its status
                        </p>
                    </div>

                </div>


                <?php

                $terminals = [
                    'Terminal 1',
                    'Terminal 2',
                    'Terminal 3'
                ];

                foreach ($terminals as $terminal):

                    $terminal_gates =
                        $gates_by_terminal[$terminal] ?? [];

                ?>

                <div
                    class="terminal-group"
                    data-terminal="<?= htmlspecialchars($terminal); ?>"
                >

                    <h3>
                        <?= htmlspecialchars($terminal); ?>
                    </h3>


                    <div class="terminal-grid">

                        <?php foreach ($terminal_gates as $gate): ?>

                            <button
                                type="button"
                                class="gate-node <?= getGateClass($gate['availability']); ?>"
                                onclick="openGateModal(
                                    '<?= htmlspecialchars($gate['gate_number'], ENT_QUOTES); ?>',
                                    '<?= htmlspecialchars($gate['availability'], ENT_QUOTES); ?>',
                                    '<?= htmlspecialchars($gate['flight_number'] ?? '', ENT_QUOTES); ?>'
                                )"
                            >

                                <?= htmlspecialchars($gate['gate_number']); ?>

                            </button>

                        <?php endforeach; ?>

                    </div>

                </div>

                <?php endforeach; ?>


                <!-- LEGEND -->

                <div class="gate-legend">

                    <div class="legend-item">
                        <span class="legend-dot available-dot"></span>
                        Available
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot occupied-dot"></span>
                        Occupied
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot maintenance-dot"></span>
                        Maintenance
                    </div>

                </div>

            </div>



            <!-- UNASSIGNED FLIGHTS -->

            <aside class="unassigned-card">

                <div class="unassigned-header">

                    <div>
                        <h2>
                            Unassigned Flights
                        </h2>

                        <p>
                            Flights waiting for a gate
                        </p>
                    </div>

                    <span class="unassigned-count">
                        <?= $unassigned_count; ?>
                    </span>

                </div>


                <div class="unassigned-list">

                    <?php if ($unassigned_count > 0): ?>

                        <?php foreach ($unassigned_flights as $flight): ?>

                            <div class="unassigned-item">

                                <div class="flight-top">

                                    <strong>
                                        <?= htmlspecialchars(
                                            $flight['flight_number']
                                        ); ?>
                                    </strong>

                                    <span
                                        class="<?= strtolower($flight['status']) === 'departing'
                                            ? 'departing'
                                            : 'arriving'; ?>"
                                    >
                                        •
                                        <?= htmlspecialchars(
                                            $flight['status']
                                        ); ?>
                                    </span>

                                </div>


                                <p class="airline">
                                    <?= htmlspecialchars(
                                        $flight['airline']
                                    ); ?>
                                </p>


                                <p class="route">

                                    <strong>
                                        <?= htmlspecialchars(
                                            $flight['departure']
                                        ); ?>

                                        →

                                        <?= htmlspecialchars(
                                            $flight['destination']
                                        ); ?>
                                    </strong>

                                    ·

                                    <?= htmlspecialchars(
                                        $flight['flight_time']
                                    ); ?>

                                </p>


                                <button
                                    type="button"
                                    class="assign-button"
                                    onclick="openAssignFlight(
                                        '<?= $flight['id']; ?>',
                                        '<?= htmlspecialchars(
                                            $flight['flight_number'],
                                            ENT_QUOTES
                                        ); ?>'
                                    )"
                                >
                                    + Assign Gate
                                </button>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-message">
                            All flights have been assigned.
                        </div>

                    <?php endif; ?>

                </div>

            </aside>

        </section>



        <!-- ==================================================
             TERMINAL FILTER
        ================================================== -->

        <div class="terminal-tabs">

            <button
                class="terminal-tab active"
                data-terminal="all"
            >
                All Terminals
            </button>

            <button
                class="terminal-tab"
                data-terminal="Terminal 1"
            >
                Terminal 1
            </button>

            <button
                class="terminal-tab"
                data-terminal="Terminal 2"
            >
                Terminal 2
            </button>

            <button
                class="terminal-tab"
                data-terminal="Terminal 3"
            >
                Terminal 3
            </button>

        </div>



        <!-- ==================================================
             GATE TABLE
        ================================================== -->

        <div class="gate-table-card">

            <table class="gate-table">

                <thead>

                    <tr>

                        <th>GATE</th>
                        <th>TERMINAL</th>
                        <th>FLIGHT</th>
                        <th>AIRLINE</th>
                        <th>ROUTE</th>
                        <th>DEP</th>
                        <th>ACTION</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($all_gates as $gate): ?>

                    <tr
                        class="gate-row"
                        data-terminal="<?= htmlspecialchars(
                            $gate['terminal']
                        ); ?>"
                    >

                        <td>

                            <strong class="gate-code">
                                <?= htmlspecialchars(
                                    $gate['gate_number']
                                ); ?>
                            </strong>

                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $gate['terminal']
                            ); ?>
                        </td>


                        <td>

                            <?php if (!empty($gate['flight_number'])): ?>

                                <strong>
                                    <?= htmlspecialchars(
                                        $gate['flight_number']
                                    ); ?>
                                </strong>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php

                            if (
                                !empty($gate['flight_number']) &&
                                !empty($gate['airline'])
                            ) {
                                echo htmlspecialchars(
                                    $gate['airline']
                                );
                            } else {
                                echo '—';
                            }

                            ?>

                        </td>


                        <td>

                            <?php if (
                                !empty($gate['departure']) &&
                                !empty($gate['destination'])
                            ): ?>

                                <?= htmlspecialchars(
                                    $gate['departure']
                                ); ?>

                                →

                                <?= htmlspecialchars(
                                    $gate['destination']
                                ); ?>

                            <?php else: ?>

                                —

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= !empty($gate['dep_time'])
                                ? htmlspecialchars($gate['dep_time'])
                                : '—'; ?>

                        </td>


                        <td>

                            <?php if (!empty($gate['flight_id'])): ?>

                                <form
                                    method="POST"
                                    onsubmit="return confirm(
                                        'Release this flight from the gate?'
                                    );"
                                >

                                    <input
                                        type="hidden"
                                        name="gate_number"
                                        value="<?= htmlspecialchars(
                                            $gate['gate_number']
                                        ); ?>"
                                    >

                                    <button
                                        type="submit"
                                        name="release_gate"
                                        class="release-button"
                                    >
                                        Release
                                    </button>

                                </form>

                            <?php elseif (
                                $gate['availability'] === 'Maintenance'
                            ): ?>

                                <span class="maintenance-text">
                                    Maintenance
                                </span>

                            <?php else: ?>

                                <span class="available-text">
                                    Available
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </main>

</div>



<!-- ==================================================
     GATE STATUS MODAL
================================================== -->

<div
    id="gateModal"
    class="modal-overlay"
>

    <div class="modal-box">

        <div class="modal-header">

            <h2>
                Gate <span id="modalGateNumber"></span>
            </h2>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('gateModal')"
            >
                ×
            </button>

        </div>


        <p class="modal-current-status">

            Current Status:
            <strong id="modalGateStatus"></strong>

        </p>


        <div
            id="occupiedMessage"
            class="occupied-message"
        >
            This gate is currently assigned to
            <strong id="assignedFlight"></strong>.
            <br><br>
            Release the flight from the table below before changing the gate status.
        </div>


        <div
            id="statusButtons"
            class="status-buttons"
        >

            <form method="POST">

                <input
                    type="hidden"
                    name="gate_number"
                    id="statusGateNumber"
                >

                <button
                    type="submit"
                    name="change_status"
                    value="1"
                    class="status-change available-status"
                    onclick="setStatus('Available')"
                >
                    ● Available
                </button>

                <input
                    type="hidden"
                    name="new_status"
                    id="newStatus"
                >

            </form>


            <form method="POST">

                <input
                    type="hidden"
                    name="gate_number"
                    id="maintenanceGateNumber"
                >

                <input
                    type="hidden"
                    name="new_status"
                    value="Maintenance"
                >

                <button
                    type="submit"
                    name="change_status"
                    class="status-change maintenance-status"
                >
                    ● Maintenance
                </button>

            </form>

        </div>

    </div>

</div>



<!-- ==================================================
     ASSIGN FLIGHT MODAL
================================================== -->

<div
    id="assignModal"
    class="modal-overlay"
>

    <div class="modal-box">

        <div class="modal-header">

            <h2>
                Assign Gate
            </h2>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('assignModal')"
            >
                ×
            </button>

        </div>


        <p class="assign-flight-name">
            Flight:
            <strong id="assignFlightNumber"></strong>
        </p>


        <form method="POST">

            <input
                type="hidden"
                name="flight_id"
                id="assignFlightId"
            >


            <label>
                Select Available Gate
            </label>


            <select
                name="gate_number"
                class="gate-select"
                required
            >

                <option value="">
                    -- Select Gate --
                </option>

                <?php foreach ($all_gates as $gate): ?>

                    <?php if (
                        $gate['availability'] === 'Available'
                    ): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $gate['gate_number']
                            ); ?>"
                        >

                            <?= htmlspecialchars(
                                $gate['gate_number']
                            ); ?>

                            -
                            <?= htmlspecialchars(
                                $gate['terminal']
                            ); ?>

                        </option>

                    <?php endif; ?>

                <?php endforeach; ?>

            </select>


            <div class="modal-actions">

                <button
                    type="submit"
                    name="assign_gate"
                    class="modal-save"
                >
                    Assign Gate
                </button>

                <button
                    type="button"
                    class="modal-cancel"
                    onclick="closeModal('assignModal')"
                >
                    Cancel
                </button>

            </div>

        </form>

    </div>

</div>



<div class="help-btn">
    ?
</div>


<script src="../assets/js/dashboard.js"></script>

</body>
</html>

