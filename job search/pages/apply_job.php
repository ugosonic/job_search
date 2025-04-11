<?php
// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php';
include 'db_connection.php';

// Fetch user ID from session (assuming user is logged in)
$user_id = $_SESSION['id'] ?? null;

if (!$user_id) {
    header('Location: ../login.php');
    exit();
}

// Get job ID from GET or POST
$job_id = $_POST['job_id'] ?? $_GET['job_id'] ?? null;
if (!$job_id) {
    die("Job ID is missing.");
}

// Fetch job details
$job_stmt = $conn->prepare("SELECT job_title, company_name FROM job_posts WHERE job_id = ?");
$job_stmt->bind_param('i', $job_id);
$job_stmt->execute();
$job_result = $job_stmt->get_result();
$job = $job_result->fetch_assoc();
$job_stmt->close();

// Fetch user's existing resumes
$resume_stmt = $conn->prepare("SELECT ID, full_name, creation_date FROM resume WHERE user_id = ?");
$resume_stmt->bind_param("i", $user_id);
$resume_stmt->execute();
$resume_result = $resume_stmt->get_result();
$user_cvs = $resume_result->fetch_all(MYSQLI_ASSOC);
$resume_stmt->close();

// Handle form submission
$successMessage = "";
$errorMessage = "";

// Set POST values
$applicant_name = $_POST['applicant_name'] ?? '';
$applicant_email = $_POST['applicant_email'] ?? '';
$applicant_phone = $_POST['applicant_phone'] ?? '';
$cv_selection = $_POST['cv_selection'] ?? '';
$cover_letter = $_POST['cover_letter'] ?? '';
$uploaded_cv = $_FILES['new_cv'] ?? [];
$cv_type = '';
$uploaded_cv_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($applicant_name) || empty($applicant_email) || empty($applicant_phone)) {
        $errorMessage = "Please fill in all required fields.";
    } elseif (empty($cv_selection) && empty($uploaded_cv['name'])) {
        $errorMessage = "Please select an existing CV or upload a new one.";
    } elseif (!empty($cv_selection) && !empty($uploaded_cv['name'])) {
        $errorMessage = "You can only choose one CV method: existing or upload.";
    } else {
        // Handle uploaded file
        if (!empty($uploaded_cv['name'])) {
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            $file_extension = strtolower(pathinfo($uploaded_cv['name'], PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                $errorMessage = "Invalid file type. Only PDF, DOC, and DOCX are allowed.";
            } else {
                $upload_directory = 'uploads/';
                $new_cv_file_name = $user_id . '_cv_' . time() . '.' . $file_extension;
                $upload_path = $upload_directory . $new_cv_file_name;

                if (move_uploaded_file($uploaded_cv['tmp_name'], $upload_path)) {
                    // Insert into uploaded_cvs
                    $cv_insert = "INSERT INTO uploaded_cvs (user_id, full_name, file_name) VALUES (?, ?, ?)";
                    $cv_stmt = $conn->prepare($cv_insert);
                    $cv_stmt->bind_param("iss", $user_id, $applicant_name, $new_cv_file_name);
                    $cv_stmt->execute();
                    $uploaded_cv_id = $cv_stmt->insert_id;
                    $cv_stmt->close();

                    $cv_type = 'file';
                    $cv_selection = null;
                } else {
                    $errorMessage = "Failed to upload CV.";
                }
            }
        } else {
            $cv_type = 'resume';
            $uploaded_cv_id = null;
        }

        if (empty($errorMessage)) {
            $sql = "INSERT INTO job_applications 
                (user_id, job_id, applicant_name, applicant_email, applicant_phone, cv_selection, uploaded_cv_id, cv_type, cover_letter, application_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("iisssiiss", 
                $user_id, $job_id, $applicant_name, $applicant_email, $applicant_phone, 
                $cv_selection, $uploaded_cv_id, $cv_type, $cover_letter
            );

            if ($stmt->execute()) {
                $successMessage = "Application submitted successfully!";
                $applicant_name = $applicant_email = $applicant_phone = $cv_selection = $cover_letter = '';
                $_POST = [];
            } else {
                $errorMessage = "Error submitting application: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Application</title>
    <link rel="stylesheet" href="../css/apply_job.css">
    <style>
        /* Style the application form */
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #e4e4e4;
        }
        .success-message, .error-message {
    /* Remove display: none; */
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    margin: 20px 0;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
}

.error-message {
    background-color: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        textarea {
            resize: vertical;
            height: 100px;
        }

        button {
            display: block;
            width: 100%;
            padding: 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .success-message, .error-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            display: none;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .hidden {
            display: none;
        }

        .job-details-link {
            display: block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
        }

        .job-details-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Apply for <?= htmlspecialchars($job['job_title']) ?> at <?= htmlspecialchars($job['company_name']) ?></h2>

        <a href="job_details.php?job_id=<?= htmlspecialchars($job_id); ?>" class="job-details-link">View Full Job Details</a>

        <!-- Success and Error Messages -->
        <?php if (!empty($successMessage)): ?>
            <div class="success-message" id="successMessage"><?= htmlspecialchars($successMessage); ?></div>
        <?php endif; ?>
        <?php if (!empty($errorMessage)): ?>
            <div class="error-message" id="errorMessage"><?= htmlspecialchars($errorMessage); ?></div>
        <?php endif; ?>

        <!-- Application Form -->
        <form action="apply_job.php?job_id=<?= htmlspecialchars($job_id); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="job_id" value="<?= htmlspecialchars($job_id); ?>">
            
            <!-- Basic Information -->
            <div class="form-group">
                <label for="applicant_name">Full Name</label>
                <input type="text" id="applicant_name" name="applicant_name" value="<?= htmlspecialchars($applicant_name) ?>" required>
            </div>
            <div class="form-group">
                <label for="applicant_email">Email</label>
                <input type="email" id="applicant_email" name="applicant_email" value="<?= htmlspecialchars($applicant_email) ?>" required>
            </div>
            <div class="form-group">
                <label for="applicant_phone">Phone Number</label>
                <input type="tel" id="applicant_phone" name="applicant_phone" value="<?= htmlspecialchars($applicant_phone) ?>" required>
            </div>

            <!-- CV Selection or Upload -->
            <div class="form-group">
                <label>Choose CV Option</label>
                <div>
                    <label>
                        <input type="radio" name="cv_option" value="select" onchange="toggleCvOption(this)" <?= (!isset($_POST['cv_option']) || $_POST['cv_option'] == 'select') ? 'checked' : '' ?>>
                        Select Existing CV
                    </label>
                    <label>
                        <input type="radio" name="cv_option" value="upload" onchange="toggleCvOption(this)" <?= (isset($_POST['cv_option']) && $_POST['cv_option'] == 'upload') ? 'checked' : '' ?>>
                        Upload New CV
                    </label>
                </div>
            </div>

            <div class="form-group" id="selectCvGroup">
                <label for="cv_selection">Select an Existing CV</label>
                <select id="cv_selection" name="cv_selection">
                    <option value="">Select a CV...</option>
                    <?php foreach ($user_cvs as $cv): ?>
                        <option value="<?= htmlspecialchars($cv['ID']); ?>" <?= (($_POST['cv_selection'] ?? '') == $cv['ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cv['full_name']); ?> (Created on <?= htmlspecialchars($cv['creation_date']); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" id="uploadCvGroup" style="display: none;">
                <label for="new_cv">Upload a New CV (PDF, DOC, DOCX)</label>
                <input type="file" id="new_cv" name="new_cv" accept=".pdf, .doc, .docx">
            </div>

            <!-- Optional Cover Letter -->
            <div class="form-group">
                <label for="cover_letter">Cover Letter (Optional)</label>
                <textarea id="cover_letter" name="cover_letter" placeholder="Write a cover letter..."><?= htmlspecialchars($cover_letter) ?></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit">Submit Application</button>
        </form>
    </div>

    <script>
        function toggleCvOption(radio) {
            if (radio.value === 'select') {
                document.getElementById('selectCvGroup').style.display = 'block';
                document.getElementById('uploadCvGroup').style.display = 'none';
                document.getElementById('cv_selection').disabled = false;
                document.getElementById('new_cv').disabled = true;
            } else {
                document.getElementById('selectCvGroup').style.display = 'none';
                document.getElementById('uploadCvGroup').style.display = 'block';
                document.getElementById('cv_selection').disabled = true;
                document.getElementById('new_cv').disabled = false;
            }
        }

        // Initialize the CV option display based on the selected option
        document.addEventListener('DOMContentLoaded', function() {
            const selectedOption = document.querySelector('input[name="cv_option"]:checked').value;
            toggleCvOption({value: selectedOption});
        });

        // Function to show success or error messages
    function showMessage() {
        const successMessageDiv = document.getElementById('successMessage');
        const errorMessageDiv = document.getElementById('errorMessage');
        if (successMessageDiv) {
            successMessageDiv.style.display = 'block';
            setTimeout(function() {
                successMessageDiv.style.display = 'none';
            }, 5000); // Hide success message after 5 seconds
        }
        if (errorMessageDiv) {
            errorMessageDiv.style.display = 'block';
            setTimeout(function() {
                errorMessageDiv.style.display = 'none';
            }, 5000); // Hide error message after 5 seconds
        }
    }

    window.onload = function() {
        // Initialize the CV option display
        const selectedOption = document.querySelector('input[name="cv_option"]:checked').value;
        toggleCvOption({value: selectedOption});

        // Show messages if any
        showMessage();
    };
        // Show success or error messages for 5 seconds
        <?php if (!empty($successMessage) || !empty($errorMessage)): ?>
        setTimeout(function() {
            <?php if (!empty($successMessage)): ?>
            document.getElementById('successMessage').style.display = 'none';
            <?php else: ?>
            document.getElementById('errorMessage').style.display = 'none';
            <?php endif; ?>
        }, 5000);
        <?php endif; ?>
    </script>
</body>
</html>
