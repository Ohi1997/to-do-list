<?php
session_start();  // Start the session

// Check if the user is already logged in through session
if (!isset($_SESSION['id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['username'])) {
    // Set session variables based on cookie values
    $_SESSION['id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];

    // Redirect to the to-do list page
    header("Location: todo.php");
    exit();
}

// Check if the user is logged in by checking the session
if (!isset($_SESSION['id'])) {
    // If user is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// User is logged in, you can now display the todo list
echo "Welcome, " . $_SESSION['username'];  // Example welcome message


?>
<a href="logout.php" style="float: right;">Logout</a>