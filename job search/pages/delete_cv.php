<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    echo "User not logged in.";
    exit();
}

// Get cv_id from POST
if (isset($_POST['cv_id'])) {
    $cv_id = intval($_POST['cv_id']);

    // Database connection parameters
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "phplogin"; // Update with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare delete statement
    $stmt = $conn->prepare("DELETE FROM resume WHERE ID = ? AND name = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $name = $_SESSION['name'];
    $stmt->bind_param('is', $cv_id, $name);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $success = false;
    }

    $stmt->close();
    $conn->close();
} else {
    $success = false;
}

// Redirect back to CV history page with a success or error message
if ($success) {
    $_SESSION['message'] = "CV deleted successfully.";
} else {
    $_SESSION['message'] = "Error deleting CV.";
}

header("Location: history.php"); // Change to the actual filename of your CV history page
exit();
?>
