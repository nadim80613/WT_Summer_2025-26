<?php

session_start();
require_once '../../config/database.php';


/* LOGIN CHECK */

if(!isset($_SESSION['user_id'])){
    header('Location: ../../login.php');
    exit();
}

if($_SESSION['role'] != 'airline'){
    header('Location: ../../index.php');
    exit();
}


/* GET AIRLINE ID */

$user_id = $_SESSION['user_id'];

$sql = "SELECT a.id, a.airline_name
        FROM users u
        JOIN airlines a ON u.email = a.email
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$airline = $stmt->get_result()->fetch_assoc();

$airline_id = $airline['id'];
$airline_name = $airline['airline_name'];

$stmt->close();


$errors = [];


/* FORM VALUES */

$flight_id = $_POST['flight_id'] ?? '';
$requested_departure = $_POST['requested_departure'] ?? '';
$requested_arrival = $_POST['requested_arrival'] ?? '';
$reason = $_POST['reason'] ?? '';


/* SUBMIT REQUEST */

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if($flight_id == ''){
        $errors[] = "Please select a flight.";
    }

    if($requested_departure == ''){
        $errors[] = "Requested departure is required.";
    }

    if($requested_arrival == ''){
        $errors[] = "Requested arrival is required.";
    }

    if($reason == ''){
        $errors[] = "Reason is required.";
    }


    /* SAVE */

    if(empty($errors)){

        $sql = "INSERT INTO flight_schedule_requests
                (flight_id, airline_id, requested_departure,
                 requested_arrival, reason, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iisss",
            $flight_id,
            $airline_id,
            $requested_departure,
            $requested_arrival,
            $reason
        );

        if($stmt->execute()){

            header("Location: schedule_request.php?success=1");
            exit();

        }else{

            $errors[] = "Failed to submit request.";

        }

        $stmt->close();
    }
}


/* GET FLIGHTS */

$sql = "SELECT f.id, f.flight_number,
               f.departure, f.destination
        FROM flights f
        JOIN airplanes a ON f.airplane_id = a.id
        WHERE a.airline_id = ?
        ORDER BY f.departure_time";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $airline_id);
$stmt->execute();

$flights = $stmt->get_result();

$stmt->close();


/* GET REQUESTS */

$sql = "SELECT r.*, f.flight_number
        FROM flight_schedule_requests r
        JOIN flights f ON r.flight_id = f.id
        WHERE r.airline_id = ?
        ORDER BY r.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $airline_id);
$stmt->execute();

$requests = $stmt->get_result();

$stmt->close();

?>


<!DOCTYPE html>

<html>

<head>

<title>Schedule Requests</title>

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

📊 Dashboard

</a>


<div class="menu-section">

<p class="menu-title">Aircraft</p>


<a href="../airplane/index.php"
   class="menu-item">

✈️ My Aircraft

</a>


<a href="../airplane/add.php"
   class="menu-item">

➕ Add Aircraft

</a>


<a href="../approval/index.php"
   class="menu-item">

📋 Approval Requests

</a>

</div>


<div class="menu-section">

<p class="menu-title">Flights</p>


<a href="index.php"
   class="menu-item">

🛫 My Flights

</a>


<a href="add.php"
   class="menu-item">

➕ Add Flight

</a>


<a href="availability.php"
   class="menu-item">

💺 Seat Availability

</a>


<a href="schedule_request.php"
   class="menu-item active">

🕐 Schedule Requests

</a>

</div>


<div class="menu-section">

<p class="menu-title">Account</p>


<a href="../../logout.php"
   class="menu-item logout-item">

🚪 Logout

</a>

</div>


</nav>

</div>


<!-- MAIN CONTENT -->

<div class="main-content">


<header class="topbar">

<div>

<h1>Schedule Requests</h1>

<p>Request changes to flight schedules.</p>

</div>


<div class="user-info">

<div class="user-avatar">

<?php echo strtoupper(substr($airline_name,0,1)); ?>

</div>


<div>

<strong>

<?php echo htmlspecialchars($airline_name); ?>

</strong>

<small>Airline</small>

</div>

</div>

</header>


<section class="page-header">

<div>

<h2>Schedule Requests</h2>

<p>Request a change to your flight schedule.</p>

</div>

</section>


<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">

Schedule change request submitted successfully.

</div>

<?php endif; ?>


<?php if(!empty($errors)): ?>

<div class="alert alert-error">

<ul>

<?php foreach($errors as $error): ?>

<li>

<?php echo htmlspecialchars($error); ?>

</li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>


<!-- FORM -->

<section class="content-card">


<div class="form-header">

<h2>Submit Schedule Change</h2>

<p>Enter the new schedule you are requesting.</p>

</div>


<form method="POST"
      class="airline-form">


<div class="form-group">

<label>Flight *</label>

<select name="flight_id" required>

<option value="">-- Select Flight --</option>


<?php while($f = $flights->fetch_assoc()): ?>

<option value="<?php echo $f['id']; ?>">

<?php

echo htmlspecialchars(
    $f['flight_number']
    . " - "
    . $f['departure']
    . " → "
    . $f['destination']
);

?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-group">

<label>Requested Departure *</label>

<input type="datetime-local"
       name="requested_departure"
       value="<?php echo htmlspecialchars($requested_departure); ?>"
       required>

</div>


<div class="form-group">

<label>Requested Arrival *</label>

<input type="datetime-local"
       name="requested_arrival"
       value="<?php echo htmlspecialchars($requested_arrival); ?>"
       required>

</div>


<div class="form-group">

<label>Reason *</label>

<textarea name="reason"
          rows="4"
          required
          placeholder="Why do you want to change the schedule?"><?php

echo htmlspecialchars($reason);

?></textarea>

</div>


<div class="form-actions">

<button type="submit"
        class="btn btn-primary">

🕐 Submit Request

</button>

</div>


</form>

</section>


<!-- REQUEST LIST -->

<section class="content-card">


<div class="table-header">

<h2>My Schedule Requests</h2>

<p>View your submitted requests.</p>

</div>


<div class="table-container">


<table class="data-table">


<thead>

<tr>

<th>ID</th>

<th>Flight</th>

<th>Requested Departure</th>

<th>Requested Arrival</th>

<th>Status</th>

<th>Feedback</th>

</tr>

</thead>


<tbody>


<?php if($requests->num_rows > 0): ?>


<?php while($r = $requests->fetch_assoc()): ?>


<tr>


<td>

#<?php echo $r['id']; ?>

</td>


<td>

<?php echo htmlspecialchars($r['flight_number']); ?>

</td>


<td>

<?php echo htmlspecialchars($r['requested_departure']); ?>

</td>


<td>

<?php echo htmlspecialchars($r['requested_arrival']); ?>

</td>


<td>


<?php

$status = strtolower($r['status']);

if($status == 'approved'){

    echo '<span class="status-badge status-approved">
          🟢 Approved
          </span>';

}
elseif($status == 'rejected'){

    echo '<span class="status-badge status-rejected">
          🔴 Rejected
          </span>';

}
else{

    echo '<span class="status-badge status-pending">
          🟡 Pending
          </span>';

}

?>

</td>


<td>

<?php

echo htmlspecialchars(
    $r['feedback'] ?? 'Pending review'
);

?>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="6"
    style="text-align:center">

No schedule requests found.

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