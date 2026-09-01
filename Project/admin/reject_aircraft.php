<?php

session_start();

include "../config/database.php";


if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}


$id = $_GET['id'];



$result = mysqli_query($conn,
"SELECT * FROM aircraft_approval_requests WHERE id=$id"
);

$data = mysqli_fetch_assoc($result);

$model = $data['proposed_model'];

$aircraft_id = $data['aircraft_id'];



// update approval request status

$sql = "UPDATE aircraft_approval_requests

SET status='Rejected'

WHERE id=$id";

mysqli_query($conn,$sql);


// update airplane status

mysqli_query($conn,
"UPDATE airplanes

SET status='Rejected'

WHERE id='$aircraft_id'"
);



// activity log

$log_sql = "INSERT INTO activity_logs
(user_id, action)

VALUES

('".$_SESSION['user_id']."',
'Rejected aircraft request: ".$model."')";


mysqli_query($conn,$log_sql);



header("Location: aircraft_requests.php");

exit();

?>