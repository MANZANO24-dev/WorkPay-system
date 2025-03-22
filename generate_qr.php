<?php
session_start();
include 'dbconnection.php';
include 'phpqrcode/qrlib.php';


if (!isset($_SESSION['user_id'])) {
    die("Access Denied - User session not found.");
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$username = $user['username'];
$role = $user['role'];
$checkin_time = date('Y-m-d H:i:s');


$qrData = "User ID: " . $user_id . "\nUsername: " . $username . "\nRole: " . $role . "\nCheck-in Time: " . $checkin_time;


$qrFolder = "qr_codes/";
if (!is_dir($qrFolder)) {
    mkdir($qrFolder, 0777, true); 
}


$filePath = $qrFolder . "emp_checkin_" . $user_id . ".png";


QRcode::png($qrData, $filePath, QR_ECLEVEL_L, 10, 2);


header('Content-Type: image/png');
readfile($filePath);
?>
