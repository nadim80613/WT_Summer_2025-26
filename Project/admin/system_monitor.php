<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}



$sql = "
SELECT 
activity_logs.id,
users.name,
users.email,
activity_logs.action,
activity_logs.created_at

FROM activity_logs

JOIN users 
ON activity_logs.user_id = users.id

ORDER BY activity_logs.id DESC ";


$result = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>

<html>

<head>

<title>System Monitor</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">

<h1>System Monitor</h1>

<div class="table-card">

<table class="data-table">

<tr>

<th>ID</th>
<th>User Name</th>
<th>Email</th>
<th>Activity</th>
<th>Date</th>

</tr>


<?php while($row=mysqli_fetch_assoc($result)){ ?>


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
<?php echo $row['action']; ?>
</td>


<td>
<?php echo $row['created_at']; ?>
</td>


</tr>

<?php } ?>

</table>


    </div>


  </div>

</div>

</body>
</html>