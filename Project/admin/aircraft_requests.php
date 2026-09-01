<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}


$sql = "SELECT aircraft_approval_requests.*, airlines.airline_name 
FROM aircraft_approval_requests
JOIN airlines 
ON aircraft_approval_requests.airline_id = airlines.id
ORDER BY aircraft_approval_requests.id DESC";

$result = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>
<html>

<head>

<title>Aircraft Approval</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Aircraft Approval</h1>


<br><br>


<div class="table-card">


<table class="data-table">


<tr>

<th>ID</th>

<th>Airline Name</th>

<th>Aircraft Model</th>

<th>Registration</th>

<th>Capacity</th>

<th>Status</th>

<th>Action</th>

</tr>



<?php while($row=mysqli_fetch_assoc($result)){ ?>


<tr>


<td>
<?php echo $row['id']; ?>
</td>


<td>
<?php echo $row['airline_name']; ?>
</td>


<td>
<?php echo $row['proposed_model']; ?>
</td>


<td>
<?php echo $row['aircraft_id']; ?>
</td>


<td>
<?php echo $row['proposed_capacity']; ?>
</td>


<td>
<?php echo $row['status']; ?>
</td>



<td>


<?php if(strtolower($row['status'])=="pending"){ ?>


<a class="edit-btn"
href="approve_aircraft.php?id=<?php echo $row['id']; ?>">
Approve
</a>


<a class="delete-btn"
href="reject_aircraft.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Reject this request?')">
Reject
</a>


<?php } else { ?>


Completed


<?php } ?>


</td>


</tr>


<?php } ?>

</table>

</div>

</div>

</div>

</body>
</html>