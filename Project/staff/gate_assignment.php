<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";

$user_id = $_SESSION['user_id'];

$user_sql = "
    SELECT name, role
    FROM users
    WHERE id = '$user_id'
";

$user_result = mysqli_query($conn, $user_sql);
$user_data = mysqli_fetch_assoc($user_result);

$staff_name = $user_data['name'] ?? $_SESSION['name'] ?? 'Staff';
$staff_role = $user_data['role'] ?? $_SESSION['role'] ?? 'Staff';

$staff_initials = strtoupper(substr($staff_name, 0, 2));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_gate'])) {

    $gate_id = (int) $_POST['gate_id'];

    $status = mysqli_real_escape_string(
        $conn,
        $_POST['status']
    );

    $terminal = mysqli_real_escape_string(
        $conn,
        $_POST['terminal']
    );

    if ($gate_id > 0) {

        if (
            $status === 'Available' ||
            $status === 'Maintenance'
        ) {

            $update_sql = "
                UPDATE gates
                SET
                    availability = '$status',
                    terminal = '$terminal',
                    flight_id = NULL
                WHERE id = '$gate_id'
            ";

        } else {

            $update_sql = "
                UPDATE gates
                SET
                    availability = '$status',
                    terminal = '$terminal'
                WHERE id = '$gate_id'
            ";
        }

        mysqli_query($conn, $update_sql);
    }

    header("Location: gate_assignment.php");
    exit();
}

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['assign_flight'])
) {

    $flight_id = (int) $_POST['flight_id'];
    $gate_id = (int) $_POST['gate_id'];

    if (
        $flight_id > 0 &&
        $gate_id > 0
    ) {

        $check_gate_sql = "
            SELECT id
            FROM gates
            WHERE id = '$gate_id'
            AND availability = 'Available'
            LIMIT 1
        ";

        $check_gate_result = mysqli_query(
            $conn,
            $check_gate_sql
        );

        if (
            $check_gate_result &&
            mysqli_num_rows($check_gate_result) > 0
        ) {

            $check_flight_sql = "
                SELECT id
                FROM gates
                WHERE flight_id = '$flight_id'
                LIMIT 1
            ";

            $check_flight_result = mysqli_query(
                $conn,
                $check_flight_sql
            );

            if (
                !$check_flight_result ||
                mysqli_num_rows($check_flight_result) === 0
            ) {

                $assign_sql = "
                    UPDATE gates
                    SET
                        flight_id = '$flight_id',
                        availability = 'Occupied'
                    WHERE id = '$gate_id'
                ";

                mysqli_query(
                    $conn,
                    $assign_sql
                );
            }
        }
    }

    header("Location: gate_assignment.php");
    exit();
}

$stats_sql = "
    SELECT

        COUNT(*) AS total_gates,

        SUM(
            CASE
                WHEN availability = 'Available'
                THEN 1
                ELSE 0
            END
        ) AS available_gates,

        SUM(
            CASE
                WHEN availability = 'Occupied'
                THEN 1
                ELSE 0
            END
        ) AS occupied_gates,

        SUM(
            CASE
                WHEN availability = 'Maintenance'
                THEN 1
                ELSE 0
            END
        ) AS maintenance_gates

    FROM gates
";

$stats_result = mysqli_query(
    $conn,
    $stats_sql
);

$stats = mysqli_fetch_assoc(
    $stats_result
);

$total_gates = $stats['total_gates'] ?? 0;

$available_gates =
    $stats['available_gates'] ?? 0;

$occupied_gates =
    $stats['occupied_gates'] ?? 0;

$maintenance_gates =
    $stats['maintenance_gates'] ?? 0;

$gates_sql = "

    SELECT

        g.id,

        g.gate_number,

        COALESCE(
            g.terminal,
            'Terminal 1'
        ) AS terminal,

        COALESCE(
            g.availability,
            'Available'
        ) AS availability,

        g.flight_id,

        f.flight_number,

        f.departure,

        f.destination,

        COALESCE(
            a.airline_name,
            ''
        ) AS airline,

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

$gates_result = mysqli_query(
    $conn,
    $gates_sql
);

$gates = [];

if ($gates_result) {

    while (
        $row = mysqli_fetch_assoc(
            $gates_result
        )
    ) {

        $gates[] = $row;
    }
}

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

    WHERE g.id IS NULL

    ORDER BY
        f.departure_time ASC
";

$unassigned_result = mysqli_query(
    $conn,
    $unassigned_sql
);

$unassigned_flights = [];

if ($unassigned_result) {

    while (
        $row = mysqli_fetch_assoc(
            $unassigned_result
        )
    ) {

        $unassigned_flights[] = $row;
    }
}

$unassigned_count =
    count($unassigned_flights);

$available_gate_sql = "

    SELECT

        id,

        gate_number,

        terminal

    FROM gates

    WHERE availability = 'Available'

    ORDER BY
        terminal ASC,
        id ASC
";

$available_gate_result = mysqli_query(
    $conn,
    $available_gate_sql
);

$available_gate_list = [];

if ($available_gate_result) {

    while (
        $row = mysqli_fetch_assoc(
            $available_gate_result
        )
    ) {

        $available_gate_list[] = $row;
    }
}

function getGateClass($status)
{

    if ($status === 'Occupied') {
        return 'gate-occupied';
    }

    if ($status === 'Maintenance') {
        return 'gate-maintenance';
    }

    return 'gate-available';
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

    <title>
        Gate & Terminal - AeroPort
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >

</head>

<body>

<div class="app-layout">

    <aside class="sidebar">

        <div class="sidebar-top">

            <div class="logo">

                <div class="logo-icon">
                    ✈
                </div>

                <div>

                    <h2>
                        AeroPort
                    </h2>

                    <p>
                        Management System
                    </p>

                </div>

            </div>

            <div class="profile">

                <div class="avatar">

                    <?= htmlspecialchars(
                        $staff_initials
                    ); ?>

                </div>

                <div>

                    <h3>
                        <?= htmlspecialchars(
                            $staff_name
                        ); ?>
                    </h3>

                    <span>
                        <?= htmlspecialchars(
                            $staff_role
                        ); ?>
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

            <p
                id="themeToggle"
                class="theme-toggle"
            >

                <span id="themeIcon">
                    🌙
                </span>

                <span id="themeText">
                    Night Mode
                </span>

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

    <main class="main">

        <div class="page-header">

            <h1>
                Gate & Terminal Assignment
            </h1>

            <p class="sub">
                Manage gate status, terminals and flight assignments
            </p>

        </div>

        <section class="gate-stat-cards">

            <div class="stat-card">

                <h4>
                    Total Gates
                </h4>

                <h2 class="text-blue">
                    <?= $total_gates; ?>
                </h2>

            </div>

            <div class="stat-card">

                <h4>
                    Available
                </h4>

                <h2 class="text-green">
                    <?= $available_gates; ?>
                </h2>

            </div>

            <div class="stat-card">

                <h4>
                    Occupied
                </h4>

                <h2 class="text-red">
                    <?= $occupied_gates; ?>
                </h2>

            </div>

            <div class="stat-card">

                <h4>
                    Maintenance
                </h4>

                <h2 class="text-amber">
                    <?= $maintenance_gates; ?>
                </h2>

            </div>

        </section>

        <section class="terminal-map-card">

            <div class="section-header">

                <div>

                    <h2>
                        Terminal Gate Map
                    </h2>

                    <p class="section-sub">
                        Click a gate to change its status or terminal
                    </p>

                </div>

            </div>

            <?php

            $terminals = [
                'Terminal 1',
                'Terminal 2',
                'Terminal 3'
            ];

            foreach (
                $terminals
                as $terminal
            ):

            ?>

            <div class="terminal-group">

                <h3>
                    <?= htmlspecialchars(
                        $terminal
                    ); ?>
                </h3>

                <div class="terminal-grid">

                    <?php foreach (
                        $gates
                        as $gate
                    ): ?>

                        <?php

                        if (
                            $gate['terminal']
                            !==
                            $terminal
                        ) {

                            continue;
                        }

                        $gate_class =
                            getGateClass(
                                $gate['availability']
                            );

                        ?>

                        <div
                            class="gate-item"
                            onclick='openGateModal(
                                <?= (int)$gate["id"]; ?>,
                                <?= json_encode($gate["gate_number"]); ?>,
                                <?= json_encode($gate["availability"]); ?>,
                                <?= json_encode($gate["terminal"]); ?>
                            )'
                        >

                            <div
                                class="gate-box
                                <?= $gate_class; ?>"
                            >

                                <?= htmlspecialchars(
                                    $gate['gate_number']
                                ); ?>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <?php endforeach; ?>

            <div class="gate-legend">

                <div class="legend-item">

                    <span
                        class="legend-circle available"
                    ></span>

                    Available

                </div>

                <div class="legend-item">

                    <span
                        class="legend-circle occupied"
                    ></span>

                    Occupied

                </div>

                <div class="legend-item">

                    <span
                        class="legend-circle maintenance"
                    ></span>

                    Maintenance

                </div>

            </div>

        </section>

        <section class="unassigned-section">

            <div class="section-header">

                <div>

                    <h2>
                        Unassigned Flights
                    </h2>

                    <p class="section-sub">
                        Flights that do not currently have a gate
                    </p>

                </div>

                <span class="unassigned-count">

                    <?= $unassigned_count; ?>

                </span>

            </div>

            <div class="unassigned-list">

                <?php if (
                    $unassigned_count === 0
                ): ?>

                    <div class="empty-message">

                        No unassigned flights.

                    </div>

                <?php else: ?>

                    <?php foreach (
                        $unassigned_flights
                        as $flight
                    ): ?>

                        <div
                            class="unassigned-item"
                        >

                            <div class="flight-info">

                                <div>

                                    <strong>

                                        <?= htmlspecialchars(
                                            $flight[
                                                'flight_number'
                                            ]
                                        ); ?>

                                    </strong>

                                    <p>

                                        <?= htmlspecialchars(
                                            $flight[
                                                'airline'
                                            ]
                                        ); ?>

                                    </p>

                                </div>

                                <span
                                    class="flight-status"
                                >

                                    <?= htmlspecialchars(
                                        $flight[
                                            'status'
                                        ]
                                    ); ?>

                                </span>

                            </div>

                            <div
                                class="flight-route"
                            >

                                <strong>

                                    <?= htmlspecialchars(
                                        $flight[
                                            'departure'
                                        ]
                                    ); ?>

                                    →

                                    <?= htmlspecialchars(
                                        $flight[
                                            'destination'
                                        ]
                                    ); ?>

                                </strong>

                                <span>

                                    <?= htmlspecialchars(
                                        $flight[
                                            'flight_time'
                                        ]
                                    ); ?>

                                </span>

                            </div>

                            <button
                                type="button"
                                class="btn-primary"
                                onclick='openAssignFlightModal(
                                    <?= (int)$flight["id"]; ?>,
                                    <?= json_encode($flight["flight_number"]); ?>
                                )'
                            >

                                + Assign Gate

                            </button>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </section>

        <section class="gate-overview">

            <div class="section-header">

                <div>

                    <h2>
                        Gate Overview
                    </h2>

                    <p class="section-sub">
                        Current gate and terminal information
                    </p>

                </div>

            </div>

            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                GATE
                            </th>

                            <th>
                                TERMINAL
                            </th>

                            <th>
                                STATUS
                            </th>

                            <th>
                                FLIGHT
                            </th>

                            <th>
                                AIRLINE
                            </th>

                            <th>
                                ROUTE
                            </th>

                            <th>
                                DEP
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach (
                        $gates
                        as $gate
                    ): ?>

                        <tr>

                            <td>

                                <strong class="gate-code">

                                    <?= htmlspecialchars(
                                        $gate[
                                            'gate_number'
                                        ]
                                    ); ?>

                                </strong>

                            </td>

                            <td>

                                <?= htmlspecialchars(
                                    $gate[
                                        'terminal'
                                    ]
                                ); ?>

                            </td>

                            <td>

                                <span
                                    class="table-status
                                    <?= getGateClass(
                                        $gate[
                                            'availability'
                                        ]
                                    ); ?>"
                                >

                                    <?= htmlspecialchars(
                                        $gate[
                                            'availability'
                                        ]
                                    ); ?>

                                </span>

                            </td>

                            <td>

                                <?php if (
                                    !empty(
                                        $gate[
                                            'flight_number'
                                        ]
                                    )
                                ): ?>

                                    <?= htmlspecialchars(
                                        $gate[
                                            'flight_number'
                                        ]
                                    ); ?>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    !empty(
                                        $gate[
                                            'airline'
                                        ]
                                    )
                                ): ?>

                                    <?= htmlspecialchars(
                                        $gate[
                                            'airline'
                                        ]
                                    ); ?>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    !empty(
                                        $gate[
                                            'departure'
                                        ]
                                    )
                                    &&
                                    !empty(
                                        $gate[
                                            'destination'
                                        ]
                                    )
                                ): ?>

                                    <?= htmlspecialchars(
                                        $gate[
                                            'departure'
                                        ]
                                    ); ?>

                                    →

                                    <?= htmlspecialchars(
                                        $gate[
                                            'destination'
                                        ]
                                    ); ?>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if (
                                    !empty(
                                        $gate[
                                            'dep_time'
                                        ]
                                    )
                                ): ?>

                                    <?= htmlspecialchars(
                                        $gate[
                                            'dep_time'
                                        ]
                                    ); ?>

                                <?php else: ?>

                                    —

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>

</div>

<div
    id="gateModal"
    class="modal-backdrop"
>

    <div class="modal-card">

        <div class="modal-header">

            <div>

                <h2>
                    Change Gate
                </h2>

                <p>

                    Gate

                    <strong
                        id="modalGateNumber"
                    ></strong>

                </p>

            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeGateModal()"
            >

                ×

            </button>

        </div>

        <form
            method="POST"
            action=""
        >

            <input
                type="hidden"
                name="gate_id"
                id="modalGateId"
            >

            <div class="modal-group">

                <label>
                    Gate Status
                </label>

                <select
                    name="status"
                    id="modalStatus"
                    required
                >

                    <option value="Available">
                        Available
                    </option>

                    <option value="Occupied">
                        Occupied
                    </option>

                    <option value="Maintenance">
                        Maintenance
                    </option>

                </select>

            </div>

            <div class="modal-group">

                <label>
                    Terminal
                </label>

                <select
                    name="terminal"
                    id="modalTerminal"
                    required
                >

                    <option value="Terminal 1">
                        Terminal 1
                    </option>

                    <option value="Terminal 2">
                        Terminal 2
                    </option>

                    <option value="Terminal 3">
                        Terminal 3
                    </option>

                </select>

            </div>

            <div class="modal-actions">

                <button
                    type="submit"
                    name="update_gate"
                    class="btn-save"
                >

                    Save Changes

                </button>

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="closeGateModal()"
                >

                    Cancel

                </button>

            </div>

        </form>

    </div>

</div>

<div
    id="assignFlightModal"
    class="modal-backdrop"
>

    <div class="modal-card">

        <div class="modal-header">

            <div>

                <h2>
                    Assign Gate
                </h2>

                <p>

                    Flight:

                    <strong
                        id="assignFlightNumber"
                    ></strong>

                </p>

            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeAssignFlightModal()"
            >

                ×

            </button>

        </div>

        <form
            method="POST"
            action=""
        >

            <input
                type="hidden"
                name="flight_id"
                id="assignFlightId"
            >

            <div class="modal-group">

                <label>
                    Select Available Gate
                </label>

                <select
                    name="gate_id"
                    required
                >

                    <option value="">
                        -- Select Gate --
                    </option>

                    <?php foreach (
                        $available_gate_list
                        as $available_gate
                    ): ?>

                        <option
                            value="<?= (int)$available_gate['id']; ?>"
                        >

                            <?= htmlspecialchars(
                                $available_gate[
                                    'gate_number'
                                ]
                            ); ?>

                            -

                            <?= htmlspecialchars(
                                $available_gate[
                                    'terminal'
                                ]
                            ); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="modal-actions">

                <button
                    type="submit"
                    name="assign_flight"
                    class="btn-save"
                >

                    Assign Gate

                </button>

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="closeAssignFlightModal()"
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