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

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = 'Passenger';

                header("Location: passenger/dashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with this email.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Airport Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #ffffff; width: 800px; max-width: 90%; min-height: 460px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); display: flex; overflow: hidden; }
        .form-area { flex: 1.1; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .form-area h2 { font-size: 28px; margin-bottom: 6px; color: #0f172a; }
        .form-area p { color: #64748b; font-size: 14px; margin-bottom: 20px; }
        .input-box { width: 100%; padding: 12px 14px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 12px; }
        .btn-in { padding: 10px 25px; border: 1.5px solid #0284c7; background: transparent; color: #0284c7; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .btn-in:hover { background: #0284c7; color: #fff; }
        .banner-area { flex: 0.9; background: #0284c7; border-top-left-radius: 100px; border-bottom-left-radius: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; padding: 30px; text-align: center; }
        .banner-area h2 { margin-bottom: 10px; }
        .error-box { background: #fee2e2; color: #dc2626; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="form-area">
        <h2>Sign in</h2>
        <p>Sign in with your email and password</p>
        <?php if (!empty($error)): ?><div class="error-box"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" action="login.php">
            <input type="email" name="email" class="input-box" placeholder="Enter E-mail" required value="eshmam@gmail.com">
            <input type="password" name="password" class="input-box" placeholder="Enter Password" required value="123456">
            <button type="submit" class="btn-in">Sign in</button>
        </form>
    </div>
    <div class="banner-area">
        <h2>Welcome Back!</h2>
        <p>Airport Management System</p>
    </div>
</div>
</body>
</html>