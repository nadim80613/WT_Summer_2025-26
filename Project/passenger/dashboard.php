<?php

session_start();


if(!isset($_SESSION["user_id"]))
{
    header("Location: ../login.php");
    exit();
}


?>

<!DOCTYPE html>
<html>

<head>

    <title>
        Passenger Dashboard
    </title>

</head>


<body>


<h1>
    Welcome <?php echo $_SESSION["name"]; ?>
</h1>


<p>
    Passenger Dashboard
</p>


<a href="../logout.php">
    Logout
</a>


</body>

</html>