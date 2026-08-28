<?php
 session_start();
 if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}
  include "../config/database.php";

?>


<!DOCTYPE html>
<html>

   <head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../assets/css/admin.css">

 </head>

<body>
   <div class="admin-container">

    <div class="sidebar">

        <h2>Airport</h2>

        <ul>
          <li>Dashboard</li>
        <li>Manage Users</li>
        <li>System Monitor</li>
        <li>Reports</li>
        <li>Airline Management</li>
        <li>Airport Services</li>
        <li>Logout</li>

        </ul>

    </div>


    <div class="main-content">

        <h1>Admin Dashboard</h1>

        <p>Welcome back, Admin</p>

        
        
        <div class="cards">


    <div class="card">

        <h3>Total Users</h3>
        <p>0</p>

    </div>


    <div class="card">

        <h3>Passengers</h3>
        <p>0</p>

    </div>


    <div class="card">

        <h3>Airport Staff</h3>
        <p>0</p>

    </div>


    <div class="card">

        <h3>Airlines</h3>
        <p>0</p>

    </div>


</div>


    </div>

</div>

</body>
</html>
