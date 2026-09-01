<?php

session_start();

require_once '../../config/database.php';


if(!isset($_SESSION['user_id']))
{
    header('Location: ../../login.php');
    exit();
}


if(strtolower(trim($_SESSION['role'] ?? '')) !== 'airline')
{
    header('Location: ../../index.php');
    exit();
}


$airline_id=(int)$_SESSION['user_id'];

$airline_name=$_SESSION['user_name'] ??
$_SESSION['name'] ?? 'Airline';


/*
GET FLIGHT
*/

$id=(int)($_GET['id'] ?? $_POST['id'] ?? 0);

$errors=[];


$stmt=$conn->prepare(
    'SELECT *
     FROM flights
     WHERE id=?
     AND airline_id=?
     LIMIT 1'
);


$stmt->bind_param(
    'ii',
    $id,
    $airline_id
);


$stmt->execute();

$flight=$stmt->get_result()->fetch_assoc();

$stmt->close();


if(!$flight)
{
    header('Location: index.php');
    exit();
}


/*
FLIGHT INFORMATION
*/

$flight_number=$flight['flight_number'];

$departure=$flight['departure'];

$destination=$flight['destination'];

$departure_time=date(
    'Y-m-d\TH:i',
    strtotime($flight['departure_time'])
);

$arrival_time=date(
    'Y-m-d\TH:i',
    strtotime($flight['arrival_time'])
);

$airplane_id=(int)$flight['airplane_id'];

$status=$flight['status'];


/*
GET AIRCRAFT
*/

$stmt=$conn->prepare(
    'SELECT id,
            model,
            registration_number,
            capacity
     FROM airplanes
     WHERE airline_id=?
     AND LOWER(status)=\'active\''
);


$stmt->bind_param(
    'i',
    $airline_id
);


$stmt->execute();

$aircraft_result=$stmt->get_result();


/*
UPDATE FLIGHT
*/

if($_SERVER['REQUEST_METHOD']==='POST')
{

    $flight_number=trim(
        $_POST['flight_number'] ?? ''
    );

    $departure=trim(
        $_POST['departure'] ?? ''
    );

    $destination=trim(
        $_POST['destination'] ?? ''
    );

    $departure_time=trim(
        $_POST['departure_time'] ?? ''
    );

    $arrival_time=trim(
        $_POST['arrival_time'] ?? ''
    );

    $airplane_id=(int)(
        $_POST['airplane_id'] ?? 0
    );

    $status=trim(
        $_POST['status'] ?? 'Scheduled'
    );


    /*
    VALIDATION
    */

    if($flight_number==='')
    {
        $errors[]='Flight number is required.';
    }


    if($departure==='')
    {
        $errors[]='Departure is required.';
    }


    if($destination==='')
    {
        $errors[]='Arrival is required.';
    }


    if($departure_time==='' || $arrival_time==='')
    {
        $errors[]='Date and time are required.';
    }


    if($airplane_id<=0)
    {
        $errors[]='Aircraft is required.';
    }


    if(
        $departure_time!=='' &&
        $arrival_time!=='' &&
        strtotime($arrival_time)<=strtotime($departure_time)
    )
    {
        $errors[]=
            'Arrival must be after departure.';
    }


    /*
    UPDATE
    */

    if(empty($errors))
    {

        $stmt=$conn->prepare(
            'UPDATE flights
             SET flight_number=?,
                 airplane_id=?,
                 departure=?,
                 destination=?,
                 departure_time=?,
                 arrival_time=?,
                 status=?
             WHERE id=?
             AND airline_id=?'
        );


        $stmt->bind_param(
            'sisssssii',
            $flight_number,
            $airplane_id,
            $departure,
            $destination,
            $departure_time,
            $arrival_time,
            $status,
            $id,
            $airline_id
        );


        if($stmt->execute())
        {

            $stmt->close();

            header(
                'Location: view.php?id='.$id
            );

            exit();

        }


        $errors[]='Failed to update flight.';

        $stmt->close();

    }

}

?>


<!DOCTYPE html>

<html>


<head>

<title>Edit Flight</title>

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

<h1>

Edit Flight

</h1>

<p>

Update flight information.

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


<section class="page-header">


<div>

<h2>

Edit Flight

</h2>


<p>

Update the details of this flight.

</p>


</div>


<a href="view.php?id=<?php echo $id; ?>"
   class="btn btn-secondary">

← Back

</a>


</section>


<?php

if(!empty($errors))
{

?>


<div class="alert alert-error">


<strong>

Please fix the following:

</strong>


<ul>


<?php

foreach($errors as $error)
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


<section class="content-card">


<form method="POST"
      class="airline-form">


<input
type="hidden"
name="id"
value="<?php echo $id; ?>"
>


<div class="form-group">


<label>

Flight Number

<span class="required">*</span>

</label>


<input
type="text"
name="flight_number"
value="<?php echo htmlspecialchars($flight_number); ?>"
required
>


</div>


<div class="form-group">


<label>

Departure

<span class="required">*</span>

</label>


<input
type="text"
name="departure"
value="<?php echo htmlspecialchars($departure); ?>"
required
>


</div>


<div class="form-group">


<label>

Arrival

<span class="required">*</span>

</label>


<input
type="text"
name="destination"
value="<?php echo htmlspecialchars($destination); ?>"
required
>


</div>


<div class="form-group">


<label>

Departure Time

<span class="required">*</span>

</label>


<input
type="datetime-local"
name="departure_time"
value="<?php echo htmlspecialchars($departure_time); ?>"
required
>


</div>


<div class="form-group">


<label>

Arrival Time

<span class="required">*</span>

</label>


<input
type="datetime-local"
name="arrival_time"
value="<?php echo htmlspecialchars($arrival_time); ?>"
required
>


</div>


<div class="form-group">


<label>

Aircraft

<span class="required">*</span>

</label>


<select name="airplane_id"
        required>


<?php

while($a=$aircraft_result->fetch_assoc())
{

?>


<option
value="<?php echo (int)$a['id']; ?>"
<?php

if($airplane_id==$a['id'])
{
    echo 'selected';
}

?>
>

<?php

echo htmlspecialchars(
    $a['model'] .
    ' - ' .
    $a['registration_number'] .
    ' (' .
    $a['capacity'] .
    ' seats)'
);

?>

</option>


<?php

}

?>


</select>


</div>


<div class="form-group">


<label>

Status

</label>


<select name="status">


<option
<?php

if($status==='Scheduled')
{
    echo 'selected';
}

?>
>

Scheduled

</option>


<option
<?php

if($status==='Delayed')
{
    echo 'selected';
}

?>
>

Delayed

</option>


<option
<?php

if($status==='Cancelled')
{
    echo 'selected';
}

?>
>

Cancelled

</option>


<option
<?php

if($status==='Completed')
{
    echo 'selected';
}

?>
>

Completed

</option>


</select>


</div>


<div class="form-actions">


<a href="index.php"
   class="btn btn-secondary">

Cancel

</a>


<button
type="submit"
class="btn btn-primary"
>

Save Changes

</button>


</div>


</form>


</section>


</div>


</div>


</body>

</html>
