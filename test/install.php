<?php
if (file_exists('config.php') && !empty(DB_HOST)) {
    // Redirect to index.php if already installed
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user input
    $dbHost = $_POST['db_host'];
    $dbUser = $_POST['db_user'];
    $dbPass = $_POST['db_pass'];
    $dbName = $_POST['db_name'];

    // Create connection
    $conn = new mysqli($dbHost, $dbUser, $dbPass);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create the database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS $dbName";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully.<br>";
    } else {
        die("Error creating database: " . $conn->error);
    }

    // Select the database
    $conn->select_db($dbName);

    // Create tasks table
    $sql = "CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        task VARCHAR(255) NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo "Table 'tasks' created successfully.<br>";
    } else {
        die("Error creating table: " . $conn->error);
    }

    // Write credentials to config.php
    $configContent = "<?php\n";
    $configContent .= "define('DB_HOST', '$dbHost');\n";
    $configContent .= "define('DB_USER', '$dbUser');\n";
    $configContent .= "define('DB_PASS', '$dbPass');\n";
    $configContent .= "define('DB_NAME', '$dbName');\n";

    file_put_contents('config.php', $configContent);

    // Redirect to main page
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">To-Do List Installation</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="db_host" class="form-label">Database Host</label>
                <input type="text" class="form-control" id="db_host" name="db_host" placeholder="e.g., localhost" required>
            </div>
            <div class="mb-3">
                <label for="db_user" class="form-label">Database User</label>
                <input type="text" class="form-control" id="db_user" name="db_user" placeholder="e.g., root" required>
            </div>
            <div class="mb-3">
                <label for="db_pass" class="form-label">Database Password</label>
                <input type="password" class="form-control" id="db_pass" name="db_pass" placeholder="e.g., password">
            </div>
            <div class="mb-3">
                <label for="db_name" class="form-label">Database Name</label>
                <input type="text" class="form-control" id="db_name" name="db_name" placeholder="e.g., todolist_db" required>
            </div>
            <button type="submit" class="btn btn-primary">Install</button>
        </form>
    </div>
</body>
</html>
