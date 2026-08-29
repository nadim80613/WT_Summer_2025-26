```php
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
   DATABASE CONNECTION
========================= */

include "../config/database.php";


$user_id = $_SESSION['user_id'];


/* =========================
   STAFF INFORMATION
========================= */

$user_sql = "
    SELECT
        id,
        name,
        email,
        role
    FROM users
    WHERE id = '$user_id'
    LIMIT 1
";

$user_result = mysqli_query($conn, $user_sql);

if (!$user_result) {

    die(
        "User query error: "
        . mysqli_error($conn)
    );

}

$user_data = mysqli_fetch_assoc($user_result);

if (!$user_data) {

    session_destroy();

    header("Location: ../login.php");
    exit();

}


$staff_name = $user_data['name'];

$staff_role = $user_data['role'];

$staff_initials =
    strtoupper(
        substr($staff_name, 0, 2)
    );


/* =========================
   DASHBOARD STATISTICS
========================= */


/* Total Flights */

$total_flights_sql = "
    SELECT COUNT(*) AS total
    FROM flights
";

$total_flights_result =
    mysqli_query(
        $conn,
        $total_flights_sql
    );

$total_flights_data =
    mysqli_fetch_assoc(
        $total_flights_result
    );

$total_flights =
    $total_flights_data['total'];


/* Delayed Flights */

$delayed_flights_sql = "
    SELECT COUNT(*) AS total
    FROM flights
    WHERE LOWER(status) = 'delayed'
";

$delayed_flights_result =
    mysqli_query(
        $conn,
        $delayed_flights_sql
    );

$delayed_flights_data =
    mysqli_fetch_assoc(
        $delayed_flights_result
    );

$delayed_flights =
    $delayed_flights_data['total'];


/* Active Gates */

$active_gates_sql = "
    SELECT COUNT(*) AS total
    FROM gates
    WHERE LOWER(availability) = 'occupied'
";

$active_gates_result =
    mysqli_query(
        $conn,
        $active_gates_sql
    );

$active_gates_data =
    mysqli_fetch_assoc(
        $active_gates_result
    );

$active_gates =
    $active_gates_data['total'];


/* Total Gates */

$total_gates_sql = "
    SELECT COUNT(*) AS total
    FROM gates
";

$total_gates_result =
    mysqli_query(
        $conn,
        $total_gates_sql
    );

$total_gates_data =
    mysqli_fetch_assoc(
        $total_gates_result
    );

$total_gates =
    $total_gates_data['total'];


/* =========================
   FLIGHT DATA
========================= */

$flights_sql = "
    SELECT

        f.id,

        f.flight_number,

        f.departure,

        f.destination,

        f.departure_time,

        f.arrival_time,

        f.status,

        COALESCE(
            a.airline_name,
            'N/A'
        ) AS airline,

        COALESCE(
            a.model,
            'N/A'
        ) AS aircraft,

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


$flights_result =
    mysqli_query(
        $conn,
        $flights_sql
    );


if (!$flights_result) {

    die(
        "Flight query error: "
        . mysqli_error($conn)
    );

}


/* =========================
   BAGGAGE DATA
========================= */

/*
   We are keeping baggage empty for now
   because your baggage table structure
   has not been provided yet.

   Once you give me the baggage table,
   I can connect this section too.
*/

$baggages = [];


/* =========================
   GATE DATA
========================= */

$gates_sql = "
    SELECT
        gate_number,
        availability
    FROM gates
    ORDER BY gate_number ASC
    LIMIT 8
";

$gates_result =
    mysqli_query(
        $conn,
        $gates_sql
    );


if (!$gates_result) {

    die(
        "Gate query error: "
        . mysqli_error($conn)
    );

}


/* =========================
   HELPER FUNCTIONS
========================= */

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Staff Dashboard - AeroPort
    </title>

    <link
        rel="stylesheet"
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

                <h2>
                    AeroPort
                </h2>

                <p>
                    Management System
                </p>

            </div>

        </div>


        <!-- Staff Profile -->

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


        <!-- Navigation Title -->

        <div class="title">
            STAFF OPERATIONS
        </div>


        <!-- Navigation -->

        <nav>


            <a
                href="dashboard.php"
                class="menu active">

                ▦ Dashboard

            </a>


            <a
                href="flight_schedule.php"
                class="menu">

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

            <a
                href="../logout.php"
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

            <?php
            echo date("M d, Y");
            ?>

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

                echo $total_flights;

                ?>

            </h2>


            <span class="sub-alert danger">

                ↓

                <?php

                echo $delayed_flights;

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

    $passenger_sql = "
        SELECT COUNT(*) AS total
        FROM users
        WHERE role = 'passenger'
    ";

    $passenger_result =
        mysqli_query(
            $conn,
            $passenger_sql
        );

    $passenger_data =
        mysqli_fetch_assoc(
            $passenger_result
        );

    echo $passenger_data['total'];

    ?>

</h2>

<span class="sub-alert success">

    Registered passengers

</span>


            <span class="sub-alert success">

                Live database

            </span>

        </div>


        <!-- Gates -->

        <div class="card">

            <h4>
                ACTIVE GATES
            </h4>


            <h2>

                <?php

                echo $active_gates;

                ?>

                /

                <?php

                echo $total_gates;

                ?>

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


                <a
                    href="flight_schedule.php"
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


                <?php

                if (
                    mysqli_num_rows(
                        $flights_result
                    ) > 0
                ):

                ?>


                    <?php

                    while (
                        $flight =
                        mysqli_fetch_assoc(
                            $flights_result
                        )
                    ):

                    ?>


                        <tr>


                            <!-- Flight -->

                            <td>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $flight['flight_number']
                                    );

                                    ?>

                                </strong>

                            </td>


                            <!-- Route -->

                            <td>

                                <?php

                                echo htmlspecialchars(
                                    $flight['departure']
                                );

                                ?>

                                →

                                <?php

                                echo htmlspecialchars(
                                    $flight['destination']
                                );

                                ?>

                            </td>


                            <!-- Departure -->

                            <td>

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

                            </td>


                            <!-- Arrival -->

                            <td>

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

                            </td>


                            <!-- Gate -->

                            <td>

                                <strong class="gate-text">

                                    <?php

                                    echo htmlspecialchars(
                                        $flight['gate_number']
                                        ?? 'TBD'
                                    );

                                    ?>

                                </strong>

                            </td>


                            <!-- Status -->

                            <td>

                                <span
                                    class="status
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


                    <?php

                    endwhile;

                    ?>


                <?php else: ?>


                    <tr>

                        <td
                            colspan="6"
                            style="text-align:center;">

                            No flights available.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>

            </table>


            <!-- ==================================================
                 BAGGAGE
            ================================================== -->

            <div
                class="section-header baggage-header">


                <h2>
                    Recent Baggage
                </h2>


                <a
                    href="baggage_status.php"
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


                    <?php if (!empty($baggages)): ?>


                        <?php foreach (
                            $baggages as $bag
                        ): ?>


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


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="4"
                                style="text-align:center;">

                                No baggage data available.

                            </td>

                        </tr>


                    <?php endif; ?>


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


                <?php

                if (
                    mysqli_num_rows(
                        $gates_result
                    ) > 0
                ):

                ?>


                    <?php

                    while (
                        $gate =
                        mysqli_fetch_assoc(
                            $gates_result
                        )
                    ):

                    ?>


                        <div class="gate-box">

                            <?php

                            echo htmlspecialchars(
                                $gate['gate_number']
                            );

                            ?>

                        </div>


                    <?php endwhile; ?>


                <?php else: ?>


                    <p>
                        No gates available.
                    </p>


                <?php endif; ?>


            </div>


            <!-- Manage Gates -->

            <div class="manage-gates">

                <a
                    href="gate_assignment.php"
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

<script
    src="../assets/js/dashboard.js">
</script>


</body>

</html>

