<?php
session_start();
include 'dbconnection.php';

if (!isset($_SESSION['user_id'])) {
    die("<div class='error-message'>Access Denied - User session not found.</div>");
}

$user_id = $_SESSION['user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';
$selected_date = isset($_POST['selected_date']) ? $_POST['selected_date'] : date('Y-m-d');
$current_time = date('H:i:s');
$selected_date_time = $selected_date . " " . $current_time;
$message = "";
$qr_code = "";

$check_sql = "SELECT check_in, check_out FROM attendance WHERE user_id = ? AND DATE(check_in) = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("is", $user_id, $selected_date);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$attendance = $check_result->fetch_assoc();

if ($action == 'check_in') {
    if ($attendance) {
        $message = "<div class='error-message'>You have already checked in today.</div>";
    } else {
        $sql = "INSERT INTO attendance (user_id, check_in) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $selected_date_time);
        if (!$stmt->execute()) {
            die("<div class='error-message'>Database Error: " . $stmt->error . "</div>");
        }
        $qr_code = "generate_qr.php?user_id=" . $user_id . "&t=" . time();
        $message = "<div class='success-message'>Please Scan the QR code. Thank you!</div>";
    }
} elseif ($action == 'check_out') {
    if (!$attendance) {
        $message = "<div class='error-message'>You must check in before checking out!</div>";
    } elseif ($attendance['check_out']) {
        $message = "<div class='error-message'>You have already checked out today.</div>";
    } else {
        $sql = "UPDATE attendance SET check_out = ? WHERE user_id = ? AND DATE(check_in) = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $selected_date_time, $user_id, $selected_date);
        if (!$stmt->execute()) {
            die("<div class='error-message'>Database Error: " . $stmt->error . "</div>");
        }
        $message = "<div class='success-message'>Check-out successful!</div>";
    }
} else {
    $message = "<div class='error-message'>Invalid action.</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - Workpay System</title>
    <link rel="stylesheet" href="css/attendance.css">
</head>
<body>

<div class="container">
    <h2>Attendance Status</h2>
    <?php echo $message; ?>

    <div id="qr-container">
        <h3>Generated QR Code</h3>
        <img id="qrCodeImage" src="<?php echo $qr_code; ?>" alt="QR Code">
    </div>

    <button class="back-button" onclick="window.location.href='dashboard.php';">Back</button>
</div>

<script>
if ("<?php echo $qr_code; ?>" !== "") {
    document.getElementById("qr-container").style.display = "block";
}
</script>

</body>
</html>
