<?php

include "../config/database.php";


if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];


    $sql = "INSERT INTO users(name,email,password,role)
            VALUES('$name','$email','$password','$role')";


    mysqli_query($conn,$sql);


    header("Location: users.php");
    exit();

}

?>


<!DOCTYPE html>
<html>

<head>

<title>Add User</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Add User</h1>


<div class="form-card">


<form method="POST">


<label>Name</label>

<input type="text" name="name" required>



<label>Email</label>

<input type="email" name="email" required>



<label>Password</label>

<input type="password" name="password" required>



<label>Role</label>

<select name="role">

<option value="admin">Admin</option>

<option value="staff">Staff</option>

<option value="passenger">Passenger</option>

<option value="airline">Airline</option>

</select>



<div class="form-buttons">

<button class="save-btn" type="submit" name="submit">
Add User
</button>


<a class="back-btn" href="users.php">
Back
</a>


</div>

</form>

   </div>
  </div>

</div>

</body>
</html>