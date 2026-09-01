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
"SELECT * FROM aircraft_approval_requests WHERE id=$id"
);


$data = mysqli_fetch_assoc($result);


$model = $data['proposed_model'];




// update status

$sql = "UPDATE aircraft_approval_requests

SET status='Approved'

WHERE id=$id";

// Get aircraft id
$aircraft_id = $data['aircraft_id'];


// Update aircraft status
$update_aircraft = "
UPDATE airplanes
SET status='Active'
WHERE id=$aircraft_id
";

mysqli_query($conn,$update_aircraft);

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