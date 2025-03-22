<?php
session_start();
include 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['name'];  
    $password = $_POST['password'];  
    $role = $_POST['role']; 

    $sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];  
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "Invalid username or role. Please try again.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WorkPay System</title>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="styleshet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
     
   
</head>
<body>

<div class="form-container">
    <h3>Login</h3>

    <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>

    <form method="POST" action="login.php">
        <label for="name">Username:</label>
        <input type="text" id="name" name="name" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="dean">Dean</option>
            <option value="faculty">Faculty</option>
            <option value="staff">Staff</option>
        </select>

        <button type="submit">Login</button>
    </form>

    <a href="register.php" class="register-link">Don't have an account?  Register here</a>

    <button class="back-button" onclick="window.location.href='index.html';">Back</button>
</div>

</body>
</html>
