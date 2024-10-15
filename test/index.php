<?php
include 'config.php';

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if the connection works
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle adding a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['new_task'])) {
    $new_task = $_POST['new_task'];
    $sql = "INSERT INTO tasks (task) VALUES ('$new_task')";
    $conn->query($sql);
    header('Location: index.php');
}

// Handle marking task as completed
if (isset($_GET['complete'])) {
    $task_id = $_GET['complete'];
    $sql = "UPDATE tasks SET status='completed' WHERE id=$task_id";
    $conn->query($sql);
    header('Location: index.php');
}

// Handle deleting a task
if (isset($_GET['delete'])) {
    $task_id = $_GET['delete'];
    $sql = "DELETE FROM tasks WHERE id=$task_id";
    $conn->query($sql);
    header('Location: index.php');
}

// Retrieve tasks from the database
$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);
print_r("Result:" .$result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">To-Do List</h1>
        <form method="POST" class="d-flex justify-content-center mb-4">
            <input type="text" class="form-control me-2" name="new_task" placeholder="Enter new task" required>
            <button type="submit" class="btn btn-success">Add Task</button>
        </form>

        <ul class="list-group">
            <?php if($row = $result->fetch_assoc()) { ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php echo $row['task']; ?>
                    <div>
                        <?php if($row['status'] === 'pending') { ?>
                            <a href="?complete=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Complete</a>
                        <?php } ?>
                        <!-- <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a> -->
                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn btn-sm btn-danger">Delete</a>
                    </div>
                </li>
            <?php } else{
                echo "Maa chudao!";
            } ?>
        </ul>
    </div>
</body>
</html>
<script type="text/javascript">
    function confirmDelete(taskId) {
        if (confirm("Are you sure you want to delete this task?")) {
            window.location.href = "?delete=" + taskId;
        }
    }
</script>
