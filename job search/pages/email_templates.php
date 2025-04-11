<?php
// email_templates.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php';

$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Handle form submission
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accept_email_template = $_POST['accept_email_template'] ?? '';
    $reject_email_template = $_POST['reject_email_template'] ?? '';

    // Insert or update the email templates in the database
    $sql = "INSERT INTO email_templates (user_id, accept_email_template, reject_email_template) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE accept_email_template = VALUES(accept_email_template), reject_email_template = VALUES(reject_email_template)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iss", $user_id, $accept_email_template, $reject_email_template);
        if ($stmt->execute()) {
            $successMessage = "Email templates updated successfully.";
        } else {
            $errorMessage = "Error updating templates: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Error preparing statement: " . $conn->error;
    }
}

// Fetch existing templates
$sql = "SELECT accept_email_template, reject_email_template FROM email_templates WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($accept_email_template, $reject_email_template);
$stmt->fetch();
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Meta tags and links) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Email Templates</title>
    <style>
        /* Basic styling */
        .container {
            max-width: 800px;
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        textarea {
            width: 100%;
            height: 150px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
        }

        .note {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }

        .btn-submit {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

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
    </style>
</head>
<body>
    <div class="container">
        <h2>Customize Email Templates</h2>

        <?php if (!empty($successMessage)): ?>
            <div class="success-message"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <form action="email_templates.php" method="POST">
            <div class="form-group">
                <label for="accept_email_template">Accept Email Template</label>
                <textarea name="accept_email_template" id="accept_email_template"><?= htmlspecialchars($accept_email_template ?? '') ?></textarea>
                <p class="note">You can use the following placeholders: <strong>{applicant_name}</strong>, <strong>{job_title}</strong>, <strong>{company_name}</strong></p>
            </div>

            <div class="form-group">
                <label for="reject_email_template">Reject Email Template</label>
                <textarea name="reject_email_template" id="reject_email_template"><?= htmlspecialchars($reject_email_template ?? '') ?></textarea>
                <p class="note">You can use the following placeholders: <strong>{applicant_name}</strong>, <strong>{job_title}</strong>, <strong>{company_name}</strong></p>
            </div>

            <button type="submit" class="btn-submit">Save Templates</button>
        </form>
    </div>
</body>
</html>
