<?php
 session_start();

 if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}

 include "../config/database.php";

 $total_users = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users");
 $total_users = mysqli_fetch_assoc($total_users)['total'];

 $total_passengers = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='passenger'");
 $total_passengers = mysqli_fetch_assoc($total_passengers)['total'];

$total_staff = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='staff'");
$total_staff = mysqli_fetch_assoc($total_staff)['total'];

$total_airlines = mysqli_query($conn,"SELECT COUNT(*) AS total FROM users WHERE role='airline'");
$total_airlines = mysqli_fetch_assoc($total_airlines)['total'];

$total_services = mysqli_query($conn,"SELECT COUNT(*) AS total FROM airport_services");
$total_services = mysqli_fetch_assoc($total_services)['total'];


$total_activities = mysqli_query($conn,"SELECT COUNT(*) AS total FROM activity_logs");
$total_activities = mysqli_fetch_assoc($total_activities)['total'];

?>


<!DOCTYPE html>
<html>

   <head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/admin.css">

 </head>

<body>
   <div class="admin-container">

    <?php include "../includes/admin_sidebar.php"; ?>

    <div class="main-content">

        <h1>Admin Dashboard</h1>

        <p>Welcome back, Admin</p>

        
        
<div class="cards">


<div class="card">

<h3>Total Users</h3>

<div class="number">
<?php echo $total_users; ?>
</div>

</div>



<div class="card">

<h3>Passengers</h3>

<div class="number">
<?php echo $total_passengers; ?>
</div>

</div>



<div class="card">

<h3>Airport Staff</h3>

<div class="number">
<?php echo $total_staff; ?>
</div>

</div>



<div class="card">

<h3>Airlines</h3>

<div class="number">
<?php echo $total_airlines; ?>
</div>

</div>


<div class="card">

<h3>Airport Services</h3>

<div class="number">
<?php echo $total_services; ?>
</div>

</div>



<div class="card">

<h3>System Activities</h3>

<div class="number">
<?php echo $total_activities; ?>
</div>

</div>



</div>


</div>


    </div>

</div>

</body>
</html>
