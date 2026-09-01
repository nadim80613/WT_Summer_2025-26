```php
<?php

session_start();

include "../../config/database.php";


/*
CHECK LOGIN
*/

if(!isset($_SESSION['user_id']))
{
    header("Location:../../login.php");
    exit();
}


if($_SESSION['role'] != "airline")
{
    header("Location:../../index.php");
    exit();
}


/*
GET USER ID
*/

$user_id = $_SESSION['user_id'];

$airline_name = $_SESSION['user_name'] ?? "Airline";


/*
GET USER EMAIL
*/

$sql = "SELECT email FROM users WHERE id='$user_id'";

$result = mysqli_query($conn,$sql);

$user = mysqli_fetch_assoc($result);

$user_email = $user['email'];


/*
GET AIRLINE ID
*/

$sql = "SELECT * FROM airlines WHERE email='$user_email'";

$result = mysqli_query($conn,$sql);


if(mysqli_num_rows($result) == 0)
{
    die("Airline account is not connected.");
}


$airline = mysqli_fetch_assoc($result);

$airline_id = $airline['id'];

$airline_name = $airline['airline_name'];


/*
GET AIRCRAFT
*/

$sql = "SELECT * FROM airplanes
        WHERE airline_id='$airline_id'
        ORDER BY id DESC";


$result = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>

<html>


<head>

<title>Aircraft Management</title>

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


<!-- MAIN CONTENT -->


<div class="main-content">


<!-- TOPBAR -->


<header class="topbar">


<div>

<h1>

Aircraft Management

</h1>

<p>

Manage your aircraft.

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

My Aircraft

</h2>

<p>

View and manage aircraft registered under your airline.

</p>

</div>


<a href="add.php"
   class="btn btn-primary">

+ Add Aircraft

</a>


</section>


<!-- SUCCESS MESSAGE -->


<?php

if(isset($_GET['success']))
{

?>


<div class="alert alert-success">


<?php

if($_GET['success'] == "added")
{
    echo "Aircraft added successfully. An approval request has been submitted.";
}

elseif($_GET['success'] == "updated")
{
    echo "Aircraft updated successfully.";
}

else
{
    echo "Operation completed successfully.";
}

?>


</div>


<?php

}

?>


<!-- AIRCRAFT TABLE -->


<section class="content-card">


<div class="table-header">


<h2>

Aircraft Records

</h2>


<p>

Aircraft belonging to your airline.

</p>


</div>


<?php

if(mysqli_num_rows($result) > 0)
{

?>


<div class="table-container">


<table class="data-table">


<tr>


<th>ID</th>

<th>Model</th>

<th>Manufacturer</th>

<th>Registration</th>

<th>Capacity</th>

<th>Status</th>

<th>Action</th>


</tr>


<?php

while($row = mysqli_fetch_assoc($result))
{

?>


<tr>


<td>

<?php

echo $row['id'];

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['model']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['manufacturer']
);

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $row['registration_number']
);

?>

</td>


<td>

<?php

echo $row['capacity'];

?>

seats

</td>


<td>


<?php

$status = strtolower(
    $row['status']
);


if($status == "active")
{

    echo '<span class="status-badge status-approved">
          🟢 Active
          </span>';

}

elseif(strpos($status,"pending") !== false)
{

    echo '<span class="status-badge status-pending">
          🟡 Pending Approval
          </span>';

}

elseif($status == "maintenance")
{

    echo '<span class="status-badge status-warning">
          🟠 Maintenance
          </span>';

}

elseif($status == "rejected")
{

    echo '<span class="status-badge status-rejected">
          🔴 Rejected
          </span>';

}

else
{

    echo '<span class="status-badge">';

    echo htmlspecialchars(
        $row['status']
    );

    echo '</span>';

}

?>


</td>


<td>


<div class="table-actions">


<a class="btn btn-small btn-secondary"
   href="view.php?id=<?php echo $row['id']; ?>">

View

</a>


<a class="btn btn-small btn-primary"
   href="edit.php?id=<?php echo $row['id']; ?>">

Edit

</a>


</div>


</td>


</tr>


<?php

}

?>


</table>


</div>


<?php

}

else
{

?>


<!-- NO AIRCRAFT -->


<div class="empty-state">


<div class="empty-icon">

✈️

</div>


<h3>

No Aircraft Found

</h3>


<p>

You have not added any aircraft yet.

</p>


<a href="add.php"
   class="btn btn-primary">

+ Add Your First Aircraft

</a>


</div>


<?php

}

?>


</section>


<!-- APPROVAL INFORMATION -->


<section class="info-box">


<div class="info-icon">

ℹ️

</div>


<div>


<h3>

Aircraft Approval

</h3>


<p>

New aircraft and aircraft changes
must be approved by the airport authority.

</p>


<a href="../approval/index.php">

View Approval Requests →

</a>


</div>


</section>


</div>


</div>


</body>

</html>
```
