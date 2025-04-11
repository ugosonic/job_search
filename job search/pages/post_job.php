<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary files
include 'header.php';
include 'home_logged_users.php';
include 'db_connection.php'; // Make sure you have a database connection file

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['id'];

// Check if user is in the employers group
if ($_SESSION['usergroup'] != 'employers') {
    // User is not an employer, deny access
    die("Access denied. Only employers can post jobs.");
}

// Initialize variables
$successMessage = "";
$errorMessage = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $job_title = $_POST['job_title'];
    $company_name = $_POST['company_name'];
    $company_website = $_POST['company_website'];
    $company_description = $_POST['company_description'];
    $job_type = isset($_POST['job_type']) ? implode(', ', $_POST['job_type']) : '';
    $experience_level = $_POST['experience_level'];
    $job_category = $_POST['job_category'];
    $location = $_POST['location'];
    $remote_option = isset($_POST['remote_option']) ? 1 : 0;
    $currency = $_POST['currency'];
    $salary_min = $_POST['salary_min'];
    $salary_max = $_POST['salary_max'];
    $application_email = $_POST['application_email'];
    $application_url = $_POST['application_url'];
    $about_job = $_POST['about_job'];
    $description = $_POST['description'];
    $responsibilities = $_POST['responsibilities'];
    $requirements = $_POST['requirements'];
    $benefits = $_POST['benefits'];
    $skills = $_POST['skills'];
    $education_level = $_POST['education_level'];
    $languages = $_POST['languages'];
    $application_deadline = $_POST['application_deadline'];
    $number_of_positions = $_POST['number_of_positions'];
    $submission_date = date('Y-m-d');
    $expiring_date = $_POST['expiring_date'];

    // Handle Interview Questions
    $interview_questions = [];
    if (isset($_POST['interview_questions'])) {
        foreach ($_POST['interview_questions'] as $index => $question) {
            $options = $_POST['interview_options'][$index] ?? [];
            $interview_questions[] = [
                'question' => $question,
                'options' => array_values(array_filter($options)) // Remove empty options
            ];
        }
    }
    $interview_questions_json = json_encode($interview_questions);

    // Validate required fields
    if (empty($job_title) || empty($company_name) || empty($job_type) || empty($location) || (empty($application_email) && empty($application_url)) || empty($description) || empty($requirements) || empty($expiring_date)) {
        $errorMessage = "Please fill in all required fields marked with *.";
    } else {
        // Insert job post data into the database, including user_id
        $sql = "INSERT INTO job_posts (
                    user_id,
                    job_title,
                    company_name,
                    company_website,
                    company_description,
                    job_type,
                    experience_level,
                    job_category,
                    location,
                    remote_option,
                    currency,
                    salary_min,
                    salary_max,
                    application_email,
                    application_url,
                    about_job,
                    description,
                    responsibilities,
                    requirements,
                    benefits,
                    skills,
                    education_level,
                    languages,
                    interview_questions,
                    application_deadline,
                    number_of_positions,
                    submission_date,
                    expiring_date
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )";

        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param(
                "issssssssiiddssssssssssssiss",
                $user_id,
                $job_title,
                $company_name,
                $company_website,
                $company_description,
                $job_type,
                $experience_level,
                $job_category,
                $location,
                $remote_option,
                $currency,
                $salary_min,
                $salary_max,
                $application_email,
                $application_url,
                $about_job,
                $description,
                $responsibilities,
                $requirements,
                $benefits,
                $skills,
                $education_level,
                $languages,
                $interview_questions_json,
                $application_deadline,
                $number_of_positions,
                $submission_date,
                $expiring_date
            );

            if ($stmt->execute()) {
                $successMessage = "Job posted successfully!";
                $_POST = array(); // Clear POST data after successful submission
            } else {
                $errorMessage = "Error posting job: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Error preparing statement: " . $conn->error;
        }
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
    <link rel="stylesheet" href="../css/post_job.css"> <!-- Link to the CSS file -->
    <style>
        /* CSS styles for the form */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f8;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .success-message, .error-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .success-message {
            background-color: #e6ffed;
            color: #2a7a3a;
        }

        .error-message {
            background-color: #ffe6e6;
            color: #a12a2a;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dcdfe6;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form-group textarea {
            resize: vertical;
        }

        .form-group .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .form-group .checkbox-group label {
            font-weight: normal;
            display: flex;
            align-items: center;
        }

        .form-group .checkbox-group input[type="checkbox"] {
            margin-right: 5px;
        }

        .expandable-section {
            border: 1px solid #dcdfe6;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .expandable-header {
            background-color: #f9fafb;
            padding: 15px;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
        }

        .expandable-header h3 {
            margin: 0;
            font-size: 16px;
            flex-grow: 1;
        }

        .expandable-content {
            display: none;
            padding: 20px;
        }

        .expandable-content.active {
            display: block;
        }

        .expandable-header .toggle-icon {
            font-size: 20px;
            transition: transform 0.3s ease;
        }

        .expandable-header.active .toggle-icon {
            transform: rotate(45deg);
        }

        .required {
            color: red;
        }

        .submit-button {
            background-color: #007bff;
            color: #fff;
            padding: 15px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }
        .currency-select {
            max-width: 200px;
        }

        .interview-question {
            margin-bottom: 20px;
            border: 1px solid #dcdfe6;
            padding: 15px;
            border-radius: 5px;
        }

        .interview-question h4 {
            margin-top: 0;
        }

        .add-option-btn,
        .remove-option-btn,
        .remove-question-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 6px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }

        .add-option-btn {
            background-color: #28a745;
        }

        .add-question-btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .add-question-btn:hover {
            background-color: #0056b3;
        }

        .option-input {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-input input[type="text"] {
            flex-grow: 1;
            margin-right: 10px;
        }
    </style>
    <script>
        // JavaScript functions for dynamic interview questions and options
        window.onload = function() {
            showMessage();

            // Expandable sections functionality
            const expandableHeaders = document.querySelectorAll('.expandable-header');
            expandableHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    this.classList.toggle('active');
                    const content = this.nextElementSibling;
                    content.classList.toggle('active');
                });
            });

            // Interview Questions Functionality
            let questionIndex = 0;
            const addQuestionBtn = document.getElementById('addQuestionBtn');
            const interviewQuestionsContainer = document.getElementById('interviewQuestionsContainer');

            addQuestionBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addInterviewQuestion();
            });

            function addInterviewQuestion() {
                const questionDiv = document.createElement('div');
                questionDiv.classList.add('interview-question');
                questionDiv.dataset.index = questionIndex;

                questionDiv.innerHTML = `
                    <h4>Question ${questionIndex + 1}</h4>
                    <div class="form-group">
                        <label>Question:</label>
                        <input type="text" name="interview_questions[${questionIndex}]" required>
                    </div>
                    <div class="options-container">
                        <label>Options:</label>
                        <div class="option-input">
                            <input type="text" name="interview_options[${questionIndex}][]" placeholder="Option 1">
                            <button type="button" class="remove-option-btn">-</button>
                        </div>
                    </div>
                    <button type="button" class="add-option-btn">Add Option</button>
                    <button type="button" class="remove-question-btn">Remove Question</button>
                `;

                interviewQuestionsContainer.appendChild(questionDiv);

                // Event listeners for options and remove buttons
                questionDiv.querySelector('.add-option-btn').addEventListener('click', function() {
                    addOption(questionDiv);
                });

                questionDiv.querySelector('.remove-question-btn').addEventListener('click', function() {
                    interviewQuestionsContainer.removeChild(questionDiv);
                });

                questionDiv.querySelector('.remove-option-btn').addEventListener('click', function(e) {
                    const optionDiv = e.target.parentElement;
                    const optionsContainer = questionDiv.querySelector('.options-container');
                    optionsContainer.removeChild(optionDiv);
                });

                questionIndex++;
            }

            function addOption(questionDiv) {
                const index = questionDiv.dataset.index;
                const optionsContainer = questionDiv.querySelector('.options-container');

                const optionDiv = document.createElement('div');
                optionDiv.classList.add('option-input');
                optionDiv.innerHTML = `
                    <input type="text" name="interview_options[${index}][]" placeholder="Option">
                    <button type="button" class="remove-option-btn">-</button>
                `;

                optionsContainer.appendChild(optionDiv);

                optionDiv.querySelector('.remove-option-btn').addEventListener('click', function(e) {
                    optionsContainer.removeChild(optionDiv);
                });
            }
        };

        // Function to show and hide messages
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
    </script>
</head>
<body>
    <div class="container">
        <h2>Post a New Job</h2>

        <!-- Success message -->
        <?php if (!empty($successMessage)): ?>
            <div id="successMessage" class="success-message">
                <?= htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Error message -->
        <?php if (!empty($errorMessage)): ?>
            <div id="errorMessage" class="error-message">
                <?= htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <!-- Job posting form -->
        <form action="post_job.php" method="POST">
            <!-- Basic Information -->
            <div class="form-group">
                <label for="job_title">Job Title <span class="required">*</span></label>
                <input type="text" id="job_title" name="job_title" value="<?= htmlspecialchars($_POST['job_title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="company_name">Company Name <span class="required">*</span></label>
                <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>" required>
            </div>

            <!-- Expandable Section: Company Details -->
            <div class="expandable-section">
                <div class="expandable-header">
                    <h3>Company Details (Optional)</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="expandable-content">
                    <div class="form-group">
                        <label for="company_website">Company Website</label>
                        <input type="text" id="company_website" name="company_website" value="<?= htmlspecialchars($_POST['company_website'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="company_description">Company Description</label>
                        <textarea id="company_description" name="company_description" rows="4"><?= htmlspecialchars($_POST['company_description'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Job Details -->
            <div class="form-group">
                <label for="job_type">Job Type <span class="required">*</span></label>
                <div class="checkbox-group">
                    <?php
                    $job_types = ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship', 'Volunteer', 'Remote'];
                    foreach ($job_types as $type):
                    ?>
                    <label>
                        <input type="checkbox" name="job_type[]" value="<?= $type ?>" <?= (isset($_POST['job_type']) && in_array($type, $_POST['job_type'])) ? 'checked' : '' ?>>
                        <?= $type ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            

            <div class="form-group">
                <label for="experience_level">Experience Level</label>
                <select id="experience_level" name="experience_level">
                    <option value="">Select Experience Level...</option>
                    <option value="Entry-level" <?= (($_POST['experience_level'] ?? '') == 'Entry-level') ? 'selected' : '' ?>>Entry-level</option>
                    <option value="Mid-level" <?= (($_POST['experience_level'] ?? '') == 'Mid-level') ? 'selected' : '' ?>>Mid-level</option>
                    <option value="Senior-level" <?= (($_POST['experience_level'] ?? '') == 'Senior-level') ? 'selected' : '' ?>>Senior-level</option>
                    <option value="Director" <?= (($_POST['experience_level'] ?? '') == 'Director') ? 'selected' : '' ?>>Director</option>
                    <option value="Executive" <?= (($_POST['experience_level'] ?? '') == 'Executive') ? 'selected' : '' ?>>Executive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="job_category">Job Category</label>
                <select id="job_category" name="job_category">
                    <option value="">Select Job Category...</option>
                    <option value="IT & Software" <?= (($_POST['job_category'] ?? '') == 'IT & Software') ? 'selected' : '' ?>>IT & Software</option>
                    <option value="Finance" <?= (($_POST['job_category'] ?? '') == 'Finance') ? 'selected' : '' ?>>Finance</option>
                    <option value="Healthcare" <?= (($_POST['job_category'] ?? '') == 'Healthcare') ? 'selected' : '' ?>>Healthcare</option>
                    <option value="Education" <?= (($_POST['job_category'] ?? '') == 'Education') ? 'selected' : '' ?>>Education</option>
                    <option value="Engineering" <?= (($_POST['job_category'] ?? '') == 'Engineering') ? 'selected' : '' ?>>Engineering</option>
                    <option value="Sales & Marketing" <?= (($_POST['job_category'] ?? '') == 'Sales & Marketing') ? 'selected' : '' ?>>Sales & Marketing</option>
                    <!-- Add more categories as needed -->
                </select>
            </div>

            <div class="form-group">
                <label for="location">Location <span class="required">*</span></label>
                <input type="text" id="location" name="location" value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" id="remote_option" name="remote_option" <?= isset($_POST['remote_option']) ? 'checked' : '' ?>>
                    Remote Option Available
                </label>
            </div>

            <!-- Expandable Section: Salary & Benefits -->
            <div class="expandable-section">
                <div class="expandable-header">
                    <h3>Salary & Benefits (Optional)</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="expandable-content">
                <div class="form-group">
                <label for="currency">Currency</label>
                <select id="currency" name="currency" class="currency-select">
                    <option value="">Select Currency...</option>
                    <?php
                    $currencies = [
                        'USD' => 'US Dollar',
                        'EUR' => 'Euro',
                        'GBP' => 'British Pound',
                        'JPY' => 'Japanese Yen',
                        'AUD' => 'Australian Dollar',
                        'CAD' => 'Canadian Dollar',
                        'CHF' => 'Swiss Franc',
                        'CNY' => 'Chinese Yuan',
                        'NZD' => 'New Zealand Dollar',
                        // Add more currencies as needed
                    ];
                    foreach ($currencies as $code => $name):
                    ?>
                    <option value="<?= $code ?>" <?= (($_POST['currency'] ?? '') == $code) ? 'selected' : '' ?>>
                        <?= $name ?> (<?= $code ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

                    <div class="form-group">
                        <label for="salary_min">Minimum Salary</label>
                        <input type="number" id="salary_min" name="salary_min" value="<?= htmlspecialchars($_POST['salary_min'] ?? '') ?>" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="salary_max">Maximum Salary</label>
                        <input type="number" id="salary_max" name="salary_max" value="<?= htmlspecialchars($_POST['salary_max'] ?? '') ?>" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label for="benefits">Benefits</label>
                        <textarea id="benefits" name="benefits" rows="4"><?= htmlspecialchars($_POST['benefits'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Application Details -->
            <div class="form-group">
                <label for="application_email">Application Email</label>
                <input type="email" id="application_email" name="application_email" value="<?= htmlspecialchars($_POST['application_email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="application_url">Application URL</label>
                <input type="text" id="application_url" name="application_url" value="<?= htmlspecialchars($_POST['application_url'] ?? '') ?>">
            </div>
<!-- About the Job -->
<div class="form-group">
                <label for="about_job">About the Job</label>
                <textarea id="about_job" name="about_job" rows="5"><?= htmlspecialchars($_POST['about_job'] ?? '') ?></textarea>
            </div>
            <!-- Job Description -->
            <div class="form-group">
                <label for="description">Job Description <span class="required">*</span></label>
                <textarea id="description" name="description" rows="5" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <!-- Expandable Section: Responsibilities & Requirements -->
            <div class="expandable-section">
                <div class="expandable-header">
                    <h3>Responsibilities & Requirements</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="expandable-content">
                    <div class="form-group">
                        <label for="responsibilities">Responsibilities</label>
                        <textarea id="responsibilities" name="responsibilities" rows="4"><?= htmlspecialchars($_POST['responsibilities'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="requirements">Requirements <span class="required">*</span></label>
                        <textarea id="requirements" name="requirements" rows="4" required><?= htmlspecialchars($_POST['requirements'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <!-- Expandable Section: Interview Questions -->
            <div class="expandable-section">
                <div class="expandable-header">
                    <h3>Interview Questions (Optional)</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="expandable-content">
                    <div id="interviewQuestionsContainer">
                        <!-- Interview questions will be dynamically added here -->
                    </div>
                    <button type="button" id="addQuestionBtn" class="add-question-btn">Add Interview Question</button>
                </div>
            </div>

            <!-- Expandable Section: Additional Information -->
            <div class="expandable-section">
                <div class="expandable-header">
                    <h3>Additional Information (Optional)</h3>
                    <span class="toggle-icon">+</span>
                </div>
                <div class="expandable-content">
                    <div class="form-group">
                        <label for="skills">Skills</label>
                        <input type="text" id="skills" name="skills" placeholder="e.g., JavaScript, Project Management" value="<?= htmlspecialchars($_POST['skills'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="education_level">Education Level</label>
                        <select id="education_level" name="education_level">
                            <option value="">Select Education Level...</option>
                            <option value="High School" <?= (($_POST['education_level'] ?? '') == 'High School') ? 'selected' : '' ?>>High School</option>
                            <option value="Associate's Degree" <?= (($_POST['education_level'] ?? '') == "Associate's Degree") ? 'selected' : '' ?>>Associate's Degree</option>
                            <option value="Bachelor's Degree" <?= (($_POST['education_level'] ?? '') == "Bachelor's Degree") ? 'selected' : '' ?>>Bachelor's Degree</option>
                            <option value="Master's Degree" <?= (($_POST['education_level'] ?? '') == "Master's Degree") ? 'selected' : '' ?>>Master's Degree</option>
                            <option value="Doctorate" <?= (($_POST['education_level'] ?? '') == 'Doctorate') ? 'selected' : '' ?>>Doctorate</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="languages">Languages</label>
                        <input type="text" id="languages" name="languages" placeholder="e.g., English, Spanish" value="<?= htmlspecialchars($_POST['languages'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="number_of_positions">Number of Positions</label>
                        <input type="number" id="number_of_positions" name="number_of_positions" value="<?= htmlspecialchars($_POST['number_of_positions'] ?? '') ?>" min="1">
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="form-group">
                <label for="application_deadline">Application Deadline</label>
                <input type="date" id="application_deadline" name="application_deadline" value="<?= htmlspecialchars($_POST['application_deadline'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="expiring_date">Job Expiry Date <span class="required">*</span></label>
                <input type="date" id="expiring_date" name="expiring_date" value="<?= htmlspecialchars($_POST['expiring_date'] ?? '') ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-button">Post Job</button>
        </form>
    </div>
</body>
</html>
