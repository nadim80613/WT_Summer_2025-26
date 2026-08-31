<?php

session_start();

include "../config/database.php";


if(isset($_POST['submit'])){


    $type = $_POST['service_type'];
    $name = $_POST['service_name'];
    $description = $_POST['description'];
    $status = $_POST['status'];


    $sql = "INSERT INTO airport_services
    (service_type, service_name, description, status)
    VALUES
    ('$type','$name','$description','$status')";


    mysqli_query($conn,$sql);



    $log_sql = "INSERT INTO activity_logs
    (user_id, action)
    VALUES
    ('".$_SESSION['user_id']."',
    'Added airport service: ".$name."')";


    mysqli_query($conn,$log_sql);



    header("Location: airport_services.php");
    exit();

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Add Service</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Add Airport Service</h1>


<div class="form-card">


<form method="POST">


<label>Service Type</label>

<select name="service_type">

<option value="Terminal">Terminal</option>

<option value="Gate">Gate</option>

<option value="Facility">Facility</option>

</select>



<label>Service Name</label>

<input type="text" name="service_name" required>



<label>Description</label>

<textarea name="description"></textarea>



<label>Status</label>

<select name="status">

<option value="Active">Active</option>

<option value="Inactive">Inactive</option>

</select>



<div class="form-buttons">

<button class="save-btn" name="submit">
Add Service
</button>


<a class="back-btn" href="airport_services.php">
Back
</a>

</div>

</form>


</div>

</div>

</div>

</body>
</html>