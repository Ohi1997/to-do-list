<?php
session_start();

// Database info
$host = "localhost";
$db = "todo_list";
$user = "root";
$pass = "";

// Connecting to DB
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user data
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Password is correct, start the session
            $_SESSION['id'] = $user_id;
            $_SESSION['username'] = $username;


            // If "Remember Me" is checked, set cookies for 30 days
            if ($remember_me) {
                setcookie("user_id", $user_id, time() + (86400 * 30), "/");  // Cookie valid for 30 days
                setcookie("username", $username, time() + (86400 * 30), "/");
            }

            // Redirect to the to-do list page (create this later)
            header("Location: todo.php");
            exit();
        } else {
            echo "<p style='color:red;'>Invalid password!</p>";
        }
    } else {
        echo "<p style='color:red;'>No account found with that email!</p>";
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
    <title>Login</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CSS  -->
    <link rel="stylesheet" href="./css/login-style.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">User Login</h2>
        <form action="login.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                <label for="remember_me" class="form-check-label">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

    </div>
</body>

</html>

<?php

?>