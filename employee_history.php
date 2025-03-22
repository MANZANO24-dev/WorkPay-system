<?php
session_start();
include 'dbconnection.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    die("Access Denied - Admins only.");
}

$sql = "SELECT u.id, u.username, u.role, a.check_in, a.check_out 
        FROM users u
        LEFT JOIN (
            SELECT user_id, MAX(check_in) AS latest_check_in
            FROM attendance
            GROUP BY user_id
        ) latest ON u.id = latest.user_id
        LEFT JOIN attendance a ON a.user_id = latest.user_id AND a.check_in = latest.latest_check_in
        ORDER BY a.check_in DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Attendance History</title>
    <link rel="stylesheet" href="css/history.css">
</head>
<body>

<h2>Employees Attendance History</h2>

<div class="history-container">
    <table>
        <tr>
            <th>Username</th>
            <th>Check-In Time</th>
            <th>Check-Out Time</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='employee_salary.php?id=" . $row['id'] . "' class='user-link'>" . htmlspecialchars($row['username']) . "</a></td>";
                echo "<td>" . ($row['check_in'] ? $row['check_in'] : "No check-in record") . "</td>";
                echo "<td>" . ($row['check_out'] ? $row['check_out'] : "Not checked out") . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No attendance records found.</td></tr>";
        }
        ?>
    </table>

    <a href="hr_dashboard.php" class="back-button">Back</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
