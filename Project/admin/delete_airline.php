<?php
session_start();

include "../config/database.php";

if(!isset($_SESSION["user_id"]) || $_SESSION["role"]!="admin")
{
    header("Location:../login.php");
    exit();
}


$id = $_GET['id'];
// get airline name before delete

$query = mysqli_query($conn,"SELECT airline_name FROM airplanes WHERE id=$id");

$data = mysqli_fetch_assoc($query);

$airline_name = $data['airline_name'];


$check = mysqli_query($conn,
"SELECT * FROM flights WHERE airplane_id=$id"
);


if(mysqli_num_rows($check)>0)
{

    echo "<script>
    alert('Cannot delete this airline. Flight records are using this aircraft.');
    window.location='airlines.php';
    </script>";

    exit();

}


// delete airline

$sql = "DELETE FROM airplanes WHERE id=$id";


mysqli_query($conn,$sql);





// activity log

$log_sql = "INSERT INTO activity_logs
(user_id, action) VALUES ('".$_SESSION['user_id']."',
'Deleted airline: ".$airline_name."')";


mysqli_query($conn,$log_sql);



header("Location: airlines.php");

exit();


?>