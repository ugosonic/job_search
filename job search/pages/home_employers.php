<?php
session_start();

include 'header.php';
include 'home_logged_users.php';

// Ensure $conn is defined and connected
// If not, include your database connection here
// Assuming $conn is already defined in 'home_logged_users.php' or 'header.php'

// Corrected SQL query with the correct column name
$sql = "SELECT * FROM job_posts ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

// Prepare latest job link
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latest_job_link = "<a href='../pages/view_jobs.php?job_id=" . htmlspecialchars($row['job_id']) . "' class='latest-job-link'>" . htmlspecialchars($row['job_title']) . "'s Job Post</a>";
} else {
    $latest_job_link = "No Job Posted.";
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home Page</title>
    <link rel="stylesheet" href="../css/home_allusers.css">
</head>
<body class="loggedin">
    <div class="container">
        <main>
            <section class="certification">
                <h2>Welcome back, <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES); ?>!</h2>
            </section>
            <section class="activity">
                <h2>Dashboard</h2>
                <table>
                    <tr>
                        <th>Today's Date</th>
                        <th>Last Created Job</th>
                    </tr>
                    <tr>
                        <td><?= date('d/m/Y \a\t H:i:s'); ?></td>
                        <td><?= $latest_job_link; ?></td>
                    </tr>
                </table>
                <a href="post_job.php" class="btn">Post Job</a>
                <a href="search_form.php" class="resume-btn">Search CV</a>
            </section>
        </main>
    </div>
</body>
</html>
