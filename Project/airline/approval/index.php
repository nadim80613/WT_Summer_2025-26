<?php

session_start();

require_once '../../config/database.php';


/*
CHECK LOGIN
*/

if (!isset($_SESSION['user_id']))
{
    header("Location: ../../login.php");
    exit();
}


/*
CHECK AIRLINE ROLE
*/

if ($_SESSION['role'] != "airline")
{
    header("Location: ../../index.php");
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

$sql = "SELECT email
        FROM users
        WHERE id='$user_id'";

$result = mysqli_query($conn, $sql);

$user = mysqli_fetch_assoc($result);

$user_email = $user['email'];


/*
GET AIRLINE INFORMATION
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
GET APPROVAL REQUESTS
*/

$sql = "SELECT *
        FROM aircraft_approval_requests
        WHERE airline_id='$airline_id'
        ORDER BY id DESC";


$result = mysqli_query($conn, $sql);

?>


<!DOCTYPE html>

<html>

<head>

<title>Approval Requests</title>

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

Approval Requests

</h1>

<p>

Track your aircraft approval requests.

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

Aircraft Approval Requests

</h2>


<p>

Track requests submitted to the airport authority.

</p>

</div>


<a href="submit.php"
   class="btn btn-primary">

+ New Request

</a>


</section>


<!-- APPROVAL TABLE -->

<section class="content-card">


<div class="table-header">


<h2>

My Approval Requests

</h2>


<p>

Requests submitted for your aircraft.

</p>


</div>


<?php

if (mysqli_num_rows($result) > 0)
{

?>


<div class="table-container">


<table class="data-table">


<tr>

<th>ID</th>

<th>Aircraft</th>

<th>Request Type</th>

<th>Submitted</th>

<th>Status</th>

<th>Action</th>

</tr>


<?php

while ($row = mysqli_fetch_assoc($result))
{

?>


<tr>


<!-- ID -->

<td>

<strong>

#<?php

echo $row['id'];

?>

</strong>

</td>


<!-- AIRCRAFT -->

<td>

<?php

echo htmlspecialchars(
    $row['proposed_model'] ?? 'N/A'
);

?>

</td>


<!-- REQUEST TYPE -->

<td>

<?php

$type = $row['request_type'] ?? 'request';

$type = str_replace(
    '_',
    ' ',
    $type
);

echo htmlspecialchars(
    ucwords($type)
);

?>

</td>


<!-- DATE -->

<td>

<?php

echo htmlspecialchars(
    $row['submitted_at'] ?? 'N/A'
);

?>

</td>


<!-- STATUS -->

<td>


<?php

$status = strtolower(
    trim(
        $row['status'] ?? ''
    )
);


if ($status == "approved")
{

    echo '<span class="status-badge status-approved">
          🟢 Approved
          </span>';

}

elseif ($status == "rejected")
{

    echo '<span class="status-badge status-rejected">
          🔴 Rejected
          </span>';

}

else
{

    echo '<span class="status-badge status-pending">
          🟡 Pending
          </span>';

}

?>


</td>


<!-- ACTION -->

<td>


<a href="view.php?id=<?php echo $row['id']; ?>"
   class="btn btn-small btn-secondary">

View

</a>


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


<!-- NO REQUESTS -->

<div class="empty-state">


<div class="empty-icon">

📋

</div>


<h3>

No Approval Requests

</h3>


<p>

You have not submitted any aircraft approval requests yet.

</p>


<a href="../airplane/add.php"
   class="btn btn-primary">

+ Add Aircraft

</a>


</div>


<?php

}

?>


</section>


<!-- INFORMATION BOX -->

<section class="info-box">


<div class="info-icon">

ℹ️

</div>


<div>


<h3>

About Aircraft Approval

</h3>


<p>

New aircraft and aircraft information changes
must be approved by the airport authority.

</p>


</div>


</section>


</div>


</div>


</body>

</html>