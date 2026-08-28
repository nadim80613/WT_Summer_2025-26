<?php

session_start();

include "config/database.php";

$email="";
$password="";
$message="";


if($_SERVER["REQUEST_METHOD"]=="POST")
{

    $email=$_POST["email"];
    $password=$_POST["password"];


    $sql="SELECT * FROM users WHERE email='$email'";

    $result=mysqli_query($conn,$sql);


    if(mysqli_num_rows($result)>0)
    {

        $user=mysqli_fetch_assoc($result);


        if(password_verify($password,$user["password"]))
        {

            $_SESSION["user_id"]=$user["id"];
            $_SESSION["name"]=$user["name"];
            $_SESSION["role"]=$user["role"];


            if(isset($_POST["remember"]))
            {
                setcookie("email",$email,time()+86400*30);
            }


            if($user["role"]=="passenger")
            {
                header("Location: passenger/dashboard.php");
                exit();
            }

            else if($user["role"]=="staff")
            {
                header("Location: staff/dashboard.php");
                exit();
            }

            else if($user["role"]=="admin")
            {
                header("Location: admin/dashboard.php");
                exit();
            }

            else if($user["role"]=="airline")
            {
                header("Location: airline/dashboard.php");
                exit();
            }


        }

        else
        {
            $message="Invalid password";
        }

    }

    else
    {
        $message="Email not found";
    }

}

?>


<!DOCTYPE html>
<html>

<head>

    <title>Airport Management System - Login</title>

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>


<div class="container">


    <div class="form-box">

        <h2>Sign in</h2>


        <p class="subtitle">
            Sign in with your email and password
        </p>


        <form method="post">


            <input type="email" name="email" placeholder="Enter E-mail">


            <input type="password" name="password" placeholder="Enter Password">


            <label>
                <input type="checkbox" name="remember">
                Remember Me
            </label>


            <button type="submit">
                Sign in
            </button>


        </form>


        <p>
            <?php echo $message; ?>
        </p>


    </div>



    <div class="welcome-box login-welcome">


        <h1>
            Welcome Back!
        </h1>


        <p>
            Don't have an account?
        </p>


        <a href="register.php">

            <button>
                Sign up
            </button>

        </a>


    </div>


</div>


</body>

</html>