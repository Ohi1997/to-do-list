<?php
session_start();  // Start the session

// Check if the user is logged in by session or cookie
if (!isset($_SESSION['id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
        $_SESSION['id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
    } else {
        header("Location: login.php");
        exit();
    }
}

// Database connection (update these with your own values)
$host = "localhost";
$db = "todo_list";
$user = "root";
$pass = "";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];

// Add a new task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task_name) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $task_name);
    $stmt->execute();
    $stmt->close();

    // Refresh to display the new task
    header("Location: todo.php");
    exit();
}

// Fetch tasks for the logged-in user
$stmt = $conn->prepare("SELECT id, task_name, is_completed FROM tasks WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($task_id, $task_name, $is_completed);
$tasks = [];
while ($stmt->fetch()) {
    $tasks[] = ['id' => $task_id, 'name' => $task_name, 'completed' => $is_completed];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="./css/todo-style.css">
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <h3>Your Tasks</h3>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <?php echo htmlspecialchars($task['name']); ?>
                <?php if ($task['completed']): ?>
                    <span style="color: green;">(Completed)</span>
                <?php else: ?>
                    <span style="color: red;">(Incomplete)</span>
                <?php endif; ?>
                <!-- Edit and Delete placeholders -->
                <a href="edit_task.php?id=<?php echo $task['id']; ?>">Edit</a> |
                <a href="delete_task.php?id=<?php echo $task['id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h3>Add New Task</h3>
    <form action="todo.php" method="POST">
        <input type="text" name="task_name" placeholder="Enter new task" required>
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <a href="logout.php" style="float: right;">Logout</a>
</body>
</html>
