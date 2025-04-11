<?php
// Start or resume session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If user is not logged in, redirect to login page
    header("Location: index.html"); // Replace 'login.php' with your actual login page
    exit(); // Stop script execution
}

// You might include additional session-related functionality here, such as checking session timeouts or refreshing session data
?>
