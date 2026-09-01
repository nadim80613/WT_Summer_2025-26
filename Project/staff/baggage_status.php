<?php

/* =====================================================
   START SESSION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =====================================================
   CHECK LOGIN
===================================================== */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


/* =====================================================
   DATABASE
===================================================== */

include "../config/database.php";


/* =====================================================
   STAFF INFORMATION
===================================================== */

$user_id = $_SESSION['user_id'];

$user_sql = "SELECT name, role FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_sql);

$user = mysqli_fetch_assoc($user_result);

$staff_name = $user['name'] ?? 'Staff';
$staff_role = $user['role'] ?? 'Staff';

$staff_initials = strtoupper(substr($staff_name, 0, 2));


/* =====================================================
   UPDATE BAGGAGE STATUS
===================================================== */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_step'])) {

    $bag_id = (int)$_POST['bag_id'];
    $step = (int)$_POST['step'];

    /* Step → Status */

    if ($step == 1) {
        $status = "Checked In";
    }
    elseif ($step == 2) {
        $status = "Loaded";
    }
    elseif ($step == 3) {
        $status = "In Transit";
    }
    elseif ($step == 4) {
        $status = "Arrived";
    }
    elseif ($step == 5) {
        $status = "Delivered";
    }
    else {
        $status = "Checked In";
    }


    /* Update database */

    $update_sql = "
        UPDATE baggage
        SET baggage_status = '$status'
        WHERE id = '$bag_id'
    ";

    mysqli_query($conn, $update_sql);


    /* Send response to JavaScript */

    header('Content-Type: application/json');

    echo json_encode([
        "success" => true,
        "status" => $status
    ]);

    exit();
}


/* =====================================================
   GET STEP FROM STATUS
===================================================== */

function getStatusStep($status)
{
    $status = strtolower(trim($status));

    if (strpos($status, "check") !== false) {
        return 1;
    }

    if ($status == "loaded") {
        return 2;
    }

    if (strpos($status, "transit") !== false) {
        return 3;
    }

    if ($status == "arrived") {
        return 4;
    }

    if ($status == "delivered" || $status == "claimed") {
        return 5;
    }

    return 1;
}


/* =====================================================
   GET STATUS CSS CLASS
===================================================== */

function getBadgeClass($step)
{
    if ($step == 1) {
        return "status-checked-in";
    }

    if ($step == 2) {
        return "status-loaded";
    }

    if ($step == 3) {
        return "status-transit";
    }

    if ($step == 4) {
        return "status-arrived";
    }

    if ($step == 5) {
        return "status-delivered";
    }

    return "status-checked-in";
}


/* =====================================================
   GET BAGGAGE DATA
===================================================== */

/*
   IMPORTANT:
   There is NO b.weight here because your
   baggage table does not have a weight column.
*/

$baggage_sql = "

    SELECT

        b.id,

        CONCAT(
            COALESCE(f.departure, 'DAC'),
            '-',
            LPAD(2083140 + b.id * 105, 7, '0')
        ) AS baggage_tag,

        COALESCE(
            u.name,
            'Passenger'
        ) AS passenger_name,

        COALESCE(
            f.flight_number,
            'BG-401'
        ) AS flight_number,

        COALESCE(
            f.departure,
            'DAC'
        ) AS departure,

        COALESCE(
            f.destination,
            'DXB'
        ) AS destination,

        COALESCE(
            b.baggage_status,
            'Checked In'
        ) AS baggage_status

    FROM baggage b

    LEFT JOIN users u
        ON b.user_id = u.id

    LEFT JOIN bookings bk
        ON b.booking_id = bk.id

    LEFT JOIN flights f
        ON bk.flight_id = f.id

    ORDER BY b.id ASC

";


$baggage_result = mysqli_query($conn, $baggage_sql);


/* =====================================================
   STORE DATA
===================================================== */

$bags = [];

$checked_count = 0;
$loaded_count = 0;
$transit_count = 0;
$arrived_count = 0;
$delivered_count = 0;


if ($baggage_result) {

    while ($bag = mysqli_fetch_assoc($baggage_result)) {

        $step = getStatusStep(
            $bag['baggage_status']
        );

        $bag['step'] = $step;

        $bags[] = $bag;


        /* Count statuses */

        if ($step == 1) {
            $checked_count++;
        }
        elseif ($step == 2) {
            $loaded_count++;
        }
        elseif ($step == 3) {
            $transit_count++;
        }
        elseif ($step == 4) {
            $arrived_count++;
        }
        elseif ($step == 5) {
            $delivered_count++;
        }
    }
}


$total_count = count($bags);

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
        Baggage Handling - AeroPort
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >

</head>


<body class="light-mode">


<div class="app-layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">

        <div class="sidebar-top">


            <!-- LOGO -->

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


            <!-- PROFILE -->

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


            <!-- MENU TITLE -->

            <div class="title">
                STAFF OPERATIONS
            </div>


            <!-- NAVIGATION -->

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
                    class="menu"
                >
                    🛫 Gate & Terminal
                </a>


                <a
                    href="baggage_status.php"
                    class="menu active"
                >
                    🧳 Baggage Handling
                </a>

            </nav>

        </div>


        <!-- SIDEBAR BOTTOM -->

        <div class="sidebar-bottom">

            <p id="themeToggle">

                <span id="themeIcon">
                    🌙
                </span>

                <span id="themeText">
                    Dark Mode
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



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main">


        <!-- PAGE TITLE -->

        <div class="page-top-bar">

            <div>

                <h1>
                    Baggage Handling & Status
                </h1>

                <p class="sub">
                    Track and update baggage movement status
                </p>

            </div>

        </div>



        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="baggage-stat-cards">


            <div class="stat-card">

                <h4>
                    Checked-in
                </h4>

                <h2 class="text-blue">
                    <?= $checked_count; ?>
                </h2>

            </div>


            <div class="stat-card">

                <h4>
                    Loaded
                </h4>

                <h2 class="text-blue">
                    <?= $loaded_count; ?>
                </h2>

            </div>


            <div class="stat-card">

                <h4>
                    In Transit
                </h4>

                <h2 class="text-blue">
                    <?= $transit_count; ?>
                </h2>

            </div>


            <div class="stat-card">

                <h4>
                    Arrived
                </h4>

                <h2 class="text-blue">
                    <?= $arrived_count; ?>
                </h2>

            </div>


            <div class="stat-card">

                <h4>
                    Delivered
                </h4>

                <h2 class="text-blue">
                    <?= $delivered_count; ?>
                </h2>

            </div>


        </section>



        <!-- =====================================================
             SEARCH
        ====================================================== -->

        <div class="search-filter-container">

            <div class="search-input-wrapper">

                <span class="search-icon">
                    🔍
                </span>

                <input
                    type="text"
                    id="baggageSearch"
                    placeholder="Search by baggage tag, passenger name, or flight..."
                >

            </div>


            <div class="items-count-badge">

                <span id="itemsCount">
                    <?= $total_count; ?>
                </span>

                items

            </div>

        </div>



        <!-- =====================================================
             TABLE
        ====================================================== -->

        <div class="baggage-table-card">

            <table class="baggage-table">


                <thead>

                    <tr>

                        <th>
                            BAGGAGE TAG
                        </th>

                        <th>
                            PASSENGER
                        </th>

                        <th>
                            FLIGHT
                        </th>

                        <th>
                            ROUTE
                        </th>

                        <th>
                            PROGRESS
                        </th>

                        <th>
                            STATUS
                        </th>

                    </tr>

                </thead>


                <tbody id="baggageTableBody">


                <?php if ($total_count > 0): ?>


                    <?php foreach ($bags as $bag): ?>


                        <?php

                        $step = $bag['step'];

                        $badge_class =
                            getBadgeClass($step);

                        $tag_parts =
                            explode(
                                '-',
                                $bag['baggage_tag']
                            );

                        ?>


                        <tr
                            class="baggage-row"
                            data-id="<?= $bag['id']; ?>"
                        >


                            <!-- BAGGAGE TAG -->

                            <td>

                                <div class="baggage-tag-box">

                                    <span>
                                        <?= htmlspecialchars($tag_parts[0]); ?>–
                                    </span>

                                    <span>
                                        <?= htmlspecialchars($tag_parts[1] ?? ''); ?>
                                    </span>

                                </div>

                            </td>


                            <!-- PASSENGER -->

                            <td class="cell-passenger">

                                <strong>

                                    <?= htmlspecialchars(
                                        $bag['passenger_name']
                                    ); ?>

                                </strong>

                            </td>


                            <!-- FLIGHT -->

                            <td class="cell-flight">

                                <strong>

                                    <?= htmlspecialchars(
                                        $bag['flight_number']
                                    ); ?>

                                </strong>

                            </td>


                            <!-- ROUTE -->

                            <td class="cell-route">

                                <?= htmlspecialchars(
                                    $bag['departure']
                                ); ?>

                                →

                                <?= htmlspecialchars(
                                    $bag['destination']
                                ); ?>

                            </td>


                            <!-- PROGRESS -->

                            <td class="cell-progress">


                                <div
                                    class="progress-stepper"
                                    data-current="<?= $step; ?>"
                                >


                                    <?php for (
                                        $i = 1;
                                        $i <= 5;
                                        $i++
                                    ): ?>


                                        <?php if ($i > 1): ?>

                                            <div
                                                class="step-line
                                                <?= ($i <= $step)
                                                    ? 'completed'
                                                    : ''; ?>"
                                            ></div>

                                        <?php endif; ?>


                                        <div
                                            class="step-circle

                                            <?php

                                            if ($i < $step) {

                                                echo 'completed';

                                            }
                                            elseif ($i == $step) {

                                                echo 'active';

                                            }
                                            else {

                                                echo 'inactive';

                                            }

                                            ?>"

                                            onclick="
                                                updateBaggageStep(
                                                    <?= $bag['id']; ?>,
                                                    <?= $i; ?>
                                                )
                                            "

                                            title="Change status"
                                        >

                                            <?php if ($i < $step): ?>

                                                ✓

                                            <?php else: ?>

                                                <?= $i; ?>

                                            <?php endif; ?>

                                        </div>


                                    <?php endfor; ?>


                                </div>


                            </td>


                            <!-- STATUS -->

                            <td>

                                <span
                                    class="baggage-badge <?= $badge_class; ?>"
                                >

                                    •

                                    <?= htmlspecialchars(
                                        $bag['baggage_status']
                                    ); ?>

                                </span>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            style="
                                text-align:center;
                                padding:30px;
                            "
                        >

                            No baggage records found.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>

        </div>


    </main>

</div>



<!-- HELP BUTTON -->

<div class="help-btn">
    ?
</div>



<!-- DASHBOARD JS -->

<script src="../assets/js/dashboard.js"></script>


</body>

</html>