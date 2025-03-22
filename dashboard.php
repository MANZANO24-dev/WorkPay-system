<?php
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

$username = htmlspecialchars($_SESSION['username']); 
$role = htmlspecialchars($_SESSION['role']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WorkPay System</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<h2>Attendance Today</h2>

<div class="dashboard-container">
    <h3>Welcome, <?php echo $username; ?>!</h3>

    <div class="role-message">
        <?php
        switch ($role) {
            case 'dean':
                echo "<p>You are logged in as the Dean. You can check in/check out.</p>";
                break;
            case 'faculty':
                echo "<p>You are logged in as Faculty. You can check in/check out.</p>";
                break;
            case 'staff':
                echo "<p>You are logged in as Staff. You can check in/check out.</p>";
                break;
            default:
                echo "<p>Unknown role. Please contact admin.</p>";
        }
        ?>
    </div>

    <div class="options">
        <form method="POST" action="attendance.php">
            <input type="hidden" name="action" value="check_in">
            <input type="hidden" name="selected_date" value="<?php echo date('Y-m-d'); ?>">
            <button type="submit" class="option">Check In</button>
        </form>

        <form method="POST" action="attendance.php">
            <input type="hidden" name="action" value="check_out">
            <input type="hidden" name="selected_date" value="<?php echo date('Y-m-d'); ?>">
            <button type="submit" class="option">Check Out</button>  
        </form>
        <form method="GET" action="employee_salary.php">
            <input type="hidden" name="id" value="<?php echo $_SESSION['role']; ?>">
            <button type="submit" class="option">View Payment Slip</button>
        </form>

    </div>

    <button class="logout-button" onclick="window.location.href='logout.php';">Logout</button>
</div>

</body>
</html>
