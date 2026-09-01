```php
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


/*
GET USER INFORMATION
*/

$user_id = (int)$_SESSION['user_id'];

$airline_name = $_SESSION['user_name'] ??
$_SESSION['name'] ?? 'Airline';

$user_email = '';


/*
GET USER EMAIL
*/

$sql = "SELECT email
        FROM users
        WHERE id = ?
        LIMIT 1";


$stmt = $conn->prepare($sql);


if($stmt)
{
    $stmt->bind_param(
        'i',
        $user_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if($result->num_rows > 0)
    {
        $user = $result->fetch_assoc();

        $user_email = $user['email'];
    }


    $stmt->close();
}


/*
FIND AIRLINE
*/

$airline_id = 0;


$sql = "SELECT id,
               airline_name,
               email
        FROM airlines
        WHERE email = ?
        LIMIT 1";


$stmt = $conn->prepare($sql);


if($stmt)
{
    $stmt->bind_param(
        's',
        $user_email
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if($result->num_rows > 0)
    {
        $airline = $result->fetch_assoc();

        $airline_id = (int)$airline['id'];

        $airline_name = $airline['airline_name'];
    }


    $stmt->close();
}


/*
CHECK AIRLINE CONNECTION
*/

if($airline_id <= 0)
{
    die("Airline account is not connected to an airline record.");
}


/*
PAGE INFORMATION
*/

$page_title='Add Aircraft';

$current_page='aircraft';


/*
AIRCRAFT VARIABLES
*/

$model='';

$manufacturer='';

$registration_number='';

$capacity='';

$manufacturing_date='';

$errors=[];


/*
ADD AIRCRAFT
*/

if($_SERVER['REQUEST_METHOD']==='POST')
{

    $model=trim($_POST['model']??'');

    $manufacturer=trim($_POST['manufacturer']??'');

    $registration_number=trim($_POST['registration_number']??'');

    $capacity=trim($_POST['capacity']??'');

    $manufacturing_date=trim($_POST['manufacturing_date']??'');


    /*
    VALIDATION
    */

    if($model==='')
    {
        $errors[]='Aircraft model is required.';
    }


    if($manufacturer==='')
    {
        $errors[]='Manufacturer is required.';
    }


    if($registration_number==='')
    {
        $errors[]='Registration number is required.';
    }


    if(
        $capacity==='' ||
        !is_numeric($capacity) ||
        (int)$capacity<=0
    )
    {
        $errors[]='Capacity must be a valid positive number.';
    }


    if($manufacturing_date==='')
    {
        $errors[]='Manufacturing date is required.';
    }


    /*
    CHECK REGISTRATION NUMBER
    */

    if(empty($errors))
    {

        $stmt=$conn->prepare(
            'SELECT id
             FROM airplanes
             WHERE registration_number=?'
        );


        $stmt->bind_param(
            's',
            $registration_number
        );


        $stmt->execute();


        if($stmt->get_result()->num_rows>0)
        {
            $errors[]='This registration number already exists.';
        }


        $stmt->close();

    }


    /*
    INSERT AIRCRAFT
    */

    if(empty($errors))
    {

        $status='Pending Approval';

        $capacity_int=(int)$capacity;


        $conn->begin_transaction();


        try
        {


            /*
            INSERT AIRCRAFT
            */

            $stmt=$conn->prepare(
                'INSERT INTO airplanes
                (
                    airline_id,
                    airline_name,
                    model,
                    manufacturer,
                    manufacturing_date,
                    registration_number,
                    capacity,
                    status
                )
                VALUES
                (?,?,?,?,?,?,?,?)'
            );


            $stmt->bind_param(
                'isssssis',
                $airline_id,
                $airline_name,
                $model,
                $manufacturer,
                $manufacturing_date,
                $registration_number,
                $capacity_int,
                $status
            );


            $stmt->execute();


            $aircraft_id=$stmt->insert_id;


            $stmt->close();


            /*
            CREATE APPROVAL REQUEST
            */

            $description=
                'New aircraft submitted for airport authority approval.';


            $request_type='new_aircraft';

            $approval_status='pending';


            $stmt=$conn->prepare(
                'INSERT INTO aircraft_approval_requests
                (
                    aircraft_id,
                    airline_id,
                    request_type,
                    description,
                    proposed_model,
                    proposed_capacity,
                    proposed_manufacturer,
                    proposed_manufacturing_date,
                    status
                )
                VALUES
                (?,?,?,?,?,?,?,?,?)'
            );


            $stmt->bind_param(
                'iisssisss',
                $aircraft_id,
                $airline_id,
                $request_type,
                $description,
                $model,
                $capacity_int,
                $manufacturer,
                $manufacturing_date,
                $approval_status
            );


            $stmt->execute();


            $stmt->close();


            /*
            SAVE
            */

            $conn->commit();


            header('Location: index.php?success=added');

            exit();

        }
        catch(Exception $e)
        {

            $conn->rollback();

            $errors[]=
                'Could not add aircraft. Please try again.';

        }

    }

}

?>


<!DOCTYPE html>
<html>


<head>

<title>Add Aircraft</title>

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


<a href="index.php"
   class="menu-item">

<span>✈️</span>

My Aircraft

</a>


<a href="add.php"
   class="menu-item active">

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


<div class="main-content">


<header class="topbar">


<div>

<h1>

Add Aircraft

</h1>


<p>

Register a new aircraft.

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

Aircraft Information

</h2>


<p>

Enter the details of the aircraft.

</p>


</div>


<a href="index.php"
   class="btn btn-secondary">

← Back to Aircraft

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


<div class="form-header">


<h2>

Aircraft Profile

</h2>


<p>

All fields are required.

</p>


</div>


<form method="POST"
      class="airline-form">


<div class="form-group">


<label>

Aircraft Model

<span class="required">*</span>

</label>


<input
type="text"
name="model"
value="<?php echo htmlspecialchars($model); ?>"
placeholder="Example: Boeing 737-800"
required
>


</div>


<div class="form-group">


<label>

Manufacturer

<span class="required">*</span>

</label>


<input
type="text"
name="manufacturer"
value="<?php echo htmlspecialchars($manufacturer); ?>"
placeholder="Example: Boeing"
required
>


</div>


<div class="form-group">


<label>

Registration Number

<span class="required">*</span>

</label>


<input
type="text"
name="registration_number"
value="<?php echo htmlspecialchars($registration_number); ?>"
placeholder="Example: S2-ABC"
required
>


<small>

Registration number must be unique.

</small>


</div>


<div class="form-group">


<label>

Passenger Capacity

<span class="required">*</span>

</label>


<input
type="number"
name="capacity"
value="<?php echo htmlspecialchars($capacity); ?>"
min="1"
required
>


</div>


<div class="form-group">


<label>

Manufacturing Date

<span class="required">*</span>

</label>


<input
type="date"
name="manufacturing_date"
value="<?php echo htmlspecialchars($manufacturing_date); ?>"
required
>


</div>


<div class="form-notice">


<div class="notice-icon">

ℹ️

</div>


<div>


<strong>

Approval Required

</strong>


<p>

New aircraft will be submitted to the
airport authority for approval.

</p>


</div>


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

✈️ Add Aircraft

</button>


</div>


</form>


</section>


</div>


</div>


</body>

</html>
```
