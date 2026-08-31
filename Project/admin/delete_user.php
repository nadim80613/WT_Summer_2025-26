<?php

include "../config/database.php";


$id = $_GET['id'];



if(isset($_POST['delete'])){


    $sql = "DELETE FROM users WHERE id=$id";


    mysqli_query($conn,$sql);


    header("Location: users.php");
    exit();

}



?>


<!DOCTYPE html>

<html>


<head>

<title>Delete User</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>



<body>


<div class="admin-container">


<?php include "../includes/admin_sidebar.php"; ?>



<div class="main-content">


<h1>Delete User</h1>



<div class="form-card">


<h3>
Are you sure you want to delete this user?
</h3>



<div class="form-buttons">


<form method="POST">

<button 
class="delete-confirm-btn" 
type="submit" 
name="delete">

Yes, Delete

</button>


</form>



<a class="back-btn" href="users.php">

Cancel

</a>



    </div>


   </div>

  </div>

 </div>


</body>
</html>