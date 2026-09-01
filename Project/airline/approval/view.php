<?php

session_start();

require_once '../../config/database.php';


/*CHECK LOGIN*/

if (!isset($_SESSION['user_id']))
{
    header('Location: ../../login.php');
    exit();
}


/*CHECK AIRLINE*/

if ($_SESSION['role'] != 'airline')
{
    header('Location: ../../index.php');
    exit();
}


/*GET USER ID*/

$user_id = (int)$_SESSION['user_id'];

$airline_name = $_SESSION['user_name'] ?? 'Airline';


/*GET USER EMAIL*/

$sql = "SELECT email
        FROM users
        WHERE id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

$stmt->close();


if (!$user)
{
    die("User not found.");
}


$user_email = $user['email'];


/*GET AIRLINE ID*/

$sql = "SELECT id, airline_name
        FROM airlines
        WHERE email = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "s",
    $user_email
);

$stmt->execute();

$result = $stmt->get_result();

$airline = $result->fetch_assoc();

$stmt->close();


if (!$airline)
{
    die("Airline account is not connected.");
}


/*REAL AIRLINE ID*/

$airline_id = (int)$airline['id'];

$airline_name = $airline['airline_name'];


/*GET APPROVAL REQUEST ID*/

$id = (int)($_GET['id'] ?? 0);


if ($id <= 0)
{
    header('Location: index.php');
    exit();
}


/*GET APPROVAL REQUEST*/

$sql = "SELECT
            r.*,
            a.model,
            a.manufacturer,
            a.registration_number

        FROM aircraft_approval_requests r

        LEFT JOIN airplanes a
        ON r.aircraft_id = a.id

        WHERE r.id = ?
        AND r.airline_id = ?

        LIMIT 1";


$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $id,
    $airline_id
);

$stmt->execute();

$result = $stmt->get_result();

$request = $result->fetch_assoc();

$stmt->close();


/*CHECK REQUEST*/

if (!$request)
{
    die("Approval request not found.");
}


/*GET STATUS*/

$status = strtolower(
    trim($request['status'] ?? '')
);

?>


<!DOCTYPE html>

<html>

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Approval Request
    </title>

    <link rel="stylesheet"
          href="../../assets/css/airline.css">

</head>


<body>


<div class="airline-layout">


<!-- SIDEBAR -->

<div class="sidebar">


    <div class="sidebar-logo">

        <h2>AMS</h2>

        <p>Airline Portal</p>

    </div>


    <nav class="sidebar-menu">


        <a href="../dashboard.php"
           class="menu-item">

            <span>📊</span>

            Dashboard

        </a>


        <div class="menu-section">

            <p class="menu-title">
                Aircraft
            </p>


            <a href="../airplane/index.php"
               class="menu-item">

                <span>✈️</span>

                My Aircraft

            </a>


            <a href="../airplane/add.php"
               class="menu-item">

                <span>➕</span>

                Add Aircraft

            </a>


            <a href="index.php"
               class="menu-item active">

                <span>📋</span>

                Approval Requests

            </a>

        </div>


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


<!-- MAIN CONTENT -->

<div class="main-content">
<header class="topbar">

    <div>

        <h1>
            Approval Request
        </h1>

        <p>
            View approval request details.
        </p>

    </div>


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



<!-- PAGE HEADER -->

<section class="page-header">


    <div>

        <h2>
            Approval Request #<?php echo $id; ?>
        </h2>

        <p>
            Details of your submitted request.
        </p>

    </div>


    <a href="index.php"
       class="btn btn-secondary">

        ← Back

    </a>


</section>



<!-- REQUEST INFORMATION -->

<section class="content-card">


    <div class="table-header">

        <h2>
            Request Information
        </h2>

        <p>
            Aircraft approval request details.
        </p>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <span>
                Aircraft
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['model'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Manufacturer
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['manufacturer'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Registration Number
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['registration_number'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Request Type
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    str_replace(
                        '_',
                        ' ',
                        ucwords(
                            $request['request_type']
                        )
                    )
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Submitted
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['submitted_at'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Status
            </span>

            <strong>


                <?php

                if ($status == 'approved')
                {

                    echo '<span class="status-badge status-approved">
                            🟢 Approved
                          </span>';

                }

                elseif ($status == 'rejected')
                {

                    echo '<span class="status-badge status-rejected">
                            🔴 Rejected
                          </span>';

                }

                else
                {

                    echo '<span class="status-badge status-pending">
                            🟡 Pending
                          </span>';

                }

                ?>


            </strong>

        </div>


    </div>


</section>



<!-- DESCRIPTION -->

<section class="content-card">


    <div class="table-header">

        <h2>
            Request Description
        </h2>

    </div>


    <p>

        <?php

        echo nl2br(
            htmlspecialchars(
                $request['description'] ?? 'No description.'
            )
        );

        ?>

    </p>


</section>



<!-- PROPOSED INFORMATION -->

<section class="content-card">


    <div class="table-header">

        <h2>
            Proposed Aircraft Information
        </h2>

    </div>


    <div class="details-grid">


        <div class="detail-item">

            <span>
                Model
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['proposed_model'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Manufacturer
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['proposed_manufacturer'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Capacity
            </span>

            <strong>

                <?php

                echo (int)(
                    $request['proposed_capacity'] ?? 0
                );

                ?>

                seats

            </strong>

        </div>


        <div class="detail-item">

            <span>
                Manufacturing Date
            </span>

            <strong>

                <?php

                echo htmlspecialchars(
                    $request['proposed_manufacturing_date'] ?? 'N/A'
                );

                ?>

            </strong>

        </div>
    </div>
</section>



<!-- AUTHORITY FEEDBACK -->

<section class="info-box">
    <div class="info-icon">

        💬

    </div>

    <div>
        <h3>
            Airport Authority Feedback
        </h3>

        <?php
        if (!empty($request['feedback']))
        {
        ?>
            <p>
                <?php

                echo nl2br(
                    htmlspecialchars(
                        $request['feedback']
                    )
                );

                ?>

            </p>

        <?php

        }
        else
        {

        ?>

            <p>
                No feedback has been provided yet.
            </p>

        <?php

        }


        ?>
    </div>
</section>
</div>
</div>
</body>
</html>