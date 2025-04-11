<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Include your database connection file

// Fetch user ID from session (assuming recruiter is logged in)
$user_id = $_SESSION['id'] ?? null;

// Get application ID from URL parameter
$application_id = $_GET['application_id'] ?? null;

// Check if application ID is provided
if (!$application_id) {
    die("Application ID not specified.");
}

// Delete the application if it exists and the user has permission
$sql = "DELETE a FROM job_applications a
        INNER JOIN job_posts j ON a.job_id = j.job_id
        WHERE a.application_id = ? AND j.user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("ii", $application_id, $user_id);

if ($stmt->execute()) {
    echo "Application deleted successfully.";
} else {
    echo "Failed to delete application: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the view applicants page
header('Location: view_applicants.php?job_id=' . $_GET['job_id']);
exit();
?>
