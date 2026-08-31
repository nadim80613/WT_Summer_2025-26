<?php
session_start();

 if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}

include "../config/database.php";

$result = mysqli_query($conn,"SELECT * FROM airport_services ORDER BY id DESC");

?>


<!DOCTYPE html>
<html>

<head>

<title>Airport Services</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Airport Services</h1>

<br> <br>


<a class="add-btn" href="add_service.php">
Add Service
</a>



<table>


<tr>

<th>ID</th>
<th>Service Name</th>
<th>Description</th>
<th>Location</th>
<th>Status</th>
<th>Action</th>

</tr>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['service_name']; ?></td>

<td><?php echo $row['description']; ?></td>

<td><?php echo $row['location']; ?></td>

<td><?php echo $row['status']; ?></td>


<td>

<a class="edit-btn" href="edit_service.php?id=<?php echo $row['id']; ?>">
Edit
</a>


<a class="delete-btn" 
href="delete_service.php?id=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this service?')">
Delete
</a>

</td>

</tr>

<?php } ?>


</table>

</div>

</div>

</body>
</html>