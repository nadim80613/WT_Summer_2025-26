<?php

session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}

include "../config/database.php";


$sql = "SELECT * FROM users";

$result = mysqli_query($conn,$sql);

?>


<!DOCTYPE html>
<html>

<head>

<title>Manage Users</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Manage Users</h1>

<br>

<a href="dashboard.php" class="back-btn">
 Back to Dashboard
</a>


<a class="add-btn" href="add_user.php">
Add User
</a>


<br>


<table class="user-table">


<tr>

<th>ID</th>

<th>Name</th>

<th>Email</th>

<th>Role</th>

<th>Action</th>

</tr>



<?php


while($row = mysqli_fetch_assoc($result))

{


?>


<tr>

<td>
<?php echo $row['id']; ?>
</td>


<td>
<?php echo $row['name']; ?>
</td>


<td>
<?php echo $row['email']; ?>
</td>


<td>
<?php echo $row['role']; ?>
</td>


<td>

<a class='edit-btn' href="edit_user.php?id=<?php echo $row['id']; ?>">
Edit
</a>


<a class='delete-btn' href="delete_user.php?id=<?php echo $row['id']; ?>">
Delete
</a>


</td>


</tr>


<?php

}

?>


</table>


</div>


</div>


</body>


</html>