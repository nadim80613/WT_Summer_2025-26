<?php

session_start();

require_once '../../config/database.php';


// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}


// Check airline role
if ($_SESSION['role'] != 'airline') {
    header("Location: ../../index.php");
    exit();
}


// Get user ID
$user_id = $_SESSION['user_id'];


// Get airline information
$sql = "SELECT airlines.id, airlines.airline_name
        FROM airlines
        INNER JOIN users ON airlines.email = users.email
        WHERE users.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$airline = $result->fetch_assoc();

$stmt->close();


if (!$airline) {
    die("Airline not found.");
}


$airline_id = $airline['id'];
$airline_name = $airline['airline_name'];


// Get aircraft
$sql = "SELECT id, model, registration_number, capacity
        FROM airplanes
        WHERE airline_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $airline_id);
$stmt->execute();

$aircraft_result = $stmt->get_result();


// Empty values
$error = "";

$flight_number = "";
$departure = "";
$destination = "";
$departure_time = "";
$arrival_time = "";
$airplane_id = "";


// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $flight_number = $_POST['flight_number'];
    $departure = $_POST['departure'];
    $destination = $_POST['destination'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $airplane_id = $_POST['airplane_id'];


    // Check empty fields
    if (
        empty($flight_number) ||
        empty($departure) ||
        empty($destination) ||
        empty($departure_time) ||
        empty($arrival_time) ||
        empty($airplane_id)
    ) {

        $error = "Please fill in all fields.";

    }

    // Check same location
    elseif (strtolower($departure) == strtolower($destination)) {

        $error = "Departure and arrival locations cannot be the same.";

    }

    // Check time
    elseif (strtotime($arrival_time) <= strtotime($departure_time)) {

        $error = "Arrival time must be after departure time.";

    }

    else {

        // Add flight
        $status = "Scheduled";

        $sql = "INSERT INTO flights
                (flight_number, airplane_id, departure, destination,
                 departure_time, arrival_time, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "sisssss",
            $flight_number,
            $airplane_id,
            $departure,
            $destination,
            $departure_time,
            $arrival_time,
            $status
        );


        if ($stmt->execute()) {

            header("Location: index.php?success=added");
            exit();

        } else {

            $error = "Flight could not be added.";

        }

        $stmt->close();
    }
}

?>


<!DOCTYPE html>

<html>

<head>

    <title>Add Flight</title>

    <link rel="stylesheet" href="../../assets/css/airline.css">

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


            <!-- Dashboard -->

            <a href="../dashboard.php" class="menu-item">

                <span>📊</span>

                Dashboard

            </a>


            <!-- Aircraft -->

            <div class="menu-section">

                <p class="menu-title">
                    Aircraft
                </p>


                <a href="../airplane/index.php" class="menu-item">

                    <span>✈️</span>

                    My Aircraft

                </a>


                <a href="../airplane/add.php" class="menu-item">

                    <span>➕</span>

                    Add Aircraft

                </a>


                <a href="../approval/index.php" class="menu-item">

                    <span>✅</span>

                    Approval Requests

                </a>

            </div>


            <!-- Flights -->

            <div class="menu-section">

                <p class="menu-title">
                    Flights
                </p>


                <a href="index.php" class="menu-item">

                    <span>🛬</span>

                    My Flights

                </a>


                <a href="add.php" class="menu-item active">

                    <span>➕</span>

                    Add Flight

                </a>


                <a href="availability.php" class="menu-item">

                    <span>💺</span>

                    Seat Availability

                </a>


                <a href="schedule_request.php" class="menu-item">

                    <span>📅</span>

                    Schedule Requests

                </a>

            </div>


            <!-- Logout -->

            <div class="menu-section">

                <p class="menu-title">
                    Account
                </p>


                <a href="../../logout.php" class="menu-item">

                    <span>🚪</span>

                    Logout

                </a>

            </div>


        </nav>

    </div>



    <!-- MAIN CONTENT -->

    <div class="main-content">


        <!-- TOP BAR -->

        <div class="topbar">

            <div>

                <h1>Add Flight</h1>

                <p>Create a new flight.</p>

            </div>


            <div class="user-info">

                <div class="user-avatar">

                    <?php
                    echo strtoupper(substr($airline_name, 0, 1));
                    ?>

                </div>


                <div>

                    <strong>

                        <?php
                        echo htmlspecialchars($airline_name);
                        ?>

                    </strong>

                    <small>Airline</small>

                </div>

            </div>

        </div>



        <!-- PAGE HEADER -->

        <div class="page-header">

            <div>

                <h2>Flight Information</h2>

                <p>Enter the flight details below.</p>

            </div>


            <a href="index.php" class="btn btn-secondary">

                ← Back

            </a>

        </div>



        <!-- ERROR -->

        <?php if ($error != "") { ?>

            <div class="alert alert-error">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php } ?>



        <!-- FORM -->

        <div class="content-card">


            <form method="POST" class="airline-form">


                <!-- Flight Number -->

                <div class="form-group">

                    <label>
                        Flight Number
                    </label>

                    <input
                        type="text"
                        name="flight_number"
                        value="<?php echo htmlspecialchars($flight_number); ?>"
                        placeholder="Example: BG101"
                        required>

                </div>



                <!-- Departure -->

                <div class="form-group">

                    <label>
                        Departure Location
                    </label>

                    <input
                        type="text"
                        name="departure"
                        value="<?php echo htmlspecialchars($departure); ?>"
                        placeholder="Example: Dhaka"
                        required>

                </div>



                <!-- Destination -->

                <div class="form-group">

                    <label>
                        Arrival Location
                    </label>

                    <input
                        type="text"
                        name="destination"
                        value="<?php echo htmlspecialchars($destination); ?>"
                        placeholder="Example: Chittagong"
                        required>

                </div>



                <!-- Departure Time -->

                <div class="form-group">

                    <label>
                        Departure Time
                    </label>

                    <input
                        type="datetime-local"
                        name="departure_time"
                        value="<?php echo htmlspecialchars($departure_time); ?>"
                        required>

                </div>



                <!-- Arrival Time -->

                <div class="form-group">

                    <label>
                        Arrival Time
                    </label>

                    <input
                        type="datetime-local"
                        name="arrival_time"
                        value="<?php echo htmlspecialchars($arrival_time); ?>"
                        required>

                </div>



                <!-- Aircraft -->

                <div class="form-group">

                    <label>
                        Select Aircraft
                    </label>


                    <select name="airplane_id" required>

                        <option value="">
                            -- Select Aircraft --
                        </option>


                        <?php while ($aircraft = $aircraft_result->fetch_assoc()) { ?>

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
                                    . " (" . $aircraft['capacity'] . " seats)"
                                );
                                ?>

                            </option>

                        <?php } ?>

                    </select>

                </div>



                <!-- Buttons -->

                <div class="form-actions">

                    <a href="index.php"
                       class="btn btn-secondary">

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary">

                        Add Flight

                    </button>

                </div>


            </form>

        </div>


    </div>

</div>


</body>

</html>