<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

session_start();

require_once "../config/database.php";


/*
CHECK LOGIN
*/

if (!isset($_SESSION['user_id'])) {

    header("Location: ../login.php");
    exit();

}

/*
CHECK AIRLINE ROLE
*/

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'airline') {

    header("Location: ../index.php");
    exit();

}


/*
GET LOGGED-IN USER
*/

$user_id = $_SESSION['user_id'];

$airline_name = $_SESSION['user_name'] ?? 'Airline';


/*
| GET USER EMAIL
*/

$sql = "SELECT email FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {

    die("User information not found.");

}


$user_email = $user['email'];


/*
GET AIRLINE
the user's email to find the airline.
*/

$sql = "SELECT id, airline_name
        FROM airlines
        WHERE email = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $user_email);

$stmt->execute();

$result = $stmt->get_result();

$airline = $result->fetch_assoc();

$stmt->close();


if (!$airline) {

    die("Airline account is not connected to an airline.");

}


$airline_id = $airline['id'];

$airline_name = $airline['airline_name'];


/*
PAGE INFORMATION
*/

$page_title = "Airline Dashboard";

$current_page = "dashboard";


/*
DASHBOARD COUNTS
*/


/* Total Aircraft */

$sql = "SELECT COUNT(*) AS total
        FROM airplanes
        WHERE airline_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $airline_id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$aircraft_count = $row['total'];

$stmt->close();


/* Active Aircraft */

$sql = "SELECT COUNT(*) AS total
        FROM airplanes
        WHERE airline_id = ?
        AND status = 'active'";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $airline_id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$active_aircraft = $row['total'];

$stmt->close();


/* Total Flights */

$sql = "SELECT COUNT(*) AS total
        FROM flights f
        INNER JOIN airplanes a
        ON f.airplane_id = a.id
        WHERE a.airline_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $airline_id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$flight_count = $row['total'];

$stmt->close();


/* Pending Aircraft Approvals */

$sql = "SELECT COUNT(*) AS total
        FROM aircraft_approval_requests
        WHERE airline_id = ?
        AND status = 'pending'";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $airline_id);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$pending_approval = $row['total'];

$stmt->close();


/*
SCHEDULE REQUESTS
*/

$pending_schedule = 0;

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Airline Dashboard - AMS</title>

    <link rel="stylesheet"
          href="../assets/css/airline.css">

</head>


<body>


<div class="airline-layout">


    <!-- SIDEBAR -->

    <aside class="sidebar">


        <div class="sidebar-logo">

            <h2>AMS</h2>

            <p>Airline Portal</p>

        </div>


        <nav class="sidebar-menu">


            <!-- Dashboard -->

            <a href="dashboard.php"
               class="menu-item active">

                <span>📊</span>

                Dashboard

            </a>


            <!-- Aircraft -->

            <div class="menu-section">

                <p class="menu-title">
                    Aircraft
                </p>


                <a href="airplane/index.php"
                   class="menu-item">

                    <span>✈️</span>

                    My Aircraft

                </a>


                <a href="airplane/add.php"
                   class="menu-item">

                    <span>➕</span>

                    Add Aircraft

                </a>


                <a href="approval/index.php"
                   class="menu-item">

                    <span>📋</span>

                    Approval Requests

                </a>

            </div>


            <!-- Flights -->

            <div class="menu-section">

                <p class="menu-title">
                    Flights
                </p>


                <a href="flight/index.php"
                   class="menu-item">

                    <span>🛫</span>

                    My Flights

                </a>


                <a href="flight/add.php"
                   class="menu-item">

                    <span>➕</span>

                    Add Flight

                </a>


                <a href="flight/availability.php"
                   class="menu-item">

                    <span>💺</span>

                    Seat Availability

                </a>


                <a href="flight/schedule_request.php"
                   class="menu-item">

                    <span>🕐</span>

                    Schedule Requests

                </a>

            </div>


            <!-- Account -->

            <div class="menu-section">

                <p class="menu-title">
                    Account
                </p>


                <a href="../logout.php"
                   class="menu-item logout-item">

                    <span>🚪</span>

                    Logout

                </a>

            </div>


        </nav>

    </aside>



    <!-- MAIN CONTENT -->

    <main class="main-content">


        <!-- TOP BAR -->

        <header class="topbar">


            <div>

                <h1>
                    Airline Dashboard
                </h1>

                <p>
                    Manage your airline operations.
                </p>

            </div>


            <!-- Airline information -->

            <div class="user-info">


                <div class="user-avatar">

                    <?php

                    echo strtoupper(
                        substr($airline_name, 0, 1)
                    );

                    ?>

                </div>


                <div>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $airline_name
                        );

                        ?>

                    </strong>


                    <small>
                        Airline
                    </small>

                </div>

            </div>


        </header>



        <!-- WELCOME -->

        <section class="welcome-card">


            <div>

                <h2>

                    Welcome,

                    <?php

                    echo htmlspecialchars(
                        $airline_name
                    );

                    ?>!

                </h2>


                <p>

                    Manage your aircraft,
                    flights and approval requests
                    from one place.

                </p>
            </div>
        </section>



        <!-- STATISTICS -->

        <section class="stats-grid">


            <!-- Total Aircraft -->

            <div class="stat-card">

                <div class="stat-icon">
                    ✈️
                </div>


                <div>

                    <p>
                        Total Aircraft
                    </p>

                    <h3>

                        <?php

                        echo $aircraft_count;

                        ?>

                    </h3>

                </div>

            </div>



            <!-- Active Aircraft -->

            <div class="stat-card">

                <div class="stat-icon">
                    🟢
                </div>


                <div>

                    <p>
                        Active Aircraft
                    </p>

                    <h3>

                        <?php

                        echo $active_aircraft;

                        ?>

                    </h3>

                </div>

            </div>



            <!-- Total Flights -->

            <div class="stat-card">

                <div class="stat-icon">
                    🛫
                </div>


                <div>

                    <p>
                        Total Flights
                    </p>

                    <h3>

                        <?php

                        echo $flight_count;

                        ?>

                    </h3>

                </div>

            </div>



            <!-- Pending Approvals -->

            <div class="stat-card">

                <div class="stat-icon">
                    📋
                </div>


                <div>

                    <p>
                        Pending Approvals
                    </p>

                    <h3>

                        <?php

                        echo $pending_approval;

                        ?>

                    </h3>

                </div>

            </div>



            <!-- Schedule Requests -->

            <div class="stat-card">

                <div class="stat-icon">
                    🕐
                </div>


                <div>

                    <p>
                        Schedule Requests
                    </p>

                    <h3>

                        <?php

                        echo $pending_schedule;

                        ?>

                    </h3>

                </div>

            </div>


        </section>

        <!-- QUICK ACTIONS -->

        <section class="dashboard-section">


            <div class="section-header">

                <div>

                    <h2>
                        Quick Actions
                    </h2>

                    <p>
                        Common airline management tasks
                    </p>

                </div>

            </div>



            <div class="quick-actions">


                <!-- Add Aircraft -->

                <a href="airplane/add.php"
                   class="action-card">

                    <div class="action-icon">
                        ✈️
                    </div>

                    <div>

                        <h3>
                            Add Aircraft
                        </h3>

                        <p>
                            Register a new aircraft.
                        </p>

                    </div>

                </a>


                <!-- Add Flight -->

                <a href="flight/add.php"
                   class="action-card">

                    <div class="action-icon">
                        🛫
                    </div>

                    <div>

                        <h3>
                            Add Flight
                        </h3>

                        <p>
                            Create a new flight.
                        </p>

                    </div>

                </a>



                <!-- Approval Requests -->

                <a href="approval/index.php"
                   class="action-card">

                    <div class="action-icon">
                        📋
                    </div>

                    <div>

                        <h3>
                            Approval Requests
                        </h3>

                        <p>
                            Track authority decisions.
                        </p>

                    </div>

                </a>



                <!-- Schedule Change -->

                <a href="flight/schedule_request.php"
                   class="action-card">

                    <div class="action-icon">
                        🕐
                    </div>

                    <div>

                        <h3>
                            Schedule Change
                        </h3>

                        <p>
                            Request a time change.
                        </p>

                    </div>

                </a>


            </div>

        </section>



        <!-- AIRLINE MODULES -->

        <section class="dashboard-section">


            <div class="section-header">

                <div>

                    <h2>
                        Airline Modules
                    </h2>

                    <p>
                        Manage your airline operations
                    </p>

                </div>

            </div>



            <div class="module-grid">


                <!-- Aircraft Management -->

                <div class="module-card">


                    <div class="module-card-icon">
                        ✈️
                    </div>


                    <h3>
                        Aircraft Management
                    </h3>


                    <p>

                        Add, update and view aircraft
                        records and their current status.

                    </p>


                    <a href="airplane/index.php">

                        Manage Aircraft →

                    </a>


                </div>



                <!-- Aircraft Approval -->

                <div class="module-card">


                    <div class="module-card-icon">
                        📋
                    </div>


                    <h3>
                        Aircraft Approval
                    </h3>


                    <p>

                        Submit aircraft information
                        and track approval feedback.

                    </p>


                    <a href="approval/index.php">

                        View Approvals →

                    </a>


                </div>



                <!-- Flight Management -->

                <div class="module-card">


                    <div class="module-card-icon">
                        🛫
                    </div>


                    <h3>
                        Flight Management
                    </h3>


                    <p>

                        Create flights, assign aircraft
                        and monitor seat availability.

                    </p>


                    <a href="flight/index.php">

                        Manage Flights →

                    </a>


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>