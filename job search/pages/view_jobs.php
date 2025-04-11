<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Ensure you have a connection file for the database


// Fetch user ID from session (assuming the employer is logged in)
$user_id = $_SESSION['id'] ?? null;

// If no user is logged in, redirect to login page
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Fetch all jobs posted by the logged-in employer
$sql = "SELECT job_id, job_title, company_name, job_type, location, submission_date, expiring_date FROM job_posts WHERE user_id = ? ORDER BY submission_date DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$jobs_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link rel="stylesheet" href="../css/view_jobs.css">
    <style>
        /* Styling for the job listing page */
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e4e4e4;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            text-decoration: none;
            text-align: center;
        }

        .btn-view-applicants {
            background-color: #007bff;
        }

        .btn-view-applicants:hover {
            background-color: #0056b3;
        }

        .btn-delete {
            background-color: #dc3545;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        .center-text {
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Your Job Listings</h2>

        <?php if ($jobs_result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Job Title</th>
                    <th>Company Name</th>
                    <th>Job Type</th>
                    <th>Location</th>
                    <th>Submission Date</th>
                    <th>Expiring Date</th>
                    <th>Actions</th>
                </tr>
                <?php while ($job = $jobs_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($job['job_title']) ?></td>
                        <td><?= htmlspecialchars($job['company_name']) ?></td>
                        <td><?= htmlspecialchars($job['job_type']) ?></td>
                        <td><?= htmlspecialchars($job['location']) ?></td>
                        <td><?= htmlspecialchars($job['submission_date']) ?></td>
                        <td><?= htmlspecialchars($job['expiring_date']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_applicants.php?job_id=<?= htmlspecialchars($job['job_id']) ?>" class="btn btn-view-applicants">View Applicants</a>
                                <a href="delete_job.php?job_id=<?= htmlspecialchars($job['job_id']) ?>" class="btn btn-delete">Delete Job</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="center-text">You have not posted any jobs yet.</p>
        <?php endif; ?>
    </div>
</body>
</html>
