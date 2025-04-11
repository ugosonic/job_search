<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Include your database connection file

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader (if using Composer)
require '../vendor/autoload.php'; // Adjust the path as necessary

// Fetch user ID from session (assuming the recruiter is logged in)
$user_id = $_SESSION['id'] ?? null;

// If no user is logged in, redirect to login page
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Get job ID from URL parameter (for the specific job to view applicants)
$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    die("Job ID not specified.");
}

// Handle Accept/Reject actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && isset($_POST['selected_applications'])) {
        $action = $_POST['action']; // 'accept' or 'reject'
        $selected_applications = $_POST['selected_applications']; // array of application IDs

        // Variables to keep track of success and failure counts
        $successCount = 0;
        $failureCount = 0;

        // Loop through selected applications
        foreach ($selected_applications as $application_id) {
            // Get applicant's email and name
            $sql = "SELECT a.applicant_email, a.applicant_name, a.job_id, j.job_title, j.company_name, e.accept_email_template, e.reject_email_template
                    FROM job_applications a
                    JOIN job_posts j ON a.job_id = j.job_id
                    LEFT JOIN email_templates e ON j.user_id = e.user_id
                    WHERE a.application_id = ? AND j.user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $application_id, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $applicant = $result->fetch_assoc();
            $stmt->close();

            if ($applicant) {
                $applicant_email = $applicant['applicant_email'];
                $applicant_name = $applicant['applicant_name'];

                // Update application status
                $status = ($action == 'accept') ? 'Accepted' : 'Rejected';
                $sql = "UPDATE job_applications SET status = ? WHERE application_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $status, $application_id);
                $stmt->execute();
                $stmt->close();

                // Prepare email content
                $to = $applicant_email;
                $subject = "Application Status Update for " . $applicant['job_title'];

                // Get the email template
                if ($action == 'accept') {
                    $email_template = $applicant['accept_email_template'];
                    if (empty($email_template)) {
                        $email_template = "Dear {applicant_name},\n\nCongratulations! Your application for the position of {job_title} at {company_name} has been accepted.\n\nThank you for your interest.\n\nBest regards,\n{company_name}";
                    }
                } else {
                    $email_template = $applicant['reject_email_template'];
                    if (empty($email_template)) {
                        $email_template = "Dear {applicant_name},\n\nWe regret to inform you that your application for the position of {job_title} at {company_name} has been rejected.\n\nThank you for your interest.\n\nBest regards,\n{company_name}";
                    }
                }

                // Replace placeholders with actual values
                $message = str_replace(
                    ['{applicant_name}', '{job_title}', '{company_name}'],
                    [$applicant['applicant_name'], $applicant['job_title'], $applicant['company_name']],
                    $email_template
                );

                // Send email using PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    // $mail->SMTPDebug = 2; // Enable verbose debug output
                    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email';
    $mail->Password = 'your_password';
    // Enable TLS encryption, `ssl` also accepted
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;                                   // TCP port to connect to

                    // Recipients
                    $mail->setFrom('your_email@gmail.com', $applicant['company_name']); // Replace with your email
                    $mail->addAddress($to, $applicant_name);

                    // Content
                    $mail->isHTML(false);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                    $successCount++;
                } catch (Exception $e) {
                    // Log error or handle accordingly
                    error_log('Mailer Error (' . $applicant_email . '): ' . $mail->ErrorInfo);
                    $failureCount++;
                }
            }
        }

        if ($successCount > 0) {
            $successMessage = "$successCount application(s) have been " . ($action == 'accept' ? 'accepted' : 'rejected') . " and emails have been sent.";
        }
        if ($failureCount > 0) {
            $errorMessage = "$failureCount email(s) could not be sent.";
        }
    }
}

// Fetch job information to verify that the logged-in user owns this job posting
$sql = "SELECT job_id, job_title, company_name FROM job_posts WHERE job_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("ii", $job_id, $user_id);
$stmt->execute();
$job_result = $stmt->get_result();

if ($job_result->num_rows === 0) {
    die("You do not have permission to view applicants for this job or the job does not exist.");
}

$job_info = $job_result->fetch_assoc();
$stmt->close();

// Pagination setup
$limit = 10; // Number of entries to show per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page <= 0) $page = 1;
$offset = ($page - 1) * $limit;

// Get total number of applicants
$sql = "SELECT COUNT(*) AS total FROM job_applications WHERE job_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $job_id); 

$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalApplicants = $row['total'];
$stmt->close();

$totalPages = ceil($totalApplicants / $limit);

// Fetch applicants with pagination
$sql = "SELECT 
            a.application_id,
            a.applicant_name, 
            a.applicant_email, 
            a.applicant_phone, 
            a.cv_selection, 
            a.uploaded_cv_id,
            a.cv_type, 
            a.cover_letter, 
            a.application_date, 
            a.status,
            r.full_name AS resume_full_name,
            u.file_name AS uploaded_file_name
        FROM job_applications a
        LEFT JOIN resume r ON r.ID = a.cv_selection
        LEFT JOIN uploaded_cvs u ON u.id = a.uploaded_cv_id
        WHERE a.job_id = ? 
        ORDER BY a.application_date DESC 
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("iii", $job_id, $limit, $offset);
$stmt->execute();
$applicants_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Meta tags and links) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants for <?= htmlspecialchars($job_info['job_title']); ?></title>
    <link rel="stylesheet" href="../css/view_applicants.css">
    <style>
        /* Basic styling for the applicants table */
        .container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #e4e4e4;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .customize-email-template-link {
            display: block;
            text-align: right;
            margin-bottom: 20px;
        }

        .btn-template {
            background-color: #17a2b8;
            padding: 10px 15px;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }

        .btn-template:hover {
            background-color: #138496;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
            vertical-align: top;
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
            font-size: 14px;
        }

        .btn-accept {
            background-color: #28a745;
        }

        .btn-accept:hover {
            background-color: #218838;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .btn-reject:hover {
            background-color: #c82333;
        }

        .btn-download {
            background-color: #007bff;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }

        .cover-letter {
            max-height: 100px;
            overflow-y: auto;
        }

        /* Checkbox styling */
        .select-all {
            margin-right: 5px;
        }

        /* Additional styles for action buttons */
        .bulk-action-buttons {
            margin-bottom: 15px;
        }

        /* Success and error messages */
        .success-message, .error-message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Pagination */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .btn-pagination {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 5px;
        }

        .btn-pagination:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Applicants for <?= htmlspecialchars($job_info['job_title']) ?> at <?= htmlspecialchars($job_info['company_name']) ?></h2>

        <div class="customize-email-template-link">
            <a href="email_templates.php" class="btn btn-template">Customize Email Templates</a>
        </div>

        <?php if (!empty($successMessage)): ?>
            <div id="successMessage" class="success-message"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div id="errorMessage" class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <?php if ($applicants_result->num_rows > 0): ?>
            <form method="POST" action="">
                <div class="action-buttons bulk-action-buttons">
                    <button type="submit" name="action" value="accept" class="btn btn-accept">Accept Selected</button>
                    <button type="submit" name="action" value="reject" class="btn btn-reject">Reject Selected</button>
                </div>
                <table>
                    <tr>
                        <th><input type="checkbox" class="select-all" onclick="toggleSelectAll(this)"></th>
                        <th>Applicant Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Resume</th>
                        <th>Cover Letter</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    <?php while ($applicant = $applicants_result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" name="selected_applications[]" value="<?= htmlspecialchars($applicant['application_id']) ?>"></td>
                            <td><?= htmlspecialchars($applicant['applicant_name']) ?></td>
                            <td><?= htmlspecialchars($applicant['applicant_email']) ?></td>
                            <td><?= htmlspecialchars($applicant['applicant_phone']) ?></td>
                            <td>
                            <?php if ($applicant['cv_type'] === 'resume' && !empty($applicant['cv_selection'])): ?>
    <a href="view_resume.php?resume_id=<?= htmlspecialchars($applicant['cv_selection']) ?>" class="btn btn-download" target="_blank">View Resume</a>
<?php elseif ($applicant['cv_type'] === 'file' && !empty($applicant['uploaded_file_name'])): ?>
    <a href="uploads/<?= htmlspecialchars($applicant['uploaded_file_name']) ?>" class="btn btn-download" target="_blank">Download CV</a>
<?php else: ?>
    No CV Available
<?php endif; ?>

                            </td>
                            <td>
                                <?php if (!empty($applicant['cover_letter'])): ?>
                                    <div class="cover-letter"><?= nl2br(htmlspecialchars($applicant['cover_letter'])) ?></div>
                                <?php else: ?>
                                    No cover letter
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($applicant['application_date']) ?></td>
                            <td><?= htmlspecialchars($applicant['status'] ?? 'Pending') ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="accept_application.php?application_id=<?= htmlspecialchars($applicant['application_id']) ?>" class="btn btn-accept">Accept</a>
                                    <a href="reject_application.php?application_id=<?= htmlspecialchars($applicant['application_id']) ?>" class="btn btn-reject">Reject</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </form>

            <!-- Pagination Links -->
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?job_id=<?= $job_id ?>&page=<?= $page - 1 ?>" class="btn btn-pagination">Previous</a>
                <?php endif; ?>

                <?php if($page < $totalPages): ?>
                    <a href="?job_id=<?= $job_id ?>&page=<?= $page + 1 ?>" class="btn btn-pagination">Next</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>No applicants have applied for this job yet.</p>
        <?php endif; ?>
    </div>

    <script>
        // JavaScript function to handle select all checkbox
        function toggleSelectAll(source) {
            checkboxes = document.getElementsByName('selected_applications[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }

        // Function to show messages and hide them after 5 seconds
        window.onload = function() {
            // Show messages if any
            var successMessageDiv = document.getElementById('successMessage');
            var errorMessageDiv = document.getElementById('errorMessage');
            if (successMessageDiv) {
                setTimeout(function() {
                    successMessageDiv.style.display = 'none';
                }, 5000);
            }
            if (errorMessageDiv) {
                setTimeout(function() {
                    errorMessageDiv.style.display = 'none';
                }, 5000);
            }
        };
    </script>
</body>
</html>
