<?php

include 'header.php';
include 'home_logged_users.php';

// Get the latest CV record
$sql = "SELECT * FROM resume ORDER BY creation_date DESC LIMIT 1";
$result = $conn->query($sql);

// Prepare latest CV link
$latest_cv_link = "No CV found.";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $latest_cv_link = "<a href='../cv/view_cv.php?ID=" . $row['ID'] . "' class='latest-cv-link'>" . htmlspecialchars($row['full_name']) . "'s CV</a>";
}
// Get active job listings (excluding expired ones)
$current_date = date('Y-m-d');
$sql = "SELECT * FROM job_posts WHERE expiring_date >= '$current_date'";
$result = $conn->query($sql);

// Initialize variables for pagination
$limit = 10; // Jobs per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the total number of jobs for pagination
$sql_count = "SELECT COUNT(*) AS total FROM job_posts WHERE expiring_date >= CURDATE()";
$result_count = $conn->query($sql_count);
$total_jobs = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_jobs / $limit);

// Initialize 'rejected_jobs' session array if not set
if (!isset($_SESSION['rejected_jobs'])) {
    $_SESSION['rejected_jobs'] = array(); // Initialize it as an empty array
}

// Initialize variables for pagination
$limit = 10; // Jobs per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;



// Get active job listings with pagination (excluding expired ones)
$sql = "SELECT * FROM job_posts WHERE expiring_date >= CURDATE() LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Soft delete function
if (isset($_POST['reject_job'])) {
    $job_id = $_POST['job_id'];
    if (!in_array($job_id, $_SESSION['rejected_jobs'])) {
        $_SESSION['rejected_jobs'][] = $job_id; // Add the job ID to rejected jobs array
    }
}
// Initialize variables for pagination
$limit = 10; // Jobs per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Get the total number of jobs for pagination
$sql_count = "SELECT COUNT(*) AS total FROM job_posts WHERE expiring_date >= CURDATE()";
$result_count = $conn->query($sql_count);
$total_jobs = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_jobs / $limit);


// Initialize 'rejected_jobs' session array if not set
if (!isset($_SESSION['rejected_jobs'])) {
    $_SESSION['rejected_jobs'] = array(); // Initialize it as an empty array
}

// Initialize variables for pagination
$limit = 10; // Jobs per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and filter logic
$search_query = isset($_GET['search']) ? $_GET['search'] : '';
$filter_location = isset($_GET['location']) ? $_GET['location'] : '';
$filter_salary_min = isset($_GET['salary_min']) ? $_GET['salary_min'] : '';
$filter_salary_max = isset($_GET['salary_max']) ? $_GET['salary_max'] : '';
$filter_job_type = isset($_GET['job_type']) ? $_GET['job_type'] : '';

// Base SQL query for job listings
$sql_filters = "SELECT * FROM job_posts WHERE expiring_date >= CURDATE()";

// Apply search and filters
if (!empty($search_query)) {
    $sql_filters .= " AND (job_title LIKE '%$search_query%' OR company_name LIKE '%$search_query%')";
}
if (!empty($filter_location)) {
    $sql_filters .= " AND location = '$filter_location'";
}
if (!empty($filter_salary_min)) {
    $sql_filters .= " AND salary_min >= $filter_salary_min";
}
if (!empty($filter_salary_max)) {
    $sql_filters .= " AND salary_max <= $filter_salary_max";
}
if (!empty($filter_job_type)) {
    $sql_filters .= " AND job_type = '$filter_job_type'";
}

// Get the total number of jobs after filtering
$sql_count = "SELECT COUNT(*) AS total FROM job_posts WHERE expiring_date >= CURDATE()";
$result_count = $conn->query($sql_count);
$total_jobs = $result_count->fetch_assoc()['total'];
$total_pages = ceil($total_jobs / $limit);

// Pagination for filtered results
$sql_filters .= " LIMIT $limit OFFSET $offset";
$filter_result = $conn->query($sql_filters);

// Soft delete function
if (isset($_POST['reject_job'])) {
    $job_id = $_POST['job_id'];
    if (!in_array($job_id, $_SESSION['rejected_jobs'])) {
        $_SESSION['rejected_jobs'][] = $job_id; // Add the job ID to rejected jobs array
    }
}

// Function to calculate time difference (e.g., "posted 3 days ago")
function time_ago($datetime) {
    $time = strtotime($datetime);
    $time_diff = time() - $time;

    if ($time_diff < 1) {
        return 'just now';
    }

    $time_units = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($time_units as $unit_secs => $unit) {
        $diff = floor($time_diff / $unit_secs);
        if ($diff >= 1) {
            return $diff . ' ' . $unit . ($diff > 1 ? 's' : '') . ' ago';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <link rel="stylesheet" href="../css/home_allusers.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Icons -->
       
    <style>
        .containers {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .filters {
            width: 25%;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-right: 20px;
        }
        .filters h3 {
            margin-bottom: 20px;
            font-size: 1.4rem;
            color: #333;
        }
        .filters form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .filters label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        .filters select, .filters input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .filters button {
            padding: 10px;
            background-color: #0073b1;
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .filters button:hover {
            background-color: #005f8d;
        }
        .job-listing-container {
            width: 70%;
        }
        .job-listing {
            display: flex;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .job-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0073b1;
            text-decoration: none;
        }
        .job-title:hover {
            text-decoration: underline;
        }
        .job-company {
            color: #555;
        }
        .job-details {
            margin-top: 10px;
            color: #666;
        }
        .job-details i {
            margin-right: 5px;
        }
        .job-actions {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .job-actions .btn {
            text-decoration: none;
            font-size: 1rem;
            background-color: #0073b1;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .job-actions .btn:hover {
            background-color: #005f8d;
        }
        .job-actions .favorite,
        .job-actions .reject {
            color: #ff5757;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .favorite:hover {
            color: #ff2d2d;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #0073b1;
        }
        .pagination a.active {
            font-weight: bold;
            text-decoration: underline;
        }
        .search-bar {
            display: flex;
            margin-bottom: 20px;
            width: 100%;
        }
        .search-bar input {
            flex-grow: 1;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }
        .search-bar button {
            padding: 10px 15px;
            background-color: #0073b1;
            color: #fff;
            border: none;
            font-size: 1rem;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        .search-bar button:hover {
            background-color: #005f8d;
        }
    </style>
</head>
<body class="loggedin">
    <div class="container">
        <main>
            <section class="certification">
                <h2>Welcome back, <?=htmlspecialchars($_SESSION['name'], ENT_QUOTES)?>!</h2>
            </section>
            <section class="activity">
                <h2>Dashboard</h2>
                <table>
                    <tr>
                        <th>Todays Date</th>
                        <th>Last created CV</th>
                    </tr>
                    <tr>
                        <td><?php echo date('d/m/Y \a\t H:i:s'); ?></td>
                        <td><?php echo $latest_cv_link; ?></td>
                    </tr>
                </table>
                <a href="../cv/cv.php" class="btn">Create CV</a>
                <a href="#" class="resume-btn">Post Job Hunt</a>
            </section>
        </main>
    </div>
    
    <div class="containers">
    <!-- Filter Section -->
    <div class="filters">
        <h3>Filter Jobs</h3>
        <form method="GET" action="home_allusers.php">
            <div>
                <label for="location">Location</label>
                <select name="location" id="location">
                    <option value="">All Locations</option>
                    <option value="London">London</option>
                    <option value="Manchester">Manchester</option>
                    <!-- Add more locations as needed -->
                </select>
            </div>
            <div>
                <label for="salary_min">Salary Min</label>
                <input type="number" name="salary_min" id="salary_min" placeholder="0">
            </div>
            <div>
                <label for="salary_max">Salary Max</label>
                <input type="number" name="salary_max" id="salary_max" placeholder="100000">
            </div>
            <div>
                <label for="job_type">Job Type</label>
                <select name="job_type" id="job_type">
                    <option value="">All Types</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Remote">Remote</option>
                    <!-- Add more types as needed -->
                </select>
            </div>
            <button type="submit">Apply Filters</button>
        </form>
    </div>

    <!-- Job Listing Section -->
    <div class="job-listing-container">
        <div class="search-bar">
            <form method="GET" action="home_allusers.php">
                <input type="text" name="search" placeholder="Search for jobs or companies" value="<?= htmlspecialchars($search_query); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <h2>Available Job Listings</h2>
        <?php if ($filter_result->num_rows > 0): ?>
            <?php while ($job = $filter_result->fetch_assoc()): ?>
                <?php if (!in_array($job['job_id'], $_SESSION['rejected_jobs'])): ?>
                <div class="job-listing">
                    <div class="job-info">
                        <a href="job_details.php?job_id=<?= $job['job_id']; ?>" class="job-title"><?= htmlspecialchars($job['job_title']); ?> at <?= htmlspecialchars($job['company_name']); ?></a>
                        <div class="job-details">
                            <p><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($job['location']); ?> | <i class="fas fa-pound-sign"></i> <?= htmlspecialchars($job['currency']); ?> <?= number_format($job['salary_min'], 0); ?> - <?= number_format($job['salary_max'], 0); ?></p>
                            <small>Posted <?= time_ago($job['submission_date']); ?></small>
                        </div>
                    </div>
                    <div class="job-actions">
                        <!-- Apply Button -->
                        <a href="apply_job.php?job_id=<?= $job['job_id']; ?>" class="btn">Apply</a>
                        <!-- Favorite Button -->
                        <i class="fas fa-heart favorite" onclick="addToFavorites(<?= $job['job_id']; ?>)"></i>
                        <!-- Reject Button -->
                        <form method="POST">
                            <input type="hidden" name="job_id" value="<?= $job['job_id']; ?>">
                            <button type="submit" name="reject_job" class="reject">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No job listings available.</p>
        <?php endif; ?>
        
        <!-- Pagination -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?= $i; ?>" class="<?= ($i == $page) ? 'active' : ''; ?>"><?= $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>
</div>
    <script>
        // JavaScript to toggle full job description
        function showFullDescription(jobId) {
            const descriptionElement = document.getElementById(`description-${jobId}`);
            if (descriptionElement.style.display === 'none') {
                descriptionElement.style.display = 'block';
            } else {
                descriptionElement.style.display = 'none';
            }
        }

        // Function to add job to favorites
    function addToFavorites(jobId) {
        alert('Job ' + jobId + ' added to favorites!');
        // You can make an AJAX request here to add the job to a database or session
    }
    </script>
</body>
</html>