<?php

include __DIR__ . "/config/database.php";


$id = $_GET['id'];


$sql = "SELECT * FROM students WHERE id=$id";

$result = mysqli_query($conn,$sql);
$student = mysqli_fetch_assoc($result);

if(isset($_POST['update']))
{

    $name = $_POST['name'];
    $email = $_POST['email'];
    $registration_no = $_POST['registration_no'];
    $department = $_POST['department'];


    $sql = "UPDATE students SET
            name='$name',
            email='$email',
            registration_no='$registration_no',
            department='$department'
            WHERE id=$id";


    mysqli_query($conn,$sql);


    header("Location:index.php");
    exit();

}

?>


<!DOCTYPE html>
<html>

<head>

<title>Edit Student</title>

</head>


<body>


<h1>Edit Student</h1>


<form method="POST">


Name:

<br>

<input type="text" name="name" value="<?php echo $student['name']; ?>">


<br><br>


Email:

<br>

<input type="email" name="email" value="<?php echo $student['email']; ?>">


<br><br>


Registration No:

<br>

<input type="text" name="registration_no" value="<?php echo $student['registration_no']; ?>">


<br><br>


Department:

<br>

<input type="text" name="department" value="<?php echo $student['department']; ?>">


<br><br>


<button type="submit" name="update">
Update
</button>


</form>


<br>


<a href="index.php">
Back
</a>


</body>
</html>