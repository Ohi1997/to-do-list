<?php
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
    // Get data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    //check if password match
    if ($password !== $confirm_password) {
        echo "<center><p class='register-alert-text'>Passwords do not match!</p></center>";
    } else {
        //hash the password
        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        //check if username, email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<center><p class='register-alert-text'>Username or Email already exists!</p></center>";
        } else {
            // insert into database
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash_password);

            // show message upon succession
            if ($stmt->execute()) {
                echo "<center><p class='register-success-text'>Registration successful! You can now <a href='login.php'>login</a>.</p></center>";
            } else {
                echo "<center><p class='register-alert-text'>Something went wrong. Please try again.</p></center>";
            }
        }
    }
    //close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/register-style.css">
    <!-- <link rel="stylesheet" href="/css/register-style.css"> -->

</head>

<body>
    <div class="container" id="user-form">
        <h2 class="mt-5">User Registration</h2>
        <form action="register.php" method="POST" class="mt-3">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>

</html>