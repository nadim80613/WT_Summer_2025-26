<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}



$id = $_GET['id'];



$result = mysqli_query($conn,
"SELECT * FROM airport_services WHERE id=$id"
);


$service = mysqli_fetch_assoc($result);



if(isset($_POST['submit'])){


    $type = $_POST['service_type'];
    $name = $_POST['service_name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $status = $_POST['status'];



    $sql = "UPDATE airport_services SET

    service_type='$type',
    service_name='$name',
    description='$description',
    location='$location',
    status='$status'

    WHERE id=$id";



    mysqli_query($conn,$sql);



    $log_sql = "INSERT INTO activity_logs
    (user_id, action)

    VALUES

    ('".$_SESSION['user_id']."',
    'Updated airport service: ".$name."')";



    mysqli_query($conn,$log_sql);



    header("Location: airport_services.php");
    exit();

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Edit Service</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Edit Airport Service</h1>


<div class="form-card">


<form method="POST">


<label>Service Type</label>

<select name="service_type">


<option value="Terminal"
<?php if($service['service_type']=="Terminal") echo "selected"; ?>>
Terminal
</option>


<option value="Gate"
<?php if($service['service_type']=="Gate") echo "selected"; ?>>
Gate
</option>


<option value="Facility"
<?php if($service['service_type']=="Facility") echo "selected"; ?>>
Facility
</option>


</select>



<label>Service Name</label>

<input type="text"
name="service_name"
value="<?php echo $service['service_name']; ?>"
required>



<label>Description</label>

<textarea name="description"><?php echo $service['description']; ?></textarea>



<label>Location</label>

<input type="text"
name="location"
value="<?php echo $service['location']; ?>"
required>



<label>Status</label>

<select name="status">


<option value="Active"
<?php if($service['status']=="Active") echo "selected"; ?>>
Active
</option>


<option value="Inactive"
<?php if($service['status']=="Inactive") echo "selected"; ?>>
Inactive
</option>


</select>



<div class="form-buttons">


<button class="save-btn" name="submit">
Update
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