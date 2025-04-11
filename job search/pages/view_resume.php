<?php
// view_resume.php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Include your database connection file

// Check if resume_id is provided
$resume_id = $_GET['resume_id'] ?? null;

if (!$resume_id) {
    die("Resume ID not specified.");
}

// Fetch resume details
$sql = "SELECT * FROM resume WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resume_id);
$stmt->execute();
$resume_result = $stmt->get_result();

if ($resume_result->num_rows === 0) {
    die("Resume not found.");
}

$resume = $resume_result->fetch_assoc();
$stmt->close();
$conn->close();

// Function to safely display data
function displayField($label, $value) {
    if (!empty($value)) {
        echo "<p><strong>$label:</strong> " . nl2br(htmlspecialchars($value)) . "</p>";
    }
}

// Function to display experience
function displayExperience($index, $resume) {
    $company = $resume["name_of_company_$index"] ?? '';
    $experience = $resume["experience_$index"] ?? '';
    $years = $resume["experience_{$index}_years"] ?? '';

    if (!empty($company) || !empty($experience) || !empty($years)) {
        echo "<h3>Work Experience $index</h3>";
        if (!empty($company)) {
            displayField('Company', $company);
        }
        if (!empty($experience)) {
            displayField('Experience', $experience);
        }
        if (!empty($years)) {
            displayField('Years', $years);
        }
    }
}

// Function to display education
function displayEducation($index, $resume) {
    $institution = $resume["name_of_institution_$index"] ?? '';
    $education = $resume["education_$index"] ?? '';
    $level = $resume["education_level_$index"] ?? '';
    $qualification = $resume["educational_qualification_$index"] ?? '';
    $gcse_passes = $resume["gcse_passes_$index"] ?? '';
    $professional_qualification = $resume["professional_qualification_$index"] ?? '';

    if (!empty($institution) || !empty($education) || !empty($level) || !empty($qualification) || !empty($gcse_passes) || !empty($professional_qualification)) {
        echo "<h3>Education $index</h3>";
        if (!empty($institution)) {
            displayField('Institution', $institution);
        }
        if (!empty($education)) {
            displayField('Education', $education);
        }
        if (!empty($level)) {
            displayField('Education Level', $level);
        }
        if (!empty($qualification)) {
            displayField('Qualification', $qualification);
        }
        if (!empty($gcse_passes)) {
            displayField('GCSE Passes', $gcse_passes);
        }
        if (!empty($professional_qualification)) {
            displayField('Professional Qualification', $professional_qualification);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($resume['full_name']) ?>'s Resume</title>
    <style>
        /* Basic styles for the resume */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }

        .resume-container {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .resume-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .resume-header h1 {
            margin: 0;
            font-size: 32px;
            color: #333;
        }
        .resume-header p {
            margin: 5px 0;
            color: #777;
        }
        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        h3 {
            color: #007bff;
            margin-top: 30px;
        }
        p {
            line-height: 1.6;
            color: #555;
        }
        strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="resume-container">
        <div class="resume-header">
            <h1><?= htmlspecialchars($resume['full_name']) ?></h1>
            <?php if (!empty($resume['email'])): ?>
                <p>Email: <?= htmlspecialchars($resume['email']) ?></p>
            <?php endif; ?>
            <?php if (!empty($resume['mobile'])): ?>
                <p>Phone: <?= htmlspecialchars($resume['mobile']) ?></p>
            <?php endif; ?>
            <?php if (!empty($resume['address'])): ?>
                <p>Address: <?= htmlspecialchars($resume['address']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Professional Summary -->
        <?php if (!empty($resume['professional_summary'])): ?>
            <h2>Professional Summary</h2>
            <p><?= nl2br(htmlspecialchars($resume['professional_summary'])) ?></p>
        <?php endif; ?>

        <!-- Work Experience -->
        <?php
        for ($i = 1; $i <= 3; $i++) {
            displayExperience($i, $resume);
        }
        ?>

        <!-- Education -->
        <?php
        for ($i = 1; $i <= 3; $i++) {
            displayEducation($i, $resume);
        }
        ?>

        <!-- Skills -->
        <?php if (!empty($resume['skill'])): ?>
            <h2>Skills</h2>
            <p><?= nl2br(htmlspecialchars($resume['skill'])) ?></p>
        <?php endif; ?>

        <!-- Job Sector Preference -->
        <?php if (!empty($resume['job_sector_preference'])): ?>
            <h2>Job Sector Preference</h2>
            <p><?= htmlspecialchars($resume['job_sector_preference']) ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
