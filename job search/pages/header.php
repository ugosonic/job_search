<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to get the dashboard link based on the user's group
function getDashboardLink() {
    if (!isset($_SESSION['usergroup'])) {
        return "/job search/pages/home_allusers.php"; // Default home page
    }

    switch ($_SESSION['usergroup']) {
        case 'allusers':
            return "/job search/pages/home_allusers.php";
        case 'employers':
            return "/job search/pages/home_employers.php";
        default:
            return "/job search/pages/home_allusers.php"; // Default to 'allusers' home
    }
}

// Function to check if the user is logged in
function isLoggedIn() {
    return isset($_SESSION['id']);
}

// Function to get the user's name
function getUserName() {
    return $_SESSION['name'] ?? 'Guest';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags for Responsive Design -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobSeeker - Home</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/header.css?v=1.0">
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="<?= getDashboardLink(); ?>">
        <img src="/job search/images/jobseeker.png" alt="JobSeekers Logo">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>    
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= getDashboardLink(); ?>">Home</a>
                    </li>
                    <?php if ($_SESSION['usergroup'] == 'employers'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="view_jobs.php">View Jobs</a>
                        </li>
                    <?php elseif ($_SESSION['usergroup'] == 'employers'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="post_job.php">Post a Job</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/job search/pages/history.php">History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php"><?= htmlspecialchars(getUserName()); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-danger" href="../logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="view_jobs.php">Find Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="post_job.php">Post a Job</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary" href="/job search/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white" href="register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Bootstrap JS and Dependencies (jQuery and Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
            integrity="sha384-DfXdzyPjeY2tMWV5ygIKW2Yq9mqznCC00PQ7dQoSTZItz/9q6paVSJtdJzxssMjc" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" 
            integrity="sha384-3mPhkqsLzptDrHkc+h1Gm/g6QxghXqtDsqh00KZRJ++mHdcoR2m+5dZ2ctnpERmJ" 
            crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" 
            integrity="sha384-S5nLAH8rgaOYr1G77EOYV0mTjup39nClY4jV/9ZsQEMxATj8Fz3hG5tGJSkH8zYQ" 
            crossorigin="anonymous"></script>
</body>
</html>
