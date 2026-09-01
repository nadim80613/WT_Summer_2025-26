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
    $country = $_POST['country'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $status = $_POST['status'];



    $sql = "INSERT INTO airlines
    (airline_name, country, contact, email, status)

    VALUES

    ('$airline_name',
     '$country',
     '$contact',
     '$email',
     '$status')";


    mysqli_query($conn,$sql);



    $log_sql = "INSERT INTO activity_logs
    (user_id, action)

    VALUES

    ('".$_SESSION['user_id']."',
    'Added airline information: ".$airline_name."')";


    mysqli_query($conn,$log_sql);



    header("Location: airlines.php");
    exit();

}


?>


<!DOCTYPE html>
<html>

<head>

<title>Add Airline Information</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Add Airline Information</h1>


<div class="form-card">


<form method="POST">


<label>Airline Name</label>

<input 
type="text" 
name="airline_name" 
required>



<label>Country</label>

<input 
type="text" 
name="country" 
required>



<label>Contact</label>

<input 
type="text" 
name="contact">



<label>Email</label>

<input 
type="email" 
name="email">



<label>Status</label>

<select name="status">


<option value="Active">
Active
</option>


<option value="Inactive">
Inactive
</option>


</select>



<div class="form-buttons">


<button 
class="save-btn" 
type="submit" 
name="submit">

Add Airline

</button>



<a 
class="back-btn" 
href="airlines.php">

Back

</a>


</div>


     </form>

 
      </div>


   </div>

</div>


</body>
</html>  