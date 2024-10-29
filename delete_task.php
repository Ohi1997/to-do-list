<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$host = "localhost";
$db = "todo_list";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if task ID is set
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

// Redirect back to the to-do page
header("Location: todo.php");
exit();
?>
