<?php



include __DIR__ . "/config/database.php";


$sql = "SELECT * FROM students";

$result = mysqli_query($conn,$sql);




?>


<!DOCTYPE html>
<html>

<head>

<title>Student Management</title>

</head>


<body>


<h1>Student Management System</h1>


<a href="add_student.php">
    Add Student
</a>


<br><br>


<table border="1" cellpadding="10">


<tr>

<th>ID</th>

<th>Name</th>

<th>Email</th>

<th>Registration No</th>

<th>Department</th>

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
<?php echo $row['registration_no']; ?>
</td>


<td>
<?php echo $row['department']; ?>
</td>


<td>

<a href="edit_student.php?id=<?php echo $row['id']; ?>">
Edit
</a>


<a href="delete_student.php?id=<?php echo $row['id']; ?>">
Delete
</a>


</td>


</tr>


<?php } ?>


 </table>

</body>
</html>