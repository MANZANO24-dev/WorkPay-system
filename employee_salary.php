<?php
session_start();
include 'dbconnection.php';


if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    die("Access Denied - Please log in.");
}

$username = $_SESSION['username'];
$user_role = $_SESSION['role']; 


if ($user_role !== 'admin') {
    $employee_id = $_SESSION['user_id']; 
} else {

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        die("Invalid request.");
    }
    $employee_id = intval($_GET['id']);
}


$sql = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Employee not found.");
}


$salary_rates = [
    'dean' => 1500,
    'faculty' => 850,
    'staff' => 650
];

$role = strtolower($user['role']);
$hourly_rate = $salary_rates[$role] ?? 0;
$overtime_rate = $hourly_rate * 1.5;


$latest_attendance_sql = "SELECT check_in, check_out FROM attendance WHERE user_id = ? ORDER BY check_in DESC LIMIT 1";
$latest_stmt = $conn->prepare($latest_attendance_sql);
$latest_stmt->bind_param("i", $employee_id);
$latest_stmt->execute();
$latest_result = $latest_stmt->get_result();
$attendance = $latest_result->fetch_assoc();

$check_in_time = $attendance['check_in'] ?? "Not checked in";
$check_out_time = $attendance['check_out'] ?? "Not checked out";
$working_hours = "Not Available";
$salary_today = 0;


if (isset($attendance['check_in'])) {
    $check_in_timestamp = strtotime($attendance['check_in']);

    if (isset($attendance['check_out'])) {
        $check_out_timestamp = strtotime($attendance['check_out']);
        $hours_worked = ($check_out_timestamp - $check_in_timestamp) / 3600;

        $regular_hours = min($hours_worked, 8);
        $overtime_hours = max(0, $hours_worked - 8);

        $salary_today = ($regular_hours * $hourly_rate) + ($overtime_hours * $overtime_rate);
        $working_hours = number_format($hours_worked, 2) . " hours";


        $today_date = date("Y-m-d");
        $check_salary_sql = "SELECT COUNT(*) as count FROM salary_records WHERE user_id = ? AND salary_date = ?";
        $check_stmt = $conn->prepare($check_salary_sql);
        $check_stmt->bind_param("is", $employee_id, $today_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $salary_exists = $check_result->fetch_assoc()['count'] > 0;

        if (!$salary_exists) {
            $insert_salary_sql = "INSERT INTO salary_records (user_id, salary_date, daily_salary) VALUES (?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_salary_sql);
            $insert_stmt->bind_param("isd", $employee_id, $today_date, $salary_today);
            $insert_stmt->execute();
        }
    } else {
        $working_hours = "Still Working...";
        $salary_today = 0;
    }
}


$total_earnings_sql = "SELECT SUM(daily_salary) AS total_earnings FROM salary_records WHERE user_id = ?";
$total_stmt = $conn->prepare($total_earnings_sql);
$total_stmt->bind_param("i", $employee_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_earnings = $total_row['total_earnings'] ?? 0;


$pagibig = $total_earnings >= 30000 ? $total_earnings * 0.05 : 0;
$sss = $total_earnings >= 30000 ? $total_earnings * 0.05 : 0;
$net_salary = $total_earnings - ($pagibig + $sss);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkPay Slip</title>
   
</head>
<style>

body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #2c003e, #4b000f, #000);
    background-attachment: fixed;
    background-size: cover;
    background-position: center;
    height: 100vh;
    margin: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.salary-container {
    width: 90%;
    max-width: 800px;
    padding: 30px;
    border-radius: 12px;
    background-color: rgba(0, 0, 0, 0.9);
    box-shadow: 0 6px 15px rgba(255, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    color: white;
}


.salary-details {
    display: flex;
    justify-content: space-between;
    gap: 30px;
    width: 100%;
}


.left-section, .right-section {
    width: 48%;
    padding: 20px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0px 4px 10px rgba(255, 0, 0, 0.2);
}


h2 {
    text-align: center;
    font-size: 1.8em;
    margin-bottom: 20px;
}


.highlight {
    color: #ff1744;
    font-weight: bold;
}


p {
    margin: 10px 0;
    font-size: 1em;
}


.back-button {
    padding: 12px 30px;
    background: linear-gradient(45deg, #ff1744, #8e24aa);
    border-radius: 10px;
    color: white;
    font-size: 1em;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease;
    text-decoration: none;
    display: inline-block;
    margin-top: 20px;
}

.back-button:hover {
    background: linear-gradient(45deg, #d50000, #6a1b9a);
    transform: scale(1.05);
    box-shadow: 0px 5px 15px rgba(255, 0, 0, 0.4);
}


@media (max-width: 768px) {
    .salary-details {
        flex-direction: column;
        align-items: center;
    }

    .left-section, .right-section {
        width: 100%;
        text-align: center;
    }
}


</style>
<body>

<div class="salary-container">
    <h2>Payment Slip</h2>

    <div class="salary-details">
  
        <div class="details-box left-section">
            <h3><u>Employee Information</u></h3>
            <p><strong>Employee:</strong> <span class="highlight"><?php echo htmlspecialchars($user['username']); ?></span></p>
            <p><strong>Role:</strong> <span class="highlight"><?php echo ucfirst($user['role']); ?></span></p>
            
            <h3><u>Attendance & Work Details</u></h3>
            <p><strong>Check-In Time:</strong> <?php echo $check_in_time; ?></p>
            <p><strong>Check-Out Time:</strong> <?php echo $check_out_time; ?></p>
            <p><strong>Working Hours:</strong> <?php echo $working_hours; ?></p>
        </div>

    
        <div class="details-box right-section">
        <h3><u>Gross Salary</u></h3>

            <?php if ($check_out_time === "Not checked out"): ?>
                <p><strong>Salary for Today:</strong> <span class="highlight">Pending</span></p>
                <br>
            <?php else: ?>
                <p><strong>Salary for Today:</strong> <span class="highlight">PHP <?php echo number_format($salary_today, 2); ?></span></p>
            <?php endif; ?>
            <br>
            <hr>
            <h3><u> Net Salary</u></h3>
            <p><strong>Total Earnings:</strong> <span class="highlight">PHP <?php echo number_format($total_earnings, 2); ?></span></p>

            <?php if ($total_earnings >= 30000): ?>
                <p><strong>Pag-IBIG Deduction (5%):</strong> PHP <?php echo number_format($pagibig, 2); ?></p>
                <p><strong>SSS Deduction (5%):</strong> PHP <?php echo number_format($sss, 2); ?></p>
                <hr>
                <p><strong>Net Salary After Deductions:</strong> <span class="highlight">PHP <?php echo number_format($net_salary, 2); ?></span></p>
            <?php else: ?>
                
            <h3><u>Deductions</u></h3>
                <p><strong>No Deductions Applied Yet!</strong></p>
            <?php endif; ?>
        </div>
    </div>

    <button onclick="history.back()" class="back-button">Back</button>

</div>

</body>
</html>

<?php
$conn->close();
?>
