<?php
// accept_application.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php';
include 'home_logged_users.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader (if using Composer)
require '../vendor/autoload.php';

// Or, if you are not using Composer, include the files manually:
// require 'path/to/PHPMailer/src/Exception.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

$application_id = $_GET['application_id'] ?? null;

if (!$application_id) {
    die("Application ID not specified.");
}

// Verify that the application belongs to a job posted by the user
$sql = "SELECT a.applicant_email, a.applicant_name, a.job_id, j.job_title, j.company_name, e.accept_email_template
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

if (!$applicant) {
    die("You do not have permission to perform this action.");
}

// Update application status to 'Accepted'
$sql = "UPDATE job_applications SET status = 'Accepted' WHERE application_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $application_id);
$stmt->execute();
$stmt->close();

// Send email to applicant using PHPMailer
$to = $applicant['applicant_email'];
$subject = "Application Status Update for " . $applicant['job_title'];

// Get the email template
$email_template = $applicant['accept_email_template'];

// If no custom template, use default message
if (empty($email_template)) {
    $email_template = "Dear {applicant_name},\n\nCongratulations! Your application for the position of {job_title} at {company_name} has been accepted.\n\nThank you for your interest.\n\nBest regards,\n{company_name}";
}

// Replace placeholders with actual values
$message = str_replace(
    ['{applicant_name}', '{job_title}', '{company_name}'],
    [$applicant['applicant_name'], $applicant['job_title'], $applicant['company_name']],
    $email_template
);

// Create a new PHPMailer instance
$mail = new PHPMailer(true); // Passing `true` enables exceptions

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email';
    $mail->Password = 'your_password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;

    // Recipients
    $mail->setFrom('your_email', $applicant['company_name']);
    $mail->addAddress($to, $applicant['applicant_name']); // Add a recipient

    // Content
    $mail->isHTML(false);                                  // Set email format to plain text
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
    // Email sent successfully
} catch (Exception $e) {
    // Handle errors here
    error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
    // Optionally, display a user-friendly message
}

// Redirect back to the applicants page
header("Location: view_applicants.php?job_id=" . $applicant['job_id']);
exit();
?>
