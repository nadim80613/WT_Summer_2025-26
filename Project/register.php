<?php

include "config/database.php";

$message="";


if($_SERVER["REQUEST_METHOD"]=="POST")
{


    $name = trim($_POST["name"] ?? '');

    $email = trim($_POST["email"] ?? '');

    $password = trim($_POST["password"] ?? '');

    $confirm_password = trim($_POST["confirm_password"] ?? '');



    if(empty($name) || empty($email) || empty($password) || empty($confirm_password))
    {

        $message="Please fill all fields.";

    }


    elseif($password != $confirm_password)
    {

        $message="Password does not match.";

    }


    else
    {


        $email_safe = mysqli_real_escape_string($conn,$email);



        $check = "
        SELECT * FROM users 
        WHERE email='$email_safe'
        ";


        $result = mysqli_query($conn,$check);



        if(mysqli_num_rows($result)>0)
        {

            $message="Email already exists.";

        }


        else
        {


            $hash_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            $role="passenger";



            $sql="
            INSERT INTO users
            (
                name,
                email,
                password,
                role
            )

            VALUES

            (
                '$name',
                '$email_safe',
                '$hash_password',
                '$role'
            )
            ";



            if(mysqli_query($conn,$sql))
            {

                header("Location: login.php");

                exit();

            }

            else
            {

                $message="Registration failed.";

            }


        }


    }


}


?>



<!DOCTYPE html>

<html>


<head>


<title>
Airport Management System - Register
</title>


<link rel="stylesheet" href="assets/css/auth.css">


<script src="assets/js/validation.js"></script>


</head>



<body>



<div class="container">



<div class="welcome-box register-welcome">


<h1>
Welcome!
</h1>


<p>
Already have an account?
</p>


<a href="login.php">

<button>
Sign In
</button>

</a>


</div>






<div class="form-box">


<h2>
Create Account
</h2>


<p class="subtitle">
Register as a passenger
</p>




<?php if(!empty($message)){ ?>

<p class="server-error">

<?php echo $message; ?>

</p>

<?php } ?>






<form method="post"
action="register.php"
onsubmit="return validateRegister()">





<input 

type="text"

id="name"

name="name"

placeholder="Enter Name"

>


<span id="nameError" class="error"></span>







<input 

type="email"

id="email"

name="email"

placeholder="Enter E-mail"

>


<span id="emailError" class="error"></span>







<input 

type="password"

id="password"

name="password"

placeholder="Enter Password"

>


<span id="passwordError" class="error"></span>







<input 

type="password"

id="confirm_password"

name="confirm_password"

placeholder="Confirm Password"

>


<span id="confirmError" class="error"></span>







<button type="submit">

Sign Up

</button>


</form>

</div>
</div>

</body>
</html>