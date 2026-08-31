<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}



if(isset($_POST['submit'])){


    $airline_name = $_POST['airline_name'];
    $model = $_POST['model'];
    $registration_number = $_POST['registration_number'];
    $capacity = $_POST['capacity'];
    $status = $_POST['status'];



    $sql = "INSERT INTO airplanes
    (airline_name, model, registration_number, capacity, status)

    VALUES

    ('$airline_name',
     '$model',
     '$registration_number',
     '$capacity',
     '$status')";


    mysqli_query($conn,$sql);



    $log_sql = "INSERT INTO activity_logs
    (user_id, action)

    VALUES

    ('".$_SESSION['user_id']."',
    'Added new airline: ".$airline_name."')";


    mysqli_query($conn,$log_sql);



    header("Location: airlines.php");
    exit();

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Add Airline</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Add Airline</h1>


<div class="form-card">


<form method="POST">


<label>Airline Name</label>

<input type="text" name="airline_name" required>



<label>Aircraft Model</label>

<input type="text" name="model" required>



<label>Registration Number</label>

<input type="text" name="registration_number" required>



<label>Capacity</label>

<input type="number" name="capacity" required>



<label>Status</label>

<select name="status">

<option value="Active">
Active
</option>

<option value="Pending">
Pending
</option>

<option value="Rejected">
Rejected
</option>

</select>



<div class="form-buttons">


<button class="save-btn" type="submit" name="submit">

Add Airline

</button>


<a class="back-btn" href="airlines.php">

Back

</a>

</div>

</form>

    </div>


    </div>

 </div>

</body>
</html>