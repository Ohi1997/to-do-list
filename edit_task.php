<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$db = "todo_list";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch task details if the task ID is set
if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT task_name FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->bind_result($task_name);
    $stmt->fetch();
    $stmt->close();
} else {
    header("Location: todo.php");
    exit();
}

// Update task details on form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_task_name = $_POST['task_name'];
    $stmt = $conn->prepare("UPDATE tasks SET task_name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $new_task_name, $task_id, $_SESSION['id']);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the to-do page
    header("Location: todo.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
</head>
<body>
    <h3>Edit Task</h3>
    <form action="edit_task.php?id=<?php echo $task_id; ?>" method="POST">
        <input type="text" name="task_name" value="<?php echo htmlspecialchars($task_name); ?>" required>
        <button type="submit">Update Task</button>
    </form>
    <a href="todo.php">Cancel</a>
</body>
</html>
