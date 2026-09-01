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

$sql = "SELECT f.*,
               a.model,
               a.registration_number,
               a.capacity
        FROM flights f
        JOIN airplanes a
        ON f.airplane_id = a.id
        WHERE f.id = ?
        AND a.airline_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $flight_id,
    $airline_id
);

$stmt->execute();

$result = $stmt->get_result();

$flight = $result->fetch_assoc();


if (!$flight) {
    die("Flight not found.");
}

?>


<!DOCTYPE html>

<html>

<head>

<title>Flight Details</title>

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

<h1>Flight Details</h1>

<p>View flight information.</p>

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


<!-- PAGE HEADER -->

<section class="page-header">


<div>

<h2>

<?php

echo htmlspecialchars(
    $flight['flight_number']
);

?>

</h2>

<p>

Flight information and assigned aircraft.

</p>

</div>


<div>

<a href="edit.php?id=<?php echo $flight['id']; ?>"
   class="btn btn-primary">

Edit Flight

</a>


<a href="index.php"
   class="btn btn-secondary">

← Back

</a>

</div>


</section>


<!-- FLIGHT INFORMATION -->

<section class="content-card">


<h3>Flight Information</h3>


<div class="details-grid">


<div class="detail-item">

<label>Flight Number</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['flight_number']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Status</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['status']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Departure</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['departure']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Arrival</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['destination']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Departure Time</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['departure_time']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Arrival Time</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['arrival_time']
);

?>

</strong>

</div>


</div>

</section>


<!-- AIRCRAFT INFORMATION -->

<section class="content-card">


<h3>Assigned Aircraft</h3>


<div class="details-grid">


<div class="detail-item">

<label>Aircraft Model</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['model']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Registration Number</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['registration_number']
);

?>

</strong>

</div>


<div class="detail-item">

<label>Total Seats</label>

<strong>

<?php

echo htmlspecialchars(
    $flight['capacity']
);

?>

</strong>

</div>


</div>

</section>


</div>

</div>


</body>

</html>
