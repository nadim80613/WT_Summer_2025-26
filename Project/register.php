<?php

include "config/database.php";

$name="";
$email="";
$password="";
$confirm_password="";

$message="";


if($_SERVER["REQUEST_METHOD"]=="POST")
{

    $name=$_POST["name"];
    $email=$_POST["email"];
    $password=$_POST["password"];
    $confirm_password=$_POST["confirm_password"];


    if($password != $confirm_password)
    {
        $message="Password does not match";
    }

    else
    {

        $check="SELECT * FROM users WHERE email='$email'";

        $result=mysqli_query($conn,$check);


        if(mysqli_num_rows($result)>0)
        {
            $message="Email already exists";
        }

        else
        {

            $hash_password=password_hash($password,PASSWORD_DEFAULT);

            $role="passenger";


            $sql="INSERT INTO users(name,email,password,role)
            VALUES('$name','$email','$hash_password','$role')";


            if(mysqli_query($conn,$sql))
            {
                $message="Registration successful";
            }

            else
            {
                $message="Registration failed";
            }

        }

    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Airport Management System - Register</title>

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>

<div class="container">


    <div class="welcome-box register-welcome">

        <h1>Welcome!</h1>

        <p>Already have an account?</p>

        <a href="login.php">
            <button>Sign in</button>
        </a>

    </div>


    <div class="form-box">

        <h2>Create Account</h2>

        <p class="subtitle">
            Register as a passenger
        </p>


        <form method="post">


            <input type="text" name="name" placeholder="Enter Name">

            <input type="email" name="email" placeholder="Enter E-mail">

            <input type="password" name="password" placeholder="Enter Password">

            <input type="password" name="confirm_password" placeholder="Confirm Password">


            <button type="submit">
                Sign up
            </button>


        </form>


        <?php echo $message; ?>


    </div>


</div>


</body>

</html>