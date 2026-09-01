<?php

session_start();

require_once '../../config/database.php';

/*
CHECK LOGIN
*/

if (!isset($_SESSION['user_id']))
{
    header('Location: ../../login.php');
    exit();
}
/*
CHECK AIRLINE ROLE
*/

if (strtolower(trim($_SESSION['role'] ?? '')) !== 'airline')
{
    header('Location: ../../index.php');
    exit();
}


/*
GET LOGGED-IN USER ID
*/

$user_id = (int)$_SESSION['user_id'];


/*
GET USER EMAIL
the user email to find the airline record.
*/

$user_email = '';


$sql = "SELECT email
        FROM users
        WHERE id = ?
        LIMIT 1";


$stmt = $conn->prepare($sql);


if ($stmt)
{
    $stmt->bind_param(
        'i',
        $user_id
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0)
    {
        $user = $result->fetch_assoc();

        $user_email = $user['email'];
    }


    $stmt->close();
}


/*
FIND AIRLINE ID
users.id and airlines.id are different.
connect them using email.
*/

$airline_id = 0;


$sql = "SELECT id
        FROM airlines
        WHERE email = ?
        LIMIT 1";


$stmt = $conn->prepare($sql);


if ($stmt)
{
    $stmt->bind_param(
        's',
        $user_email
    );

    $stmt->execute();

    $result = $stmt->get_result();


    if ($result->num_rows > 0)
    {
        $airline = $result->fetch_assoc();

        $airline_id = (int)$airline['id'];
    }


    $stmt->close();
}


/*
CHECK AIRLINE CONNECTION
*/

if ($airline_id <= 0)
{
    die('Airline account is not connected to an airline record.');
}


/*
GET AIRCRAFT ID
*/

$id = (int)($_GET['id'] ?? 0);


/*
CHECK AIRCRAFT ID
*/

if ($id <= 0)
{
    header('Location: index.php');
    exit();
}


/*
DELETE AIRCRAFT
The aircraft must belong to the logged-in airline.
*/

$sql = "DELETE FROM airplanes
        WHERE id = ?
        AND airline_id = ?";


$stmt = $conn->prepare($sql);


if ($stmt)
{
    $stmt->bind_param(
        'ii',
        $id,
        $airline_id
    );

    $stmt->execute();

    $stmt->close();
}


/*
GO BACK TO AIRCRAFT LIST
*/

header('Location: index.php?success=deleted');

exit();

?>