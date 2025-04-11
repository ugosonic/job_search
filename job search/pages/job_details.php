<?php
// Include necessary files
include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Assuming you have a connection file

// Check if job ID is provided
if (!isset($_GET['job_id'])) {
    die('Error: Job ID not provided.');
}

// Get job ID from URL parameter
$job_id = intval($_GET['job_id']);
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Fetch job details from the database
$sql = "SELECT * FROM job_posts WHERE job_id = ?";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die('Error: SQL statement preparation failed. ' . $conn->error);
}

$stmt->bind_param('i', $job_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the job exists
if ($result->num_rows == 0) {
    die('Error: Job not found.');
}

$job = $result->fetch_assoc();
$stmt->close();

// Toggle favorite status
if (isset($_POST['toggle_favorite'])) {
    $new_favorite_status = $job['favorite'] == 1 ? 0 : 1;
    $update_sql = "UPDATE job_posts SET favorite = ? WHERE job_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    
    // Check if the statement was prepared successfully
    if ($update_stmt === false) {
        die('Error: SQL statement preparation failed. ' . $conn->error);
    }

    $update_stmt->bind_param('ii', $new_favorite_status, $job_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Reload page to update favorite status
    header("Location: job_details.php?job_id=$job_id");
    exit();
}

// Function to display section if value is not empty
function display_section($label, $value) {
    if (!empty($value)) {
        echo "<div class='job-section'><h3>$label</h3><p>$value</p></div>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link rel="stylesheet" href="../css/job_details.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- For Icons -->
    <style>
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        h1 {
            font-size: 2rem;
            color: #0073b1;
        }
        .job-section {
            margin-bottom: 20px;
        }
        .job-section h3 {
            font-size: 1.2rem;
            color: #333;
        }
        .job-section p {
            font-size: 1rem;
            color: #555;
        }
        .apply-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #0073b1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        .apply-button:hover {
            background-color: #005f8d;
        }
        .share-icon, .favorite-icon {
            font-size: 1.5rem;
            margin-right: 15px;
            cursor: pointer;
        }
        .favorite-icon.selected {
            color: red;
        }
        .social-share {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($job['job_title']) ?> at <?= htmlspecialchars($job['company_name']) ?></h1>

        <!-- Apply Button -->
        <?php if (!empty($job['application_url'])): ?>
            <a href="<?= htmlspecialchars($job['application_url']) ?>" target="_blank" class="apply-button">Apply</a>
        <?php else: ?>
            <a href="apply_job.php?job_id=<?= $job['job_id']; ?>" class="apply-button">Apply</a>
        <?php endif; ?>

        <?php
        // Dynamically display each section if the field is not empty
        display_section("Company Website", "<a href='" . htmlspecialchars($job['company_website']) . "' target='_blank'>" . htmlspecialchars($job['company_website']) . "</a>");
        display_section("Company Description", nl2br(htmlspecialchars($job['company_description'])));
        display_section("Job Type", $job['job_type']);
        display_section("Experience Level", $job['experience_level']);
        display_section("Job Category", $job['job_category']);
        display_section("Location", $job['location']);
        
        // Check for remote option
        if ($job['remote_option'] == 1) {
            display_section("Remote Option", "This job allows remote work.");
        }

        display_section("Salary", $job['currency'] . " " . number_format($job['salary_min'], 2) . " - " . number_format($job['salary_max'], 2));
        display_section("About the Job", nl2br(htmlspecialchars($job['about_job'])));
        display_section("Job Description", nl2br(htmlspecialchars($job['description'])));
        display_section("Responsibilities", nl2br(htmlspecialchars($job['responsibilities'])));
        display_section("Requirements", nl2br(htmlspecialchars($job['requirements'])));
        display_section("Benefits", nl2br(htmlspecialchars($job['benefits'])));
        display_section("Skills", nl2br(htmlspecialchars($job['skills'])));
        display_section("Education Level", $job['education_level']);
        display_section("Languages", nl2br(htmlspecialchars($job['languages'])));
        display_section("Application Email", $job['application_email']);
        display_section("Application Deadline", $job['application_deadline']);
        display_section("Number of Positions", $job['number_of_positions']);
        ?>

        <!-- Social Share and Favorite Icons -->
        <div class="social-share">
            <span class="share-icon">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("http://yourwebsite.com/job_details.php?job_id=" . $job['job_id']); ?>" target="_blank" title="Share on Facebook">
                    <i class="fab fa-facebook-square"></i>
                </a>
            </span>
            <span class="share-icon">
                <a href="https://twitter.com/intent/tweet?url=<?= urlencode("http://yourwebsite.com/job_details.php?job_id=" . $job['job_id']); ?>&text=Check out this job!" target="_blank" title="Share on Twitter">
                    <i class="fab fa-twitter-square"></i>
                </a>
            </span>
            <form method="POST" style="display: inline;">
                <button type="submit" name="toggle_favorite" style="background: none; border: none; cursor: pointer;">
                    <i class="fas fa-heart favorite-icon <?= $job['favorite'] == 1 ? 'selected' : '' ?>"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
