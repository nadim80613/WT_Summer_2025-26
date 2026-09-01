<?php

session_start();
require_once '../../config/database.php';


/* CHECK LOGIN */

if(!isset($_SESSION['user_id'])){
    header('Location: ../../login.php');
    exit();
}


if(strtolower(trim($_SESSION['role'] ?? '')) !== 'airline'){
    header('Location: ../../index.php');
    exit();
}


/* GET LOGGED IN USER ID */

$user_id=(int)$_SESSION['user_id'];


/* GET USER EMAIL */

$stmt=$conn->prepare(
    "SELECT email
     FROM users
     WHERE id=?
     LIMIT 1"
);

$stmt->bind_param('i',$user_id);

$stmt->execute();

$user=$stmt->get_result()->fetch_assoc();

$stmt->close();


if(!$user){
    die("User information not found.");
}


/* GET AIRLINE INFORMATION */

$stmt=$conn->prepare(
    "SELECT id,airline_name
     FROM airlines
     WHERE email=?
     LIMIT 1"
);

$stmt->bind_param('s',$user['email']);

$stmt->execute();

$airline=$stmt->get_result()->fetch_assoc();

$stmt->close();


if(!$airline){
    die("Airline information not found.");
}


$airline_id=(int)$airline['id'];

$airline_name=$airline['airline_name'];


/* GET FLIGHTS */

$stmt=$conn->prepare(
    "SELECT
        f.id,
        f.flight_number,
        f.departure,
        f.destination,
        f.departure_time,
        f.status,
        a.model,
        a.registration_number,
        a.capacity
     FROM flights f
     JOIN airplanes a
     ON f.airplane_id=a.id
     WHERE a.airline_id=?
     ORDER BY f.departure_time ASC"
);

$stmt->bind_param('i',$airline_id);

$stmt->execute();

$result=$stmt->get_result();

?>


<!DOCTYPE html>

<html>

<head>

<title>Seat Availability</title>

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
   class="menu-item">

<span>🛫</span>

My Flights

</a>


<a href="add.php"
   class="menu-item">

<span>➕</span>

Add Flight

</a>


<a href="availability.php"
   class="menu-item active">

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

<h1>Seat Availability</h1>

<p>
Monitor aircraft seat capacity.
</p>

</div>


<div class="user-info">


<div class="user-avatar">

<?php

echo strtoupper(
    substr($airline_name,0,1)
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

<h2>Seat Availability</h2>

<p>

View the seating capacity of your flights.

</p>

</div>


</section>


<section class="content-card">


<div class="table-container">


<table class="data-table">


<thead>

<tr>

<th>Flight</th>

<th>Route</th>

<th>Departure</th>

<th>Aircraft</th>

<th>Total Seats</th>

<th>Status</th>

</tr>

</thead>


<tbody>


<?php if($result->num_rows>0): ?>


<?php while($f=$result->fetch_assoc()): ?>


<tr>


<td>

<strong>

<?php

echo htmlspecialchars(
    $f['flight_number']
);

?>

</strong>

</td>


<td>

<?php

echo htmlspecialchars(
    $f['departure']
    .' → '
    .$f['destination']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $f['departure_time']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $f['model']
    .' / '
    .$f['registration_number']
);

?>

</td>


<td>

<strong>

<?php

echo (int)$f['capacity'];

?>

</strong>

seats

</td>


<td>

<span class="status-badge">

<?php

echo htmlspecialchars(
    $f['status']
);

?>

</span>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="6"
    style="text-align:center">

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


<?php

$stmt->close();

?>