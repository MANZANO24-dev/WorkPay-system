<?php 
session_start();

$correct_username = "admin";
$correct_password = password_hash("123", PASSWORD_DEFAULT); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hr_username = trim($_POST['hr_name']);
    $hr_password = trim($_POST['hr_password']);

    if (empty($hr_username) || empty($hr_password)) {
        $error_message = "All fields are required.";
    } elseif ($hr_username === "admin" && password_verify($hr_password, $correct_password)) {
        
        $_SESSION['username'] = $hr_username;
        $_SESSION['role'] = 'admin'; 

        
        header('Location: hr_dashboard.php');
        exit();
    } else {
        $error_message = "Invalid credentials. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Login - WorkPay System</title>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="styleshet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

<h2>HR Management</h2>

<div class="form-container">
    <h3>HR Login</h3>

    <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

    <form method="POST" action="hr_login.php">
        <label for="hr_name">Username:</label>
        <input type="text" id="hr_name" name="hr_name" required>

        <label for="hr_password">Password:</label>
        <input type="password" id="hr_password" name="hr_password" required>

        <button type="submit">Login</button>
    </form>

    <button class="back-button" onclick="window.location.href='index.html';">Back</button>
</div>

</body>
</html>
