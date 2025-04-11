<?php
session_start();

// Include header and any necessary files
include '../pages/home_logged_users.php';
include '../pages/header.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['name'])) {
    // Redirect to login page
    header('Location: ../login.php');
    exit();
}
// Initialize success and error messages
$successMessage = '';
$errorMessage = '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process form data here
    // For demonstration purposes, we'll set a success message
    $successMessage = 'Your resume has been saved successfully!';
    // Add code here to save the form data to a database or generate PDF
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume Builder</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .form-section {
            margin-bottom: 40px;
        }
        .add-section-btn {
            margin-top: 10px;
        }
        .remove-section-btn {
            margin-top: 30px;
            margin-bottom: 10px;
        }
        .success-message, .error-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        /* Adjust button styles */
        .btn {
            border-radius: 5px;
        }
        .btn-primary, .btn-success, .btn-secondary {
            padding: 10px 20px;
        }
        /* Responsive adjustments */
        @media (max-width: 576px) {
            .form-section {
                margin-bottom: 30px;
            }
            .add-section-btn, .remove-section-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">CV Builder</h1>
    <p class="text-center">Note: Required fields are marked with "*".</p>
    <p class="text-center">To save your resume online, please click "Submit". "Download Resume" only downloads the file without saving it online.</p>

    <!-- Success and Error Messages -->
    <?php if (!empty($successMessage)): ?>
        <div id="successMessage" class="success-message">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($errorMessage)): ?>
        <div id="errorMessage" class="error-message">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <form id="resumeForm" action="save_form.php" method="post">
        <!-- Hidden fields -->
        <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id'], ENT_QUOTES) ?>">
<input type="hidden" name="username" value="<?= htmlspecialchars($_SESSION['username'], ENT_QUOTES) ?>">
<input type="hidden" name="creation_date" value="<?= date('Y-m-d H:i:s') ?>">

        <!-- CV Title -->
        <div class="form-group">
            <label for="cvTitle">CV Title*</label>
            <input type="text" class="form-control" id="cvTitle" name="file_name" placeholder="CV Title" required>
        </div>

        <!-- Personal Information -->
        <div class="form-section">
            <h3>Personal Information</h3>
            <div class="form-group">
                <label for="fullName">Full Name*</label>
                <input type="text" class="form-control" id="fullName" name="full_name" placeholder="First Last Name" required>
            </div>
            <div class="form-group">
                <label for="mobile">Mobile No*</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" placeholder="Phone Number" required>
            </div>
            <div class="form-group">
                <label for="email">Email ID*</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="e.g. abc@gmail.com" required>
            </div>
            <div class="form-group">
                <label for="address">Permanent Address*</label>
                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
            </div>
        </div>

        <!-- Professional Summary -->
        <div class="form-section">
            <h3>Professional Summary</h3>
            <div class="form-group">
                <label for="professionalSummary">Professional Summary*</label>
                <textarea class="form-control" id="professionalSummary" name="professional_summary" rows="5" maxlength="5000" required></textarea>
            </div>
        </div>

        <!-- Experience Section -->
        <div class="form-section">
            <h3>Experience</h3>
            <div id="experienceSection">
                <!-- Experience Item Template -->
                <div class="experience-item">
                    <div class="form-group">
                        <label for="companyName1">Company Name*</label>
                        <input type="text" class="form-control" id="companyName1" name="experience[0][company_name]" placeholder="Company Name" required>
                    </div>
                    <div class="form-group">
                        <label for="jobTitle1">Job Title*</label>
                        <input type="text" class="form-control" id="jobTitle1" name="experience[0][job_title]" placeholder="Job Title" required>
                    </div>
                    <div class="form-group">
                        <label for="experienceDescription1">Description*</label>
                        <textarea class="form-control" id="experienceDescription1" name="experience[0][description]" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="experienceYears1">Number of Years*</label>
                        <input type="number" class="form-control" id="experienceYears1" name="experience[0][years]" placeholder="e.g. 2" min="0" required>
                    </div>
                </div>
            </div>
            <button type="button" id="addExperienceBtn" class="btn btn-primary add-section-btn"><i class="fas fa-plus"></i> Add Experience</button>
        </div>

        <!-- Education Section -->
        <div class="form-section">
            <h3>Education</h3>
            <div id="educationSection">
                <!-- Education Item Template -->
                <div class="education-item">
                    <div class="form-group">
                        <label for="institutionName1">Institution Name*</label>
                        <input type="text" class="form-control" id="institutionName1" name="education[0][institution_name]" placeholder="Institution Name" required>
                    </div>
                    <div class="form-group">
                        <label for="degree1">Degree*</label>
                        <input type="text" class="form-control" id="degree1" name="education[0][degree]" placeholder="Degree" required>
                    </div>
                    <div class="form-group">
                        <label for="educationDescription1">Description</label>
                        <textarea class="form-control" id="educationDescription1" name="education[0][description]" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <button type="button" id="addEducationBtn" class="btn btn-primary add-section-btn"><i class="fas fa-plus"></i> Add Education</button>
        </div>

        <!-- Skills -->
        <div class="form-section">
            <h3>Skills</h3>
            <div id="skillsSection">
                <!-- Skill Item Template -->
                <div class="skill-item">
                    <div class="form-group">
                        <label for="skill1">Skill*</label>
                        <input type="text" class="form-control" id="skill1" name="skills[0]" placeholder="e.g. Programming" required>
                    </div>
                </div>
            </div>
            <button type="button" id="addSkillBtn" class="btn btn-primary add-section-btn"><i class="fas fa-plus"></i> Add Skill</button>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
            <h3>Additional Information</h3>
            <div class="form-group">
                <label for="jobSectorPreference">Job or Sector Preference*</label>
                <input type="text" class="form-control" id="jobSectorPreference" name="job_sector_preference" placeholder="e.g. Technology" required>
            </div>
        </div>

        <!-- Declaration -->
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" id="declaration" name="declaration" value="I hereby declare that the information furnished above is true to the best of my knowledge and belief." required>
            <label class="form-check-label" for="declaration">I hereby declare that the information furnished above is true to the best of my knowledge and belief.*</label>
        </div>

        <!-- Submit Buttons -->
        <button type="submit" name="submit" class="btn btn-success">Submit</button>
        <button type="submit" formaction="generate_pdf.php" class="btn btn-secondary">Download PDF</button>

    </form>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script>
$(document).ready(function() {
    // Hide success and error messages after 5 seconds
    setTimeout(function() {
        $('#successMessage').fadeOut('slow');
        $('#errorMessage').fadeOut('slow');
    }, 5000);

    // Experience Section
    var experienceIndex = 1;
    $('#addExperienceBtn').click(function() {
        var experienceItem = `
        <div class="experience-item">
            <hr>
            <button type="button" class="btn btn-danger remove-section-btn"><i class="fas fa-trash-alt"></i> Remove Experience</button>
            <div class="form-group">
                <label for="companyName${experienceIndex + 1}">Company Name</label>
                <input type="text" class="form-control" id="companyName${experienceIndex + 1}" name="experience[${experienceIndex}][company_name]" placeholder="Company Name">
            </div>
            <div class="form-group">
                <label for="jobTitle${experienceIndex + 1}">Job Title</label>
                <input type="text" class="form-control" id="jobTitle${experienceIndex + 1}" name="experience[${experienceIndex}][job_title]" placeholder="Job Title">
            </div>
            <div class="form-group">
                <label for="experienceDescription${experienceIndex + 1}">Description</label>
                <textarea class="form-control" id="experienceDescription${experienceIndex + 1}" name="experience[${experienceIndex}][description]" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="experienceYears${experienceIndex + 1}">Number of Years</label>
                <input type="number" class="form-control" id="experienceYears${experienceIndex + 1}" name="experience[${experienceIndex}][years]" placeholder="e.g. 2" min="0">
            </div>
        </div>
        `;
        $('#experienceSection').append(experienceItem);
        experienceIndex++;
    });

    // Education Section
    var educationIndex = 1;
    $('#addEducationBtn').click(function() {
        var educationItem = `
        <div class="education-item">
            <hr>
            <button type="button" class="btn btn-danger remove-section-btn"><i class="fas fa-trash-alt"></i> Remove Education</button>
            <div class="form-group">
                <label for="institutionName${educationIndex + 1}">Institution Name</label>
                <input type="text" class="form-control" id="institutionName${educationIndex + 1}" name="education[${educationIndex}][institution_name]" placeholder="Institution Name">
            </div>
            <div class="form-group">
                <label for="degree${educationIndex + 1}">Degree</label>
                <input type="text" class="form-control" id="degree${educationIndex + 1}" name="education[${educationIndex}][degree]" placeholder="Degree">
            </div>
            <div class="form-group">
                <label for="educationDescription${educationIndex + 1}">Description</label>
                <textarea class="form-control" id="educationDescription${educationIndex + 1}" name="education[${educationIndex}][description]" rows="4"></textarea>
            </div>
        </div>
        `;
        $('#educationSection').append(educationItem);
        educationIndex++;
    });

    // Skills Section
    var skillIndex = 1;
    $('#addSkillBtn').click(function() {
        var skillItem = `
        <div class="skill-item">
            <div class="form-group">
                <label for="skill${skillIndex + 1}">Skill</label>
                <input type="text" class="form-control" id="skill${skillIndex + 1}" name="skills[${skillIndex}]" placeholder="e.g. Communication">
            </div>
        </div>
        `;
        $('#skillsSection').append(skillItem);
        skillIndex++;
    });

    // Remove Section
    $(document).on('click', '.remove-section-btn', function() {
        $(this).parent().remove();
    });

});
</script>

</body>
</html>
