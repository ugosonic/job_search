<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include './pages/header.php';

// Include database connection file
require_once './pages/db_connection.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require './vendor/autoload.php';

// Initialize variables
$error = '';
$success = '';

// Check if redirected with success message
if (isset($_GET['success'])) {
    $success = 'Registration successful! Please check your email for confirmation.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate form inputs
    if (!isset($_POST['username'], $_POST['email'], $_POST['password'], $_POST['usergroup'])) {
        $error = 'Please complete the registration form!';
    } else {
        // Sanitize and validate inputs
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $usergroup = $_POST['usergroup'];

        // Additional validation can be added here (e.g., email format, password strength)

        // Check if username already exists
        if ($stmt_username = $conn->prepare('SELECT id FROM accounts WHERE username = ?')) {
            $stmt_username->bind_param('s', $username);
            $stmt_username->execute();
            $stmt_username->store_result();

            if ($stmt_username->num_rows > 0) {
                $error = 'Username already exists!';
            }
            $stmt_username->close();
        } else {
            $error = 'Database error: ' . $conn->error;
        }

        // Check if email already exists
        if (empty($error)) {
            if ($stmt_email = $conn->prepare('SELECT id FROM accounts WHERE email = ?')) {
                $stmt_email->bind_param('s', $email);
                $stmt_email->execute();
                $stmt_email->store_result();

                if ($stmt_email->num_rows > 0) {
                    $error = 'Email already exists!';
                }
                $stmt_email->close();
            } else {
                $error = 'Database error: ' . $conn->error;
            }
        }

        // Proceed if no errors
        if (empty($error)) {
            // Insert new user into database
            if ($stmt_insert = $conn->prepare('INSERT INTO accounts (username, email, password, usergroup) VALUES (?, ?, ?, ?)')) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt_insert->bind_param('ssss', $username, $email, $password_hash, $usergroup);
                if ($stmt_insert->execute()) {
                    // Send email notification
                    $mail = new PHPMailer(true);
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
                        $mail->setFrom('your_email', 'Job Seeker');
                        $mail->addAddress($email, $username);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Registration Successful';
                        $mail->Body    = "Dear $username,<br><br>Your registration was successful!<br><br>Thank you for joining Job Seeker.<br><br>Best regards,<br>Job Seeker Team";

                        $mail->send();
                        // Registration success, redirect to avoid resubmission
                        header('Location: register.php?success=1');
                        exit();
                    } catch (Exception $e) {
                        // Email sending failed
                        $error = "Registration successful, but failed to send email. Error: " . $mail->ErrorInfo;
                    }
                } else {
                    $error = 'Error occurred during registration: ' . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                $error = 'Database error: ' . $conn->error;
            }
        }
        $conn->close();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Job Seeker</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- FontAwesome CSS CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #dbd7df 30%, #d8e2ee 70%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #fff;
            padding: 15px;
        }
        .navbar-brand {
            font-weight: bold;
            color: #007bff;
        }
        .navbar-brand:hover {
            color: #0056b3;
        }
        .register-container {
            margin-top: 50px;
        }
        .register-card {
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .register-card .form-control {
            border-radius: 5px;
        }
        .register-card .btn-primary {
            border-radius: 5px;
            padding: 10px;
            font-size: 18px;
            width: 100%;
        }
        .error-message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
        .footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #fff;
            text-align: center;
            color: #6c757d;
        }
        @media (max-width: 576px) {
            .register-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Registration Form -->
    <div class="container register-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="register-card">
                    <h3 class="text-center mb-4">Create Your Account</h3>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($success); ?>
                        </div>
                    <?php elseif (!empty($error)): ?>
                        <div class="error-message">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    <form action="register.php" method="post">
                        <!-- Username -->
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Enter a username" required>
                        </div>
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email address" required>
                        </div>
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter a password" required>
                        </div>
                        <!-- User Group -->
                        <div class="form-group">
                            <label for="usergroup"><i class="fas fa-users"></i> Select User Group</label>
                            <select name="usergroup" id="usergroup" class="form-control" required>
                                <option value="allusers">All Users</option>
                                <option value="employers">Employers</option>
                            </select>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Register</button>
                        <!-- Login Link -->
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; <?= date("Y"); ?> Job Seeker. All rights reserved.
    </div>

    <!-- Bootstrap JS and Dependencies (jQuery and Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
            integrity="sha384-DfXdzyPjeY2tMWV5ygIKW2Yq9mqznCC00PQ7dQoSTZItz/9q6paVSJtdJzxssMjc" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" 
            integrity="sha384-3mPhkqsLzptDrHkc+h1Gm/g6QxghXqtDsqh00KZRJ++mHdcoR2m+5dZ2ctnpERmJ" 
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" 
            integrity="sha384-OgVRvuATP1z7JjHLkuouMDkB7X1cQZo/3GsIju5RlEX3mcbsZccNEoR/j+e8ffY" 
            crossorigin="anonymous"></script>
</body>
</html>
