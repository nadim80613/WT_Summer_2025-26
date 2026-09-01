<?php

session_start();

require_once '../../config/database.php';


/* CHECK LOGIN */

if (!isset($_SESSION['user_id'])) {

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


/* GET AIRLINE ID */

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

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);


if ($id <= 0) {

    header("Location: index.php");
    exit();

}


/*GET AIRCRAFT*/

$sql = "SELECT *
        FROM airplanes
        WHERE id = ?
        AND airline_id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ii", $id, $airline_id);

$stmt->execute();

$result = $stmt->get_result();

$aircraft = $result->fetch_assoc();

$stmt->close();


if (!$aircraft) {

    die("Aircraft not found.");

}


/* PUT OLD VALUES INTO VARIABLES */

$model = $aircraft['model'];

$manufacturer = $aircraft['manufacturer'];

$registration_number =
    $aircraft['registration_number'];

$capacity = $aircraft['capacity'];

$manufacturing_date =
    $aircraft['manufacturing_date'];

$errors = [];


/* UPDATE AIRCRAFT */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    /* Get form values */

    $model = trim($_POST['model'] ?? '');

    $manufacturer =
        trim($_POST['manufacturer'] ?? '');

    $registration_number =
        trim($_POST['registration_number'] ?? '');

    $capacity =
        trim($_POST['capacity'] ?? '');

    $manufacturing_date =
        trim($_POST['manufacturing_date'] ?? '');


    /*VALIDATION */

    if ($model == '') {

        $errors[] = "Aircraft model is required.";

    }


    if ($manufacturer == '') {

        $errors[] = "Manufacturer is required.";

    }


    if ($registration_number == '') {

        $errors[] = "Registration number is required.";

    }


    if (
        $capacity == ''
        || !is_numeric($capacity)
        || (int)$capacity <= 0
    ) {

        $errors[] =
            "Capacity must be a valid positive number.";

    }


    if ($manufacturing_date == '') {

        $errors[] =
            "Manufacturing date is required.";

    }


    /*CHECK REGISTRATION NUMBER */

    if (empty($errors)) {

        $sql = "SELECT id
                FROM airplanes
                WHERE registration_number = ?
                AND id != ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "si",
            $registration_number,
            $id
        );

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows > 0) {

            $errors[] =
                "This registration number already exists.";

        }

        $stmt->close();

    }


    /* SAVE CHANGES */

    if (empty($errors)) {


        $capacity = (int)$capacity;

        /*
        Updated aircraft needs approval
        */

        $status = "Pending Approval";


        $sql = "UPDATE airplanes
                SET model = ?,
                    manufacturer = ?,
                    manufacturing_date = ?,
                    registration_number = ?,
                    capacity = ?,
                    status = ?
                WHERE id = ?
                AND airline_id = ?";


        $stmt = $conn->prepare($sql);


        $stmt->bind_param(
            "ssssissi",
            $model,
            $manufacturer,
            $manufacturing_date,
            $registration_number,
            $capacity,
            $status,
            $id,
            $airline_id
        );


        if ($stmt->execute()) {


            $stmt->close();


            /* CREATE APPROVAL REQUEST */

            $request_type = "modification";

            $description =
                "Aircraft information updated and submitted for approval.";

            $approval_status = "pending";


            $sql = "INSERT INTO aircraft_approval_requests
                    (
                        aircraft_id,
                        airline_id,
                        request_type,
                        description,
                        proposed_model,
                        proposed_capacity,
                        proposed_manufacturer,
                        proposed_manufacturing_date,
                        status
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?)";


            $stmt = $conn->prepare($sql);


            $stmt->bind_param(
                "iisssisss",
                $id,
                $airline_id,
                $request_type,
                $description,
                $model,
                $capacity,
                $manufacturer,
                $manufacturing_date,
                $approval_status
            );


            $stmt->execute();

            $stmt->close();


            /* Go back to aircraft page */

            header("Location: index.php?success=updated");

            exit();


        } else {

            $errors[] =
                "Could not update aircraft.";

            $stmt->close();

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Aircraft</title>

    <link rel="stylesheet"
          href="../../assets/css/airline.css">

</head>


<body>


<div class="airline-layout">


<!--SIDEBAR -->

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


<!--MAIN CONTENT -->

<div class="main-content">


<header class="topbar">


    <div>

        <h1>
            Edit Aircraft
        </h1>

        <p>
            Update aircraft information.
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


<!--PAGE HEADER -->

<section class="page-header">


    <div>

        <h2>
            Edit Aircraft
        </h2>

        <p>
            Change the aircraft information below.
        </p>

    </div>


    <a href="index.php"
       class="btn btn-secondary">

        ← Back to Aircraft

    </a>


</section>


<!--  ERROR MESSAGE  -->

<?php

if (!empty($errors)) {

?>


<div class="alert alert-error">


    <strong>
        Please fix the following:
    </strong>


    <ul>

        <?php

        foreach ($errors as $error) {

        ?>

            <li>

                <?php

                echo htmlspecialchars($error);

                ?>

            </li>

        <?php

        }

        ?>

    </ul>


</div>


<?php

}

?>


<!--FORM -->

<section class="content-card">


<div class="form-header">

    <h2>
        Aircraft Information
    </h2>

    <p>
        Update the aircraft details.
    </p>

</div>


<form method="POST"
      class="airline-form">


    <input type="hidden"
           name="id"
           value="<?php echo $id; ?>">


    <!-- MODEL -->

    <div class="form-group">

        <label>
            Aircraft Model
            <span class="required">*</span>
        </label>


        <input
            type="text"
            name="model"
            value="<?php echo htmlspecialchars($model); ?>"
            required
        >

    </div>


    <!-- MANUFACTURER -->

    <div class="form-group">

        <label>
            Manufacturer
            <span class="required">*</span>
        </label>


        <input
            type="text"
            name="manufacturer"
            value="<?php echo htmlspecialchars($manufacturer); ?>"
            required
        >

    </div>


    <!-- REGISTRATION -->

    <div class="form-group">

        <label>
            Registration Number
            <span class="required">*</span>
        </label>


        <input
            type="text"
            name="registration_number"
            value="<?php echo htmlspecialchars($registration_number); ?>"
            required
        >


        <small>
            Registration number must be unique.
        </small>

    </div>


    <!-- CAPACITY -->

    <div class="form-group">

        <label>
            Passenger Capacity
            <span class="required">*</span>
        </label>


        <input
            type="number"
            name="capacity"
            min="1"
            value="<?php echo htmlspecialchars($capacity); ?>"
            required
        >

    </div>


    <!-- MANUFACTURING DATE -->

    <div class="form-group">

        <label>
            Manufacturing Date
            <span class="required">*</span>
        </label>


        <input
            type="date"
            name="manufacturing_date"
            value="<?php echo htmlspecialchars($manufacturing_date); ?>"
            required
        >

    </div>


    <!-- APPROVAL NOTICE -->

    <div class="form-notice">


        <div class="notice-icon">
            ℹ️
        </div>


        <div>

            <strong>
                Approval Required
            </strong>


            <p>
                Changes will be submitted to the
                airport authority for approval.
            </p>

        </div>


    </div>


    <!-- BUTTONS -->

    <div class="form-actions">


        <a href="index.php"
           class="btn btn-secondary">

            Cancel

        </a>


        <button
            type="submit"
            class="btn btn-primary">

            Save Changes

        </button>
    </div>
</form>
</section>
</div>
</div>
</body>
</html>
