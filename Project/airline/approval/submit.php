<?php

session_start();

include "../../config/database.php";


/*
CHECK LOGIN
*/

if (!isset($_SESSION['user_id']))
{
    header("Location: ../../login.php");
    exit();
}


/*
CHECK AIRLINE
*/

if ($_SESSION['role'] != "airline")
{
    header("Location: ../../index.php");
    exit();
}


/*
GET AIRLINE ID
*/

$user_id = $_SESSION['user_id'];

$airline_name = $_SESSION['user_name'] ?? "Airline";


/*
GET USER EMAIL
*/

$sql = "SELECT email
        FROM users
        WHERE id='$user_id'";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

$user_email = $user['email'];


/*
GET AIRLINE ID
*/

$sql = "SELECT *
        FROM airlines
        WHERE email='$user_email'";

$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) == 0)
{
    die("Airline account is not connected.");
}


$airline = mysqli_fetch_assoc($result);

$airline_id = $airline['id'];

$airline_name = $airline['airline_name'];


/*
FORM VARIABLES
*/

$errors = [];

$aircraft_id = "";

$request_type = "modification";

$description = "";

$aircraft = null;


/*
GET AIRCRAFT
*/

$sql = "SELECT *
        FROM airplanes
        WHERE airline_id='$airline_id'
        ORDER BY model";

$result = mysqli_query($conn, $sql);


/*
FORM SUBMITTED
*/

if ($_SERVER["REQUEST_METHOD"] == "POST")
{

    /*
    GET FORM DATA
    */

    $aircraft_id = $_POST['aircraft_id'] ?? "";

    $request_type = $_POST['request_type'] ?? "";

    $description = trim($_POST['description'] ?? "");


    /*
    CHECK AIRCRAFT
    */

    if ($aircraft_id == "")
    {
        $errors[] = "Please select an aircraft.";
    }


    /*
    CHECK REQUEST TYPE
    */

    if (
        $request_type != "modification" &&
        $request_type != "specification_update"
    )
    {
        $errors[] = "Please select a valid request type.";
    }


    /*
    CHECK DESCRIPTION
    */

    if ($description == "")
    {
        $errors[] = "Description is required.";
    }


    /*
    FIND SELECTED AIRCRAFT
    */

    if (empty($errors))
    {

        $aircraft_id = (int)$aircraft_id;


        $sql = "SELECT *
                FROM airplanes
                WHERE id='$aircraft_id'
                AND airline_id='$airline_id'";

        $aircraft_result = mysqli_query($conn, $sql);


        if (mysqli_num_rows($aircraft_result) == 0)
        {
            $errors[] = "Aircraft not found.";
        }
        else
        {
            $aircraft = mysqli_fetch_assoc($aircraft_result);
        }

    }


    /*
    SAVE APPROVAL REQUEST
    */

    if (empty($errors))
    {

        $status = "pending";


        $model = $aircraft['model'];

        $capacity = $aircraft['capacity'];

        $manufacturer = $aircraft['manufacturer'];

        $manufacturing_date = $aircraft['manufacturing_date'];


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
        (
            '$aircraft_id',
            '$airline_id',
            '$request_type',
            '$description',
            '$model',
            '$capacity',
            '$manufacturer',
            '$manufacturing_date',
            '$status'
        )";


        if (mysqli_query($conn, $sql))
        {

            $request_id = mysqli_insert_id($conn);


            /*
            GO TO APPROVAL VIEW
            */

            header(
                "Location: view.php?id=$request_id"
            );

            exit();

        }
        else
        {

            $errors[] = "Could not submit approval request.";

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

<title>

Submit Approval Request - AMS

</title>

<link rel="stylesheet"
      href="../../assets/css/airline.css">

</head>

<body>

<div class="airline-layout">

<!-- SIDEBAR -->

<div class="sidebar">

<div class="sidebar-logo">

<h2>

AMS

</h2>

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

<!-- MAIN CONTENT -->

<div class="main-content">

<!-- TOPBAR -->

<header class="topbar">

<div>

<h1>

Submit Approval Request

</h1>

<p>

Submit an aircraft change for approval.

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

echo htmlspecialchars($airline_name);

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

New Approval Request

</h2>

<p>

Submit an aircraft modification or specification update.

</p>

</div>

<a href="index.php"
class="btn btn-secondary">

← Back

</a>

</section>

<!-- ERROR MESSAGE -->

<?php

if (!empty($errors))
{

?>

<div class="alert alert-error">

<strong>

Please fix the following:

</strong>

<ul>

<?php

foreach ($errors as $error)
{

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

<!-- FORM -->

<section class="content-card">

<div class="form-header">

<h2>

Approval Request Form

</h2>

<p>

Enter the information below.

</p>

</div>

<form method="POST">

<!-- AIRCRAFT -->

<div class="form-group">

<label>

Aircraft <span class="required">*</span>

</label>

<select name="aircraft_id"
     required>

<option value="">

-- Select Aircraft --

</option>

<?php

while ($row = mysqli_fetch_assoc($result))
{

?>

<option
value="<?php echo $row['id']; ?>"

<?php

if ($aircraft_id == $row['id'])
{
    echo "selected";
}

?>

>

<?php

echo htmlspecialchars(
    $row['model']
    . " - "
    . $row['manufacturer']
    . " ("
    . $row['registration_number']
    . ")"
);

?>

</option>

<?php

}

?>

</select>

</div>

<!-- REQUEST TYPE -->

<div class="form-group">

<label>

Request Type <span class="required">*</span>

</label>

<select name="request_type"
     required>

<option value="modification"

<?php

if ($request_type == "modification")
{
    echo "selected";
}

?>

>

Aircraft Modification

</option>

<option value="specification_update"

<?php

if ($request_type == "specification_update")
{
    echo "selected";
}

?>

>

Updated Specifications

</option>

</select>

</div>

<!-- DESCRIPTION -->

<div class="form-group">

<label>

Description <span class="required">*</span>

</label>

<textarea
name="description"
rows="5"
required
placeholder="Describe the aircraft change..."
><?php

echo htmlspecialchars($description);

?></textarea>

<small>

Explain what you want to change.

</small>

</div>

<!-- INFORMATION -->

<div class="form-notice">

<div class="notice-icon">

ℹ️

</div>

<div>

<strong>

Approval Required

</strong>

<p>

This request will be sent to the airport authority for approval.

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

Submit Request

</button>

</div>

</form>

</section>

</div>

</div>

</body>

</html>
