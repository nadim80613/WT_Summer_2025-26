<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}


$id = $_GET['id'];



// get aircraft info

$result = mysqli_query($conn,
"SELECT * FROM aircraft_requests WHERE id=$id"
);


$data = mysqli_fetch_assoc($result);



$model = $data['aircraft_model'];



// update status

$sql = "UPDATE aircraft_requests

SET status='Approved'

WHERE id=$id";


mysqli_query($conn,$sql);




// activity log

$log_sql = "INSERT INTO activity_logs
(user_id, action)

VALUES

('".$_SESSION['user_id']."',
'Approved aircraft request: ".$model."')";


mysqli_query($conn,$log_sql);



header("Location: aircraft_requests.php");

exit();

?>