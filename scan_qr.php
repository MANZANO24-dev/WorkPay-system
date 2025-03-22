<?php
session_start();
require 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $qr_data = $_POST['qr_data']; 

    
    preg_match('/User ID: (\d+)\nUsername: (.+)\nRole: (.+)\nCheck-in Time: (.+)/', $qr_data, $matches);
    if (!$matches) {
        die(" Invalid QR Code! Data format incorrect.");
    }

    $employee_id = $matches[1];
    $username = $matches[2];
    $role = $matches[3];
    $checkin_time = $matches[4];

    
    $stmt = $conn->prepare("INSERT INTO attendance (employee_id, username, role, checkin_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $employee_id, $username, $role, $checkin_time);

    if ($stmt->execute()) {
        echo " Check-in successful!<br>";
        echo "Employee ID: $employee_id<br>";
        echo "Username: $username<br>";
        echo "Role: $role<br>";
        echo "Check-in Time: $checkin_time";
    } else {
        echo " Check-in failed! Please try again.";
    }
}
?>
