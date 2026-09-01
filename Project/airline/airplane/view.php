```php
<?php

session_start();

require_once '../../config/database.php';


/* CHECK LOGIN */

if (!isset($_SESSION['user_id']))
     {
    header("Location: ../../login.php");
    exit();
    }


/* CHECK AIRLINE USER */

if (strtolower(trim($_SESSION['role'] ?? '')) != 'airline') {

    header("Location: ../../index.php");
    exit();

}


/* GET USER INFORMATION */

$user_id = (int)$_SESSION['user_id'];

$airline_name = $_SESSION['user_name']
                ?? $_SESSION['name']
                ?? 'Airline';


/* GET USER EMAIL */

$sql = "SELECT email
        FROM users
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();


if (!$user) {

    die("User not found.");

}

$user_email = $user['email'];


/* GET AIRLINE INFORMATION */

$sql = "SELECT id, airline_name
        FROM airlines
        WHERE email = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
$airline = $result->fetch_assoc();
$stmt->close();


if (!$airline) {

    die("Airline account is not connected.");

}


$airline_id = (int)$airline['id'];
$airline_name = $airline['airline_name'];


/* GET AIRCRAFT ID */

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0) {

    header("Location: index.php");
    exit();

}


/*GET AIRCRAFT */

$sql = "SELECT *
        FROM airplanes
        WHERE id = ?
        AND airline_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $id,
    $airline_id
);

$stmt->execute();

$result = $stmt->get_result();
$aircraft = $result->fetch_assoc();
$stmt->close();


/* CHECK AIRCRAFT */

if (!$aircraft) {

    die("Aircraft not found.");

}


/* GET STATUS */

$status = strtolower(
    trim($aircraft['status'] ?? '')
);

?>



<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Aircraft Profile
    </title>

    <link rel="stylesheet"
          href="../../assets/css/airline.css">
</head>


<body>

<div class="airline-layout">


<!-- ==============================
     SIDEBAR
============================== -->

<div class="sidebar">

    <div class="sidebar-logo">
        <h2>AMS</h2>
        <p>
            Airline Portal
        </p>
    </div>


    <nav class="sidebar-menu">


        <!-- DASHBOARD -->

        <a href="../dashboard.php"
           class="menu-item">

            <span>📊</span>

            Dashboard
        </a>


        <!-- AIRCRAFT -->

        <div class="menu-section">
            <p class="menu-title">
                Aircraft
            </p>


            <a href="index.php"
               class="menu-item active">

                <span>✈️</span>

                My Aircraft
            </a>


            <a href="add.php"
               class="menu-item">

                <span>➕</span>

                Add Aircraft
            </a>


            <a href="../approval/index.php"
               class="menu-item">

                <span>📋</span>

                Approval Requests

            </a>
        </div>


        <!-- FLIGHTS -->

        <div class="menu-section">
            <p class="menu-title">
                Flights
            </p>


            <a href="../flight/index.php"
               class="menu-item">

                <span>🛫</span>

                My Flights
            </a>


            <a href="../flight/add.php"
               class="menu-item">

                <span>➕</span>

                Add Flight
            </a>


            <a href="../flight/availability.php"
               class="menu-item">

                <span>💺</span>

                Seat Availability

            </a>


            <a href="../flight/schedule_request.php"
               class="menu-item">

                <span>🕐</span>

                Schedule Requests

            </a>
        </div>


        <!-- ACCOUNT -->

        <div class="menu-section">


            <p class="menu-title">
                Account
            </p>


            <a href="../../logout.php"
               class="menu-item logout-item">

                <span>🚪</span>

                Logout

            </a>

        </div>

    </nav>

</div>

<!--MAIN CONTENT -->

<div class="main-content">


<!-- TOPBAR -->

<header class="topbar">


    <div>
        <h1>
            Aircraft Profile
        </h1>
        <p>
            View aircraft information.
        </p>
    </div>


    <!-- USER -->

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



<!--PAGE HEADER -->

<section class="page-header">


    <div>
        <h2>
            Aircraft Profile
        </h2>
        <p>
            Detailed information about this aircraft.
        </p>
    </div>


    <div class="table-actions">


        <a href="edit.php?id=<?php echo $id; ?>"
           class="btn btn-primary">

            Edit

        </a>


        <a href="index.php"
           class="btn btn-secondary">

            ← Back to Aircraft

        </a>


    </div>
</section>



<!--AIRCRAFT INFORMATION -->

<section class="content-card">


    <div class="table-header">


        <h2>
            Aircraft Information
        </h2>


        <p>
            Current aircraft details.
        </p>


    </div>



    <div class="details-grid">


        <!-- AIRCRAFT ID -->

        <div class="detail-item">
            <span>
                Aircraft ID
            </span>
            <strong>

                #<?php echo $id; ?>

            </strong>
        </div>



        <!-- MODEL -->

        <div class="detail-item">
            <span>
                Model
            </span>

            <strong>
                <?php
                echo htmlspecialchars(
                    $aircraft['model']
                );
                ?>
            </strong>
        </div>



        <!-- MANUFACTURER -->

        <div class="detail-item">
            <span>
                Manufacturer
            </span>
            <strong>
                <?php
                echo htmlspecialchars(
                    $aircraft['manufacturer']
                    ?? 'N/A'
                );
                ?>
            </strong>
        </div>



        <!-- REGISTRATION -->

        <div class="detail-item">
            <span>
                Registration Number
            </span>
            <strong>

                <?php

                echo htmlspecialchars(
                    $aircraft['registration_number']
                );

                ?>

            </strong>
        </div>



        <!-- CAPACITY -->

        <div class="detail-item">

            <span>
                Passenger Capacity
            </span>
            <strong>

                <?php

                echo (int)$aircraft['capacity'];

                ?>

                seats

            </strong>
        </div>



        <!-- MANUFACTURING DATE -->

        <div class="detail-item">

            <span>
                Manufacturing Date
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $aircraft['manufacturing_date']
                    ?? 'N/A'
                );
                ?>

            </strong>
        </div>



        <!-- STATUS -->

        <div class="detail-item">
            <span>
                Status
            </span>

            <strong>

                <?php


                if ($status == 'active') {

                    echo '<span class="status-badge status-approved">
                            🟢 Active
                          </span>';

                }


                elseif (
                    strpos($status, 'pending') !== false
                ) {

                    echo '<span class="status-badge status-pending">
                            🟡 Pending Approval
                          </span>';

                }


                elseif ($status == 'maintenance') {

                    echo '<span class="status-badge status-warning">
                            🟠 Maintenance
                          </span>';

                }


                elseif ($status == 'rejected') {

                    echo '<span class="status-badge status-rejected">
                            🔴 Rejected
                          </span>';

                }


                else {

                    echo htmlspecialchars(
                        $aircraft['status']
                    );

                }


                ?>


            </strong>
        </div>

        <!-- CREATED DATE -->

        <div class="detail-item">


            <span>
                Created
            </span>


            <strong>

                <?php

                echo htmlspecialchars(
                    $aircraft['created_at']
                    ?? 'N/A'
                );

                ?>

            </strong>
        </div>
    </div>
</section>



<!-- APPROVAL INFORMATION -->

<section class="info-box">


    <div class="info-icon">
        ℹ️
    </div>


    <div>
        <h3>
            Aircraft Approval
        </h3>
        <p>
            New aircraft and aircraft
            information changes must be
            approved by the airport authority.
        </p>

        <a href="../approval/index.php">

            View Approval Requests →

        </a>
    </div>
</section>
</div>
</div>
</body>
</html>