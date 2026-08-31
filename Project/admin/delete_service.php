<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}



$id = $_GET['id'];



// get service name before delete

$query = mysqli_query($conn,
"SELECT service_name FROM airport_services WHERE id=$id"
);


$data = mysqli_fetch_assoc($query);


$name = $data['service_name'];




// delete

$sql = "DELETE FROM airport_services WHERE id=$id";


mysqli_query($conn,$sql);




// activity log

$log_sql = "INSERT INTO activity_logs
(user_id, action)

VALUES

('".$_SESSION['user_id']."',
'Deleted airport service: ".$name."')";


mysqli_query($conn,$log_sql);



header("Location: airport_services.php");

exit();


?>