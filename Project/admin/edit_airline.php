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
"SELECT * FROM airlines WHERE id=$id"
);


$airline = mysqli_fetch_assoc($result);



if(isset($_POST['submit'])){


    $airline_name = $_POST['airline_name'];
    $country = $_POST['country'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $status = $_POST['status'];



    $sql = "UPDATE airlines SET

    airline_name='$airline_name',
    country='$country',
    contact='$contact',
    email='$email',
    status='$status'

    WHERE id=$id";



    mysqli_query($conn,$sql);



    $log_sql = "INSERT INTO activity_logs
    (user_id, action)

    VALUES

    ('".$_SESSION['user_id']."',
    'Updated airline information: ".$airline_name."')";


    mysqli_query($conn,$log_sql);



    header("Location: airlines.php");
    exit();

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Edit Airline</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Edit Airline</h1>


<div class="form-card">


<form method="POST">


<label>Airline Name</label>

<input type="text"
name="airline_name"
value="<?php echo $airline['airline_name']; ?>"
required>



<label>Country</label>

<input type="text"
name="country"
value="<?php echo $airline['country']; ?>">



<label>Contact</label>

<input type="text"
name="contact"
value="<?php echo $airline['contact']; ?>">



<label>Email</label>

<input type="email"
name="email"
value="<?php echo $airline['email']; ?>">



<label>Status</label>

<select name="status">


<option value="Active"
<?php if($airline['status']=="Active") echo "selected"; ?>>
Active
</option>


<option value="Pending"
<?php if($airline['status']=="Pending") echo "selected"; ?>>
Pending
</option>


<option value="Rejected"
<?php if($airline['status']=="Rejected") echo "selected"; ?>>
Rejected
</option>


</select>



<div class="form-buttons">


<button class="save-btn" name="submit">
Update
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