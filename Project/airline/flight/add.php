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

$errors=[];

$flight_number='';
$departure='';
$destination='';
$departure_time='';
$arrival_time='';
$airplane_id='';


/* Get aircraft */

$stmt=$conn->prepare(
    "SELECT id,model,registration_number,capacity
     FROM airplanes
     WHERE airline_id=?
     AND LOWER(status)='active'"
);

$stmt->bind_param('i',$airline_id);
$stmt->execute();

$aircraft_result=$stmt->get_result();


/* Add flight */

if($_SERVER['REQUEST_METHOD']==='POST'){

    $flight_number=trim($_POST['flight_number']??'');
    $departure=trim($_POST['departure']??'');
    $destination=trim($_POST['destination']??'');
    $departure_time=$_POST['departure_time']??'';
    $arrival_time=$_POST['arrival_time']??'';
    $airplane_id=(int)($_POST['airplane_id']??0);

    if($flight_number===''){
        $errors[]='Flight number is required.';
    }

    if($departure===''){
        $errors[]='Departure is required.';
    }

    if($destination===''){
        $errors[]='Arrival is required.';
    }

    if($departure_time==='' || $arrival_time===''){
        $errors[]='Date and time are required.';
    }

    if($airplane_id<=0){
        $errors[]='Please select an aircraft.';
    }

    if(empty($errors)){

        $stmt=$conn->prepare(
            "SELECT capacity
             FROM airplanes
             WHERE id=?
             AND airline_id=?
             AND LOWER(status)='active'"
        );

        $stmt->bind_param(
            'ii',
            $airplane_id,
            $airline_id
        );

        $stmt->execute();

        $aircraft=$stmt->get_result()->fetch_assoc();

        if(!$aircraft){

            $errors[]='Invalid aircraft selected.';

        }else{

            $capacity=$aircraft['capacity'];

            $status='Scheduled';

            $stmt=$conn->prepare(
                "INSERT INTO flights
                (flight_number,airplane_id,airline_id,departure,
                 destination,departure_time,arrival_time,status,
                 total_seats,available_seats)
                VALUES(?,?,?,?,?,?,?,?,?,?)"
            );

            $stmt->bind_param(
                'siisssssii',
                $flight_number,
                $airplane_id,
                $airline_id,
                $departure,
                $destination,
                $departure_time,
                $arrival_time,
                $status,
                $capacity,
                $capacity
            );

            if($stmt->execute()){
                header('Location: index.php?success=added');
                exit();
            }

            $errors[]='Could not add flight.';
        }
    }
}

?>


<!DOCTYPE html>
<html>

<head>

<title>Add Flight</title>

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


<a href="../dashboard.php" class="menu-item">
<span>📊</span>
Dashboard
</a>


<div class="menu-section">

<p class="menu-title">Aircraft</p>

<a href="../airplane/index.php" class="menu-item">
<span>✈️</span>
My Aircraft
</a>

<a href="../airplane/add.php" class="menu-item">
<span>➕</span>
Add Aircraft
</a>

<a href="../approval/index.php" class="menu-item">
<span>📋</span>
Approval Requests
</a>

</div>


<div class="menu-section">

<p class="menu-title">Flights</p>

<a href="index.php" class="menu-item">
<span>🛫</span>
My Flights
</a>

<a href="add.php" class="menu-item active">
<span>➕</span>
Add Flight
</a>

<a href="availability.php" class="menu-item">
<span>💺</span>
Seat Availability
</a>

<a href="schedule_request.php" class="menu-item">
<span>🕐</span>
Schedule Requests
</a>

</div>


<div class="menu-section">

<p class="menu-title">Account</p>

<a href="../../logout.php" class="menu-item logout-item">
<span>🚪</span>
Logout
</a>

</div>

</nav>

</div>


<div class="main-content">


<header class="topbar">

<div>

<h1>Add Flight</h1>
<p>Create a new flight.</p>

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

<h2>Flight Information</h2>
<p>Enter the flight details.</p>

</div>

<a href="index.php" class="btn btn-secondary">
← Back
</a>

</section>


<?php if(!empty($errors)): ?>

<div class="alert alert-error">

<ul>

<?php foreach($errors as $error): ?>

<li><?php echo htmlspecialchars($error); ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>


<section class="content-card">


<form method="POST" class="airline-form">


<div class="form-group">

<label>Flight Number *</label>

<input
type="text"
name="flight_number"
value="<?php echo htmlspecialchars($flight_number); ?>"
placeholder="Example: BG101"
required
>

</div>


<div class="form-group">

<label>Departure Location *</label>

<input
type="text"
name="departure"
value="<?php echo htmlspecialchars($departure); ?>"
placeholder="Example: Dhaka"
required
>

</div>


<div class="form-group">

<label>Arrival Location *</label>

<input
type="text"
name="destination"
value="<?php echo htmlspecialchars($destination); ?>"
placeholder="Example: Chittagong"
required
>

</div>


<div class="form-group">

<label>Departure Time *</label>

<input
type="datetime-local"
name="departure_time"
value="<?php echo htmlspecialchars($departure_time); ?>"
required
>

</div>


<div class="form-group">

<label>Arrival Time *</label>

<input
type="datetime-local"
name="arrival_time"
value="<?php echo htmlspecialchars($arrival_time); ?>"
required
>

</div>


<div class="form-group">

<label>Aircraft *</label>

<select name="airplane_id" required>

<option value="">
-- Select Aircraft --
</option>

<?php while($aircraft=$aircraft_result->fetch_assoc()): ?>

<option value="<?php echo $aircraft['id']; ?>">

<?php echo htmlspecialchars(
    $aircraft['model'].' - '.
    $aircraft['registration_number'].' ('.
    $aircraft['capacity'].' seats)'
); ?>

</option>

<?php endwhile; ?>

</select>

</div>


<div class="form-actions">

<a href="index.php" class="btn btn-secondary">
Cancel
</a>

<button type="submit" class="btn btn-primary">
    Add Flight
</button>
</div>
</form>
</section>
</div>
</div>
</body>
</html>