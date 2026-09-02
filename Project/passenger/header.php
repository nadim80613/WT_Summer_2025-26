<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'passenger') {
    header("Location: ../login.php");
    exit();
}

$logged_user_name = $_SESSION['user_name'] ?? 'Passenger';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Airport Management System - Passenger</title>
    <link rel="stylesheet" href="passenger.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { display: flex; background: #f0f4f8; min-height: 100vh; color: #0f172a; }

        .sidebar {
            width: 260px;
            background: #0f172a;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; bottom: 0; left: 0;
            z-index: 1000;
        }
        .sidebar-brand {
            padding: 24px 20px;
            font-size: 18px;
            font-weight: 800;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #1e293b;
            letter-spacing: 0.5px;
        }
        .nav-links {
            list-style: none;
            padding: 15px 0;
            flex-grow: 1;
        }
        .nav-links li a {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }
        .nav-links li a:hover,
        .nav-links li a.active {
            background: #1e293b;
            color: #ffffff;
            border-left: 4px solid #0284c7;
        }
        .nav-links li.logout-item a {
            color: #ef4444;
        }

        .main-wrapper {
            margin-left: 260px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: #ffffff;
            padding: 18px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .topbar-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }
        .user-pill {
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .page-content {
            padding: 30px;
            flex-grow: 1;
        }

        .btn {
            background: #0284c7;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: inline-block;
            transition: 0.2s;
        }
        .btn:hover { background: #0369a1; }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            background: #e0f2fe;
            color: #0284c7;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        th, td {
            padding: 14px 18px;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        th { background: #f8fafc; font-weight: 700; color: #475569; }
    </style>
</head>
<body>

    
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span>Passenger Portal</span>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="<?php echo ($current_page === 'dashboard.php') ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="search_flights.php" class="<?php echo ($current_page === 'search_flights.php' || $current_page === 'book_flight.php') ? 'active' : ''; ?>">Search & Book Flights</a></li>
            <li><a href="my_bookings.php" class="<?php echo ($current_page === 'my_bookings.php') ? 'active' : ''; ?>">My Bookings</a></li>
            <li><a href="boarding_pass.php" class="<?php echo ($current_page === 'boarding_pass.php') ? 'active' : ''; ?>">Boarding Pass</a></li>
            <li><a href="baggage.php" class="<?php echo ($current_page === 'baggage.php') ? 'active' : ''; ?>">Baggage Tracker</a></li>
            <li><a href="lost_found.php" class="<?php echo ($current_page === 'lost_found.php') ? 'active' : ''; ?>">Lost & Found</a></li>
            <li><a href="notifications.php" class="<?php echo ($current_page === 'notifications.php') ? 'active' : ''; ?>">Notifications</a></li>
            <li class="logout-item"><a href="logout.php">Logout</a></li>
        </ul>
    </aside>

    
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-title">Airport Management System</div>
            <div class="user-pill">
                Logged in as: <strong><?php echo htmlspecialchars($logged_user_name); ?></strong>
            </div>
        </header>
        <main class="page-content">