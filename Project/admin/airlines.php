<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}


$sql = "SELECT * FROM airplanes ORDER BY id DESC";

$result = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>
<html>

<head>

<title>Airline Management</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Airline Management</h1>

<br> <br>
<a class="save-btn" href="add_airline.php">
Add Airline
</a>


<div class="table-card">


<table class="data-table">


<tr>

<th>ID</th>

<th>Airline Name</th>

<th>Model</th>

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
<?php echo $row['model']; ?>
</td>


<td>
<?php echo $row['registration_number']; ?>
</td>


<td>
<?php echo $row['capacity']; ?>
</td>


<td>
<?php echo $row['status']; ?>
</td>


<td>

<a class="edit-btn" href="edit_airline.php?id=<?php echo $row['id']; ?>">
Edit
</a>


<a class="delete-btn"
href="delete_airline.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this airline?')">
Delete
</a>


</td>


</tr>


<?php } ?>


</table>


</div>
</div>

</div>

</body>
</html>