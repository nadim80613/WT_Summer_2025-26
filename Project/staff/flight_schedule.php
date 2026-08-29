<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include "../config/database.php";


$user_id = $_SESSION['user_id'];


/* Get Staff Information */

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


/* Get Flight Schedules */

$flights_sql = "
    SELECT
        f.id,
        f.flight_number,

        COALESCE(
            a.airline_name,
            'N/A'
        ) AS airline,

        COALESCE(
            a.model,
            'N/A'
        ) AS aircraft,

        f.departure,
        f.destination,
        f.departure_time,
        f.arrival_time,
        f.status,

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
";


$flights_result =
    mysqli_query($conn, $flights_sql);


$total_flights =
    mysqli_num_rows($flights_result);


/* Status Badge */

function getScheduleBadgeClass($status)
{
    switch (strtolower(trim($status))) {

        case 'boarding':
            return 'status-boarding';

        case 'delayed':
            return 'status-delayed';

        case 'departed':
            return 'status-departed';

        case 'arrived':
            return 'status-arrived';

        case 'cancelled':
            return 'status-cancelled';

        case 'on time':
        default:
            return 'status-scheduled';
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

```
<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>
    Flight Schedule & Status - AeroPort
</title>

<link
    rel="stylesheet"
    href="../assets/css/dashboard.css">
```

</head>

<body>

<!-- =====================================================
     SIDEBAR
     ===================================================== -->

<aside class="sidebar">

```
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

            <?php
            echo htmlspecialchars(
                $staff_initials
            );
            ?>

        </div>

        <div>

            <h3>

                <?php
                echo htmlspecialchars(
                    $staff_name
                );
                ?>

            </h3>

            <span>

                <?php
                echo htmlspecialchars(
                    $staff_role
                );
                ?>

            </span>

        </div>

    </div>


    <div class="title">
        STAFF OPERATIONS
    </div>


    <nav>

        <a
            href="dashboard.php"
            class="menu">

            ▦ Dashboard

        </a>


        <a
            href="flight_schedule.php"
            class="menu active">

            📅 Flight Schedules

        </a>


        <a
            href="gate_assignment.php"
            class="menu">

            🛫 Gate & Terminal

        </a>


        <a
            href="baggage_status.php"
            class="menu">

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
            Day Mode
        </span>

    </p>


    <p>

        <a
            href="../logout.php"
            class="sign-out">

            ↪ Sign Out

        </a>

    </p>

</div>
```

</aside>

<!-- =====================================================
     MAIN CONTENT
     ===================================================== -->

<main class="main">

```
<!-- PAGE HEADER -->

<div class="page-top-bar">

    <div class="page-title-group">

        <h1>
            Flight Schedule & Status
        </h1>

        <p class="sub">
            Manage daily flight schedules and update operational status
        </p>

    </div>


    <button
        type="button"
        id="toggleFormBtn"
        class="btn-primary">

        + Add Schedule

    </button>

</div>



<!-- =================================================
     ADD SCHEDULE FORM
     ================================================= -->

<div
    id="addScheduleCard"
    class="form-card"
    style="display:none;">


    <div class="form-header">

        <h3>
            New Flight Schedule
        </h3>


        <span
            id="closeFormBtn"
            class="close-x">

            ✕

        </span>

    </div>


    <form
        method="POST"
        action="add_flight.php">


        <div class="form-grid">


            <!-- Flight Number -->

            <div class="form-group">

                <label>
                    Flight Number
                </label>

                <input
                    type="text"
                    name="flight_number"
                    placeholder="e.g. BG-401"
                    required>

            </div>


            <!-- Airline -->

            <div class="form-group">

                <label>
                    Airline
                </label>

                <input
                    type="text"
                    name="airline"
                    placeholder="e.g. Biman Bangladesh"
                    required>

            </div>


            <!-- Origin -->

            <div class="form-group">

                <label>
                    Origin
                </label>

                <input
                    type="text"
                    name="departure"
                    placeholder="e.g. DAC"
                    maxlength="3"
                    required>

            </div>


            <!-- Destination -->

            <div class="form-group">

                <label>
                    Destination
                </label>

                <input
                    type="text"
                    name="destination"
                    placeholder="e.g. DXB"
                    maxlength="3"
                    required>

            </div>


            <!-- Departure Time -->

            <div class="form-group">

                <label>
                    Departure Time
                </label>

                <input
                    type="time"
                    name="departure_time"
                    required>

            </div>


            <!-- Arrival Time -->

            <div class="form-group">

                <label>
                    Arrival Time
                </label>

                <input
                    type="time"
                    name="arrival_time"
                    required>

            </div>


            <!-- Aircraft -->

            <div class="form-group">

                <label>
                    Aircraft
                </label>

                <input
                    type="text"
                    name="aircraft"
                    placeholder="e.g. Boeing 777-300ER"
                    required>

            </div>


            <!-- Gate -->

            <div class="form-group">

                <label>
                    Gate
                </label>

                <input
                    type="text"
                    name="gate_number"
                    placeholder="e.g. G14">

            </div>

        </div>


        <div class="form-actions">

            <button
                type="submit"
                class="btn-save">

                Save Schedule

            </button>


            <button
                type="button"
                id="cancelFormBtn"
                class="btn-cancel">

                Cancel

            </button>

        </div>

    </form>

</div>



<!-- =================================================
     FILTERS
     ================================================= -->

<div class="filter-container">


    <div class="filter-pills">


        <button
            type="button"
            class="filter-btn active"
            data-status="all">

            All

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="on time">

            On Time

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="boarding">

            Boarding

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="delayed">

            Delayed

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="departed">

            Departed

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="arrived">

            Arrived

        </button>


        <button
            type="button"
            class="filter-btn"
            data-status="cancelled">

            Cancelled

        </button>

    </div>


    <div class="flight-count-text">

        <span id="visibleCount">

            <?php
            echo $total_flights;
            ?>

        </span>

        flights

    </div>

</div>



<!-- =================================================
     FLIGHT SCHEDULE TABLE
     ================================================= -->

<div class="schedule-table-card">


    <table class="schedule-table">


        <thead>

            <tr>

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
                    SCHEDULE
                </th>

                <th>
                    AIRCRAFT
                </th>

                <th>
                    GATE
                </th>

                <th>
                    STATUS
                </th>

                <th>
                    ACTION
                </th>

            </tr>

        </thead>


        <tbody>


        <?php if ($total_flights > 0): ?>


            <?php while ($flight = mysqli_fetch_assoc($flights_result)): ?>


                <?php

                $status =
                    $flight['status'] ?? 'On Time';

                $status_class =
                    getScheduleBadgeClass(
                        $status
                    );

                ?>


                <tr
                    class="flight-row"
                    data-status="<?php echo strtolower(trim($status)); ?>">


                    <!-- FLIGHT -->

                    <td>

                        <strong class="flight-no-link">

                            <?php
                            echo htmlspecialchars(
                                $flight['flight_number']
                            );
                            ?>

                        </strong>

                    </td>


                    <!-- AIRLINE -->

                    <td>

                        <span class="airline-text">

                            <?php
                            echo htmlspecialchars(
                                $flight['airline']
                            );
                            ?>

                        </span>

                    </td>


                    <!-- ROUTE -->

                    <td>

                        <div class="route-block">

                            <strong class="route-iata">

                                <?php
                                echo htmlspecialchars(
                                    $flight['departure']
                                );
                                ?>

                            </strong>


                            <span class="route-symbol">
                                ↓
                            </span>


                            <strong class="route-iata">

                                <?php
                                echo htmlspecialchars(
                                    $flight['destination']
                                );
                                ?>

                            </strong>

                        </div>

                    </td>


                    <!-- SCHEDULE -->

                    <td>

                        <div class="schedule-time-box">

                            <span>

                                <?php
                                echo htmlspecialchars(
                                    date(
                                        'H:i',
                                        strtotime(
                                            $flight['departure_time']
                                        )
                                    )
                                );
                                ?>

                            </span>


                            <span>

                                <?php
                                echo htmlspecialchars(
                                    date(
                                        'H:i',
                                        strtotime(
                                            $flight['arrival_time']
                                        )
                                    )
                                );
                                ?>

                            </span>

                        </div>

                    </td>


                    <!-- AIRCRAFT -->

                    <td>

                        <span class="aircraft-text">

                            <?php
                            echo htmlspecialchars(
                                $flight['aircraft']
                            );
                            ?>

                        </span>

                    </td>


                    <!-- GATE -->

                    <td>

                        <strong class="gate-bold-cyan">

                            <?php
                            echo htmlspecialchars(
                                $flight['gate_number']
                                ?? 'TBD'
                            );
                            ?>

                        </strong>

                    </td>


                    <!-- STATUS -->

                    <td>

                        <span
                            class="status-badge clickable-badge <?php echo $status_class; ?>">

                            •
                            <?php
                            echo htmlspecialchars(
                                $status
                            );
                            ?>

                        </span>

                    </td>


                    <!-- ACTION -->

                    <td>

                        <button
                            type="button"
                            class="btn-edit-action"
                            onclick="editFlight(<?php echo $flight['id']; ?>)">

                            Edit

                        </button>

                    </td>

                </tr>


            <?php endwhile; ?>


        <?php else: ?>


            <tr>

                <td
                    colspan="8"
                    style="text-align:center;">

                    No flight schedules available.

                </td>

            </tr>


        <?php endif; ?>


        </tbody>

    </table>

</div>



<!-- =================================================
     STATUS LEGEND
     ================================================= -->

<div class="status-legend-bar">


    <span>
        Flight status overview
    </span>


    <div class="legend-pills">


        <span class="status-badge status-scheduled">
            • On Time
        </span>


        <span class="status-badge status-boarding">
            • Boarding
        </span>


        <span class="status-badge status-delayed">
            • Delayed
        </span>


        <span class="status-badge status-departed">
            • Departed
        </span>


        <span class="status-badge status-arrived">
            • Arrived
        </span>


        <span class="status-badge status-cancelled">
            • Cancelled
        </span>


    </div>

</div>
```

</main>

<script src="../assets/js/dashboard.js"></script>



</body>

</html>
