<?php
session_start();
include 'db_connection.php';

// Get job ID and user ID from POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'])) {
    $job_id = $_POST['job_id'];
    $user_id = $_SESSION['id']; // Assuming you have a user ID stored in session

    // Insert into rejected jobs table (you need to create this table)
    $sql = "INSERT INTO rejected_jobs (user_id, job_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $job_id);
        if ($stmt->execute()) {
            echo "Job rejected successfully!";
        } else {
            echo "Error rejecting job: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
