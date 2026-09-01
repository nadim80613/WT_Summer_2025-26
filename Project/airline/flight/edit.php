<?php

session_start();
require_once '../../config/database.php';


/* CHECK LOGIN */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

if ($_SESSION['role'] != 'airline') {
    header("Location: ../../index.php");
    exit();
}


/* GET USER */

$user_id = $_SESSION['user_id'];

$sql = "SELECT email FROM users WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$email = $user['email'];


/* GET AIRLINE */

$sql = "SELECT id, airline_name
        FROM airlines
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$airline = $result->fetch_assoc();


if (!$airline) {
    die("Airline record not found.");
}


$airline_id = $airline['id'];
$airline_name = $airline['airline_name'];


/* GET FLIGHT ID */

$flight_id = $_GET['id'] ?? 0;

if ($flight_id <= 0) {
    header("Location: index.php");
    exit();
}


/* GET FLIGHT */

$sql = "SELECT f.*
        FROM flights f
        JOIN airplanes a
        ON f.airplane_id = a.id
        WHERE f.id = ?
        AND a.airline_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $flight_id, $airline_id);
$stmt->execute();

$result = $stmt->get_result();

$flight = $result->fetch_assoc();


if (!$flight) {
    die("Flight not found.");
}


/* GET AIRCRAFT */

$sql = "SELECT id, model, registration_number, capacity
        FROM airplanes
        WHERE airline_id = ?
        ORDER BY model";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $airline_id);
$stmt->execute();

$aircraft_result = $stmt->get_result();


/* VARIABLES */

$errors = [];

$flight_number = $flight['flight_number'];
$departure = $flight['departure'];
$destination = $flight['destination'];
$departure_time = $flight['departure_time'];
$arrival_time = $flight['arrival_time'];
$airplane_id = $flight['airplane_id'];
$status = $flight['status'];


/* UPDATE FLIGHT */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $flight_number = trim($_POST['flight_number']);
    $departure = trim($_POST['departure']);
    $destination = trim($_POST['destination']);
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $airplane_id = $_POST['airplane_id'];
    $status = $_POST['status'];


    /* CHECK FIELDS */

    if ($flight_number == "") {
        $errors[] = "Flight number is required.";
    }

    if ($departure == "") {
        $errors[] = "Departure location is required.";
    }

    if ($destination == "") {
        $errors[] = "Arrival location is required.";
    }

    if ($departure_time == "") {
        $errors[] = "Departure time is required.";
    }

    if ($arrival_time == "") {
        $errors[] = "Arrival time is required.";
    }

    if ($airplane_id == "") {
        $errors[] = "Please select an aircraft.";
    }

    if ($status == "") {
        $errors[] = "Please select a status.";
    }


    /* CHECK AIRCRAFT */

    if (empty($errors)) {

        $sql = "SELECT id
                FROM airplanes
                WHERE id = ?
                AND airline_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $airplane_id, $airline_id);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $errors[] = "Invalid aircraft selected.";
        }
    }


    /* UPDATE */

    if (empty($errors)) {

        $sql = "UPDATE flights
                SET flight_number = ?,
                    airplane_id = ?,
                    departure = ?,
                    destination = ?,
                    departure_time = ?,
                    arrival_time = ?,
                    status = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sisssssi",
            $flight_number,
            $airplane_id,
            $departure,
            $destination,
            $departure_time,
            $arrival_time,
            $status,
            $flight_id
        );


        if ($stmt->execute()) {

            header("Location: index.php?success=updated");
            exit();

        } else {

            $errors[] = "Could not update flight.";
        }
    }
}

?>


<!DOCTYPE html>

<html>

<head>

<title>Edit Flight</title>

<link rel="stylesheet"
      href="../../assets/css/airline.css">

</head>


<body>


<div class="airline-container">


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


            <a href="../approval/index.php"
               class="menu-item">

                <span>📋</span>

                Approval Requests

            </a>

        </div>


        <div class="menu-section">

            <p class="menu-title">

                Flights

            </p>


            <a href="index.php"
               class="menu-item active">

                <span>🛫</span>

                My Flights

            </a>


            <a href="add.php"
               class="menu-item">

                <span>➕</span>

                Add Flight

            </a>


            <a href="availability.php"
               class="menu-item">

                <span>💺</span>

                Seat Availability

            </a>


            <a href="schedule_request.php"
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

        <h1>Edit Flight</h1>

        <p>Update flight information.</p>

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

            <small>Airline</small>

        </div>


    </div>


</header>


<section class="page-header">


    <div>

        <h2>Flight Information</h2>

        <p>Edit the flight details below.</p>

    </div>


    <a href="index.php"
       class="btn btn-secondary">

        ← Back

    </a>


</section>


<!-- ERRORS -->

<?php if (!empty($errors)): ?>

    <div class="alert alert-error">

        <ul>

            <?php foreach ($errors as $error): ?>

                <li>

                    <?php

                    echo htmlspecialchars($error);

                    ?>

                </li>

            <?php endforeach; ?>

        </ul>

    </div>

<?php endif; ?>


<!-- FORM -->

<section class="content-card">


<form method="POST"
      class="airline-form">


    <div class="form-group">

        <label>

            Flight Number *

        </label>


        <input
            type="text"
            name="flight_number"
            value="<?php echo htmlspecialchars($flight_number); ?>"
            required>

    </div>


    <div class="form-group">

        <label>

            Departure Location *

        </label>


        <input
            type="text"
            name="departure"
            value="<?php echo htmlspecialchars($departure); ?>"
            required>

    </div>


    <div class="form-group">

        <label>

            Arrival Location *

        </label>


        <input
            type="text"
            name="destination"
            value="<?php echo htmlspecialchars($destination); ?>"
            required>

    </div>


    <div class="form-group">

        <label>

            Departure Time *

        </label>


        <input
            type="datetime-local"
            name="departure_time"
            value="<?php echo date('Y-m-d\TH:i', strtotime($departure_time)); ?>"
            required>

    </div>


    <div class="form-group">

        <label>

            Arrival Time *

        </label>


        <input
            type="datetime-local"
            name="arrival_time"
            value="<?php echo date('Y-m-d\TH:i', strtotime($arrival_time)); ?>"
            required>

    </div>


    <div class="form-group">

        <label>

            Aircraft *

        </label>


        <select name="airplane_id" required>


            <option value="">

                -- Select Aircraft --

            </option>


            <?php while ($aircraft = $aircraft_result->fetch_assoc()): ?>


                <option
                    value="<?php echo $aircraft['id']; ?>"
                    <?php

                    if ($airplane_id == $aircraft['id']) {

                        echo "selected";

                    }

                    ?>
                >


                    <?php

                    echo htmlspecialchars(
                        $aircraft['model']
                        . " - "
                        . $aircraft['registration_number']
                        . " ("
                        . $aircraft['capacity']
                        . " seats)"
                    );

                    ?>


                </option>


            <?php endwhile; ?>


        </select>

    </div>


    <div class="form-group">

        <label>

            Status *

        </label>


        <select name="status" required>


            <option value="Scheduled"
                <?php if ($status == "Scheduled") echo "selected"; ?>>

                Scheduled

            </option>


            <option value="Boarding"
                <?php if ($status == "Boarding") echo "selected"; ?>>

                Boarding

            </option>


            <option value="Departed"
                <?php if ($status == "Departed") echo "selected"; ?>>

                Departed

            </option>


            <option value="Completed"
                <?php if ($status == "Completed") echo "selected"; ?>>

                Completed

            </option>


            <option value="Cancelled"
                <?php if ($status == "Cancelled") echo "selected"; ?>>

                Cancelled

            </option>


        </select>

    </div>


    <div class="form-actions">


        <a href="index.php"
           class="btn btn-secondary">

            Cancel

        </a>


        <button type="submit"
                class="btn btn-primary">

            Update Flight

        </button>


    </div>


</form>

</section>


</div>

</div>


</body>

</html>
