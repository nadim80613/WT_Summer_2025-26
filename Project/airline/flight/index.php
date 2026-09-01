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


/* GET USER ID */

$user_id = $_SESSION['user_id'];


/* GET USER EMAIL */

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


/* GET FLIGHTS */

$sql = "SELECT f.*,
               a.model,
               a.registration_number
        FROM flights f
        JOIN airplanes a
        ON f.airplane_id = a.id
        WHERE a.airline_id = ?
        ORDER BY f.departure_time ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $airline_id);
$stmt->execute();

$result = $stmt->get_result();

?>


<!DOCTYPE html>

<html>

<head>

<title>Flight Management</title>

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

<h1>Flight Management</h1>

<p>Manage routes and schedules.</p>

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

<small>Airline</small>

</div>


</div>


</header>


<!-- PAGE HEADER -->

<section class="page-header">


<div>

<h2>My Flights</h2>

<p>

Manage routes, schedules and assigned aircraft.

</p>

</div>


<a href="add.php"
   class="btn btn-primary">

+ Add Flight

</a>


</section>


<!-- FLIGHT TABLE -->

<section class="content-card">


<div class="table-container">


<table class="data-table">


<thead>

<tr>

<th>Flight</th>

<th>Route</th>

<th>Departure</th>

<th>Arrival</th>

<th>Aircraft</th>

<th>Status</th>

<th>Actions</th>

</tr>

</thead>


<tbody>


<?php if ($result->num_rows > 0): ?>


<?php while ($f = $result->fetch_assoc()): ?>


<tr>


<!-- FLIGHT NUMBER -->

<td>

<strong>

<?php

echo htmlspecialchars(
    $f['flight_number']
);

?>

</strong>

</td>


<!-- ROUTE -->

<td>

<?php

echo htmlspecialchars(
    $f['departure']
    . " → "
    . $f['destination']
);

?>

</td>


<!-- DEPARTURE -->

<td>

<?php

echo htmlspecialchars(
    $f['departure_time']
);

?>

</td>


<!-- ARRIVAL -->

<td>

<?php

echo htmlspecialchars(
    $f['arrival_time']
);

?>

</td>


<!-- AIRCRAFT -->

<td>

<?php

echo htmlspecialchars(
    $f['model']
    . " / "
    . $f['registration_number']
);

?>

</td>


<!-- STATUS -->

<td>

<span class="status-badge">

<?php

echo htmlspecialchars(
    $f['status']
);

?>

</span>

</td>


<!-- ACTIONS -->

<td>

<div class="table-actions">


<a
href="view.php?id=<?php echo $f['id']; ?>"
class="btn btn-small btn-secondary">

View

</a>


<a
href="edit.php?id=<?php echo $f['id']; ?>"
class="btn btn-small btn-primary">

Edit

</a>


</div>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="7"
    style="text-align:center;">

No flights found.

</td>

</tr>


<?php endif; ?>


</tbody>

</table>

</div>

</section>


</div>

</div>


</body>

</html>