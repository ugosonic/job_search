<?php
session_start();

// Database connection parameters
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "phplogin";

// Create connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$response_message = "";

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get user_id and username from POST data
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $username = isset($_POST['username']) ? $_POST['username'] : '';

    // Collect data from POST
    $cv_title = isset($_POST['file_name']) ? $_POST['file_name'] : '';
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : '';
    $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $address = isset($_POST['address']) ? $_POST['address'] : '';
    $professional_summary = isset($_POST['professional_summary']) ? $_POST['professional_summary'] : '';
    $job_sector_preference = isset($_POST['job_sector_preference']) ? $_POST['job_sector_preference'] : '';
    $creation_date = date('Y-m-d H:i:s'); // Current timestamp

    // Collect experiences
    $experiences = isset($_POST['experience']) ? $_POST['experience'] : [];
    $experiences_json = json_encode($experiences);

    // Collect educations
    $educations = isset($_POST['education']) ? $_POST['education'] : [];
    $educations_json = json_encode($educations);

    // Collect skills
    $skills = isset($_POST['skills']) ? $_POST['skills'] : [];
    $skills_json = json_encode($skills);

    // Prepare the SQL statement
    $sql = "INSERT INTO resume (
        user_id, full_name, file_name, mobile, email, address, professional_summary,
        job_sector_preference, skills, experiences, educations, creation_date, name
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Check if the prepare statement failed
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "issssssssssss",
        $user_id, $full_name, $cv_title, $mobile, $email, $address, $professional_summary,
        $job_sector_preference, $skills_json, $experiences_json, $educations_json, $creation_date, $username
    );

    // Execute the statement
    if ($stmt->execute()) {
        $response_message = "Your CV has been saved successfully!";
        $message_type = "success";
    } else {
        $response_message = "Error: " . $stmt->error;
        $message_type = "error";
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    $response_message = "Invalid request.";
    $message_type = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CV Submission Status</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            padding-top: 50px;
            text-align: center;
        }
        .container {
            max-width: 600px;
        }
        .message {
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>CV Submission Status</h2>
        <div id="responseMessage" class="alert alert-<?php echo ($message_type == 'success') ? 'success' : 'danger'; ?> message">
            <?php echo htmlspecialchars($response_message); ?>
        </div>
        <a href="cv.php" class="btn btn-primary">Back to CV Builder</a>
    </div>

    <!-- Include Bootstrap JS and dependencies (Optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        // Hide the message after 5 minutes (300000 milliseconds)
        setTimeout(function() {
            $('#responseMessage').fadeOut('slow');
        }, 300000); // 300,000 milliseconds = 5 minutes
    </script>
</body>
</html>
