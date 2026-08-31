<?php

include __DIR__ . "/config/database.php";


if(isset($_POST['submit']))
{

    $name = $_POST['name'];
    $email = $_POST['email'];
    $registration_no = $_POST['registration_no'];
    $department = $_POST['department'];


    $sql = "INSERT INTO students(name,email,registration_no,department)
            VALUES('$name','$email','$registration_no','$department')";


    mysqli_query($conn,$sql);


    header("Location:index.php");
    exit();

}

?>


<!DOCTYPE html>
<html>

<head>

<title>Add Student</title>

</head>
<body>


<h1>Add Student</h1>
<form method="POST">


Name:

<br>

<input type="text" name="name">

<br><br>


Email:

<br>

<input type="email" name="email">

<br><br>


Registration No:

<br>

<input type="text" name="registration_no">

<br><br>


Department:

<br>

<input type="text" name="department">

<br><br>


<button type="submit" name="submit">
Add Student
</button>


</form>


<br>


<a href="index.php">
Back
</a>


</body>
</html>