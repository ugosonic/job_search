<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Include your database connection file

// Fetch user ID from session (assuming the employer is logged in)
$user_id = $_SESSION['id'] ?? null;

// Get job ID from URL parameter
$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    die("Job ID not specified.");
}

// Delete the job if it exists and the user has permission
$sql = "DELETE FROM job_posts WHERE job_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("ii", $job_id, $user_id);

if ($stmt->execute()) {
    echo "Job deleted successfully.";
} else {
    echo "Failed to delete job: " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back to the job listings page
header('Location: view_jobs.php');
exit();
?>
