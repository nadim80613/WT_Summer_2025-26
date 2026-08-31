<?php

include "../config/database.php";


$id = $_GET['id'];


$sql = "SELECT * FROM users WHERE id=$id";

$result = mysqli_query($conn,$sql);

$user = mysqli_fetch_assoc($result);



if(isset($_POST['update'])){


    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];


    $sql = "UPDATE users SET
            name='$name',
            email='$email',
            role='$role'
            WHERE id=$id";


    mysqli_query($conn,$sql);


    header("Location: users.php");
    exit();

}


?>


<!DOCTYPE html>

<html>


<head>

<title>Edit User</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>


<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>


<div class="main-content">


<h1>Edit User</h1>



<div class="form-card">


<form method="POST">


<label>Name</label>

<input 
type="text" 
name="name" 
value="<?php echo $user['name']; ?>"
>



<label>Email</label>

<input 
type="email" 
name="email"
value="<?php echo $user['email']; ?>"
>



<label>Role</label>


<select name="role">


<option 
value="admin"
<?php if($user['role']=="admin") echo "selected"; ?>
>
Admin
</option>


<option 
value="staff"
<?php if($user['role']=="staff") echo "selected"; ?>
>
Staff
</option>


<option 
value="passenger"
<?php if($user['role']=="passenger") echo "selected"; ?>
>
Passenger
</option>


<option 
value="airline"
<?php if($user['role']=="airline") echo "selected"; ?>
>
Airline
</option>


</select>




<div class="form-buttons">


<button class="save-btn" type="submit" name="update">
Update
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