<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header('Location: hr_login.php');
    exit();
}

if (!isset($_SESSION['qr_generated'])) {
    $_SESSION['qr_generated'] = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard - WorkPay System</title>
    <link rel="stylesheet" href="css/hrdashboard.css">
</head>
<body>

<h1>Welcome, HR Administrators </h1>

<div class="dashboard-container">
    <p>Manage employee records, generate QR codes, and check attendance history.</p>

    <div class="options">
        <a href="employee_history.php" class="option">View Employees Records</a>
        <button id="generateQR" class="option">Generate QR Code</button>
        <button class="logout-button" onclick="window.location.href='logout.php';">Logout</button>
    </div>

    <div id="qr-container">
        <p id="qrMessage"></p>
    </div>
</div>

<script src="js/hrdashboard.js"></script>

</body>
</html>
