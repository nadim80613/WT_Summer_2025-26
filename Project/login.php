<?php

include 'config/database.php';

session_start();

$error = "";




if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    $email = trim($_POST['email'] ?? '');

    $password = trim($_POST['password'] ?? '');



    if (!empty($email) && !empty($password)) {


        $email_safe = $conn->real_escape_string($email);


        $res = $conn->query(
            "SELECT * FROM users WHERE email = '$email_safe' LIMIT 1"
        );



        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();
            $db_pass = $user['password'];
            $pass_valid = false;



           
            if (
                $password === $db_pass ||
                md5($password) === $db_pass ||
                password_verify($password, $db_pass)
            ) {

                $pass_valid = true;

            }



            if ($pass_valid) 
                {
                $_SESSION['user_id'] = (int)$user['id'];

                $_SESSION['user_name'] = $user['name'];

                $_SESSION['role'] = strtolower(trim($user['role'] ?? 'passenger'));



                // Activity Log

                $log_sql = "INSERT INTO activity_logs (user_id, action)
                 VALUES ('".$user['id']."','User logged in')";

                 mysqli_query($conn,$log_sql);

                // Role based 

                if ($_SESSION['role'] == "admin") {


                    header("Location: admin/dashboard.php");


                } 
                elseif ($_SESSION['role'] == "staff") {


                    header("Location: staff/dashboard.php");


                } 
                elseif ($_SESSION['role'] == "airline") {


                    header("Location: airline/dashboard.php");


                } 
                else {


                    header("Location: passenger/dashboard.php");


                }


                exit();



            } else {


                $error = "Incorrect password! Please try again.";


            }



        } else {


            $error = "No user found with this email address.";


        }



    } else {


        $error = "Please fill in both email and password.";


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


        <h2>Sign In</h2>


        <p class="subtitle">
            Sign in with your email and password
        </p>



        <?php if(!empty($error)): ?>

            <p style="color:red; margin-bottom:15px;">
                <?php echo $error; ?>
            </p>

        <?php endif; ?>



        <form method="post" action="login.php">


            <input 
                type="email" 
                name="email" 
                placeholder="Enter E-mail"
                required
            >



            <input 
                type="password" 
                name="password" 
                placeholder="Enter Password"
                required
            >



            <button type="submit">
                Sign In
            </button>


        </form>



        



    </div>





    <div class="welcome-box login-welcome">


        <h1>
            Welcome Back!
        </h1>


        <p>
            Airport Management System
        </p>

        <p>
            Don't Have any Account?
        </p>

        <a href="register.php">
        <button class="register-btn">
            Sign Up
        </button>
    </a>




    </div>



</div>



</body>

</html>