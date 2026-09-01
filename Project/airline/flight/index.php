<?php

session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id'])){
    header('Location: ../../login.php');
    exit();
}

if(strtolower(trim($_SESSION['role'] ?? '')) !== 'airline'){
    header('Location: ../../index.php');
    exit();
}

$airline_id=(int)$_SESSION['user_id'];
$airline_name=$_SESSION['user_name'] ?? $_SESSION['name'] ?? 'Airline';


$stmt=$conn->prepare(
    'SELECT f.*,
            a.model,
            a.registration_number
     FROM flights f
     LEFT JOIN airplanes a
     ON f.airplane_id=a.id
     WHERE a.airline_id=?
     ORDER BY f.departure_time ASC'
);

$stmt->bind_param('i',$airline_id);
$stmt->execute();

$result=$stmt->get_result();

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


<div class="main-content">


<header class="topbar">


<div>

<h1>Flight Management</h1>

<p>Manage routes and schedules.</p>

</div>


<div class="user-info">


<div class="user-avatar">

<?php
echo strtoupper(substr($airline_name,0,1));
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


<?php

if($result->num_rows > 0):

while($f=$result->fetch_assoc()):

?>


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
    $f['departure'].' → '.$f['destination']
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
    $f['arrival_time']
);
?>

</td>


<td>

<?php

echo htmlspecialchars(
    ($f['model'] ?? 'N/A').
    ' / '.
    ($f['registration_number'] ?? 'N/A')
);

?>

</td>


<td>

<span class="status-badge">

<?php
echo htmlspecialchars($f['status']);
?>

</span>

</td>


<td>

<div class="table-actions">


<a
href="view.php?id=<?php echo (int)$f['id']; ?>"
class="btn btn-small btn-secondary">

View

</a>


<a
href="edit.php?id=<?php echo (int)$f['id']; ?>"
class="btn btn-small btn-primary">

Edit

</a>


</div>

</td>


</tr>


<?php

endwhile;

else:

?>


<tr>

<td colspan="7"
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
