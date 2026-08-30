<?php
require_once 'config/database.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $email_safe = $conn->real_escape_string($email);
        $res = $conn->query("SELECT * FROM users WHERE email = '$email_safe' LIMIT 1");

        if ($res && $res->num_rows > 0) {
            $user = $res->fetch_assoc();

            $db_pass = $user['password'];
            $pass_valid = false;

            // Check various password formats (Plain text, MD5, or Password Hash)
            if ($password === $db_pass || md5($password) === $db_pass || password_verify($password, $db_pass)) {
                $pass_valid = true;
            }

            if ($pass_valid) {
                $_SESSION['user_id']   = (int)$user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = strtolower(trim($user['role'] ?? 'passenger'));

                // Force redirect to passenger dashboard
                header("Location: passenger/dashboard.php");
                exit();
            } else {
                $error = "Incorrect password! Please try again.";
            }
        } else {
            $error = "No user found with this email address.";
        }
    } else {
        $error = "Please fill in both email and password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airport Management System - Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-card { display: flex; background: #ffffff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); width: 800px; min-height: 440px; overflow: hidden; }
        .login-form-side { flex: 1.2; padding: 45px; display: flex; flex-direction: column; justify-content: center; }
        .login-form-side h2 { font-size: 26px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .login-form-side p { font-size: 13px; color: #64748b; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-group input { width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; background: #f8fafc; }
        .form-group input:focus { border-color: #0284c7; background: #ffffff; }
        .btn-signin { background: #0284c7; color: #ffffff; border: none; padding: 12px 24px; font-size: 14px; font-weight: 600; border-radius: 8px; cursor: pointer; transition: 0.2s; margin-top: 8px; width: 100%; }
        .btn-signin:hover { background: #0369a1; }
        .login-banner-side { flex: 1; background: #0284c7; border-top-left-radius: 120px; border-bottom-left-radius: 120px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ffffff; text-align: center; padding: 30px; }
        .login-banner-side h3 { font-size: 24px; font-weight: 800; margin-bottom: 8px; }
        .login-banner-side p { font-size: 13px; color: #e0f2fe; }
        .error-box { background: #fee2e2; color: #dc2626; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; border: 1px solid #fca5a5; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-form-side">
        <h2>Sign in</h2>
        <p>Sign in with your email and password</p>

        <?php if (!empty($error)): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <input type="email" name="email" placeholder="name@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? 'esmamahmed00@gmail.com'); ?>" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" value="123456" required>
            </div>
            <div>
                <button type="submit" class="btn-signin">Sign in</button>
            </div>
        </form>
    </div>

    <div class="login-banner-side">
        <h3>Welcome Back!</h3>
        <p>Airport Management System</p>
    </div>
</div>

</body>
</html>