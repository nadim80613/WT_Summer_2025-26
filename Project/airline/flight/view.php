```php
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

$id=(int)($_GET['id'] ?? 0);


$stmt=$conn->prepare(
    'SELECT f.*,
            a.model,
            a.registration_number,
            a.capacity
     FROM flights f
     LEFT JOIN airplanes a ON f.airplane_id=a.id
     WHERE f.id=?
     AND f.airline_id=?
     LIMIT 1'
);

$stmt->bind_param('ii',$id,$airline_id);
$stmt->execute();

$flight=$stmt->get_result()->fetch_assoc();

$stmt->close();


if(!$flight){
    header('Location: index.php');
    exit();
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

<h1>Flight Details</h1>

<p>
View flight information.
</p>

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

<h2>

Flight <?php
echo htmlspecialchars($flight['flight_number']);
?>

</h2>


<p>

<?php

echo htmlspecialchars(
    $flight['departure'] .
    ' → ' .
    $flight['destination']
);

?>

</p>

</div>


<div class="table-actions">


<a
href="edit.php?id=<?php echo $id; ?>"
class="btn btn-primary">

Edit

</a>


<a
href="index.php"
class="btn btn-secondary">

← Back

</a>


</div>


</section>


<section class="content-card">


<div class="details-grid">


<div class="detail-item">

<span>Flight Number</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['flight_number']
);
?>

</strong>

</div>


<div class="detail-item">

<span>Departure</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['departure']
);
?>

</strong>

</div>


<div class="detail-item">

<span>Arrival</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['destination']
);
?>

</strong>

</div>


<div class="detail-item">

<span>Departure Time</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['departure_time']
);
?>

</strong>

</div>


<div class="detail-item">

<span>Arrival Time</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['arrival_time']
);
?>

</strong>

</div>


<div class="detail-item">

<span>Aircraft</span>

<strong>

<?php

echo htmlspecialchars(
    ($flight['model'] ?? 'N/A') .
    ' / ' .
    ($flight['registration_number'] ?? 'N/A')
);

?>

</strong>

</div>


<div class="detail-item">

<span>Aircraft Capacity</span>

<strong>

<?php
echo (int)($flight['capacity'] ?? 0);
?>

seats

</strong>

</div>


<div class="detail-item">

<span>Status</span>

<strong>

<?php
echo htmlspecialchars(
    $flight['status']
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
```
