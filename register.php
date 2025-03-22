<?php
session_start();
include 'dbconnection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['name']);  
    $password = trim($_POST['password']);  
    $confirm_password = trim($_POST['confirm_password']);  
    $role = $_POST['role'];  

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_message = "Username already taken. Please choose another one.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $hashed_password, $role);

            if ($stmt->execute()) {
                $success_message = "Registration successful! You can now login.";
            } else {
                $error_message = "Error registering user.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WorkPay System</title>
    <link rel="stylesheet" href="css/register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="styleshet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>  
    <style>
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container input {
            width: 100%;
            padding-right: 30px;
        }
        .password-container .toggle-password {
            position: absolute;
            right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h3>Register</h3>

    <?php if (isset($error_message)) { echo "<p class='error-message'>$error_message</p>"; } ?>
    <?php if (isset($success_message)) { echo "<p class='success-message'>$success_message</p>"; } ?>

    <form method="POST" action="register.php">
        <label for="name">Username:</label>
        <input type="text" id="name" name="name" required>

        <label for="password">Password:</label>
        <div class="password-container">
            <input type="password" id="password" name="password" required>
            <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
        </div>

        <label for="confirm_password">Confirm Password:</label>
        <div class="password-container">
            <input type="password" id="confirm_password" name="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
        </div>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="dean">Dean</option>
            <option value="faculty">Faculty</option>
            <option value="staff">Staff</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <a href="login.php" class="register-link">Already have an account? Login here</a>
    <button class="back-button" onclick="window.location.href='index.html';">Back</button>
</div>

<script>
function togglePassword(fieldId) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
    } else {
        field.type = "password";
    }
}
</script>

</body>
</html>
