<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $email_safe = $conn->real_escape_string($email);
        $sql = "SELECT * FROM users WHERE email = '$email_safe'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];

                // Direct redirect to Passenger Dashboard
                if (strcasecmp($user['role'], 'Passenger') === 0) {
                    header("Location: passenger/dashboard.php");
                    exit();
                } else {
                    header("Location: passenger/dashboard.php");
                    exit();
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found with this email.";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>