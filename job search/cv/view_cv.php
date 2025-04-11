<?php
include '../pages/home_logged_users.php';
include '../pages/header.php';

// Get the CV by ID
$cv_id = isset($_GET['ID']) ? intval($_GET['ID']) : 0;
$sql = "SELECT * FROM resume WHERE ID = $cv_id";
$result = $conn->query($sql);

// Check if a record was found
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<p>CV not found.</p>";
    exit;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View CV - <?=htmlspecialchars($row['full_name'], ENT_QUOTES)?>'s CV</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/view_cv.css">
</head>
<body class="loggedin">
    <div class="container">
        <main>
            <section class="cv-view">
                <h2><?=htmlspecialchars($row['full_name'], ENT_QUOTES)?>'s CV</h2>
                <div class="cv-details">
                    <p><strong>Full Name:</strong> <?=htmlspecialchars($row['full_name'], ENT_QUOTES)?></p>
                    <p><strong>Mobile:</strong> <?=htmlspecialchars($row['mobile'], ENT_QUOTES)?></p>
                    <p><strong>Email:</strong> <?=htmlspecialchars($row['email'], ENT_QUOTES)?></p>
                    <p><strong>Address:</strong> <?=htmlspecialchars($row['address'], ENT_QUOTES)?></p>
                    <p><strong>Professional Summary:</strong> <?=htmlspecialchars($row['professional_summary'], ENT_QUOTES)?></p>
                    <h3>Work Experience</h3>
                    <p><strong>Company 1:</strong> <?=htmlspecialchars($row['name_of_company_1'], ENT_QUOTES)?> (<?=htmlspecialchars($row['experience_1_years'], ENT_QUOTES)?> years)</p>
                    <p><strong>Role:</strong> <?=htmlspecialchars($row['experience_1'], ENT_QUOTES)?></p>
                    <p><strong>Company 2:</strong> <?=htmlspecialchars($row['name_of_company_2'], ENT_QUOTES)?> (<?=htmlspecialchars($row['experience_2_years'], ENT_QUOTES)?> years)</p>
                    <p><strong>Role:</strong> <?=htmlspecialchars($row['experience_2'], ENT_QUOTES)?></p>
                    <p><strong>Company 3:</strong> <?=htmlspecialchars($row['name_of_company_3'], ENT_QUOTES)?> (<?=htmlspecialchars($row['experience_3_years'], ENT_QUOTES)?> years)</p>
                    <p><strong>Role:</strong> <?=htmlspecialchars($row['experience_3'], ENT_QUOTES)?></p>
                    <h3>Education</h3>
                    <p><strong>Institution 1:</strong> <?=htmlspecialchars($row['name_of_institution_1'], ENT_QUOTES)?></p>
                    <p><strong>Qualification:</strong> <?=htmlspecialchars($row['educational_qualification_1'], ENT_QUOTES)?></p>
                    <p><strong>Institution 2:</strong> <?=htmlspecialchars($row['name_of_institution_2'], ENT_QUOTES)?></p>
                    <p><strong>Qualification:</strong> <?=htmlspecialchars($row['educational_qualification_2'], ENT_QUOTES)?></p>
                    <p><strong>Institution 3:</strong> <?=htmlspecialchars($row['name_of_institution_3'], ENT_QUOTES)?></p>
                    <p><strong>Qualification:</strong> <?=htmlspecialchars($row['educational_qualification_3'], ENT_QUOTES)?></p>
                    <h3>Skills</h3>
                    <p><?=htmlspecialchars($row['skill'], ENT_QUOTES)?></p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>