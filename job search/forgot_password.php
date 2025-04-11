<?php
session_start();
include './pages/header.php';

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include database connection
    require_once './pages/db_connection.php';

    
    // Load Composer's autoloader
    require './vendor/autoload.php';

    $email = trim($_POST['email']);

    // Check if email exists
    if ($stmt = $conn->prepare('SELECT id, username FROM accounts WHERE email = ?')) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // User exists
            $stmt->bind_result($user_id, $username);
            $stmt->fetch();

            // Generate reset token
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // Update the user record with the reset token and expiry
            if ($update_stmt = $conn->prepare('UPDATE accounts SET reset_token = ?, reset_token_expires = ? WHERE email = ?')) {
                $update_stmt->bind_param('sss', $token, $expires, $email);
                $update_stmt->execute();

                // Send password reset email
                $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/job search/reset_password.php?token=' . $token;

                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'your_email';
                    $mail->Password = 'your _password';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    // Recipients
                    $mail->setFrom('your_mail', 'Job Seeker');
                    $mail->addAddress($email, $username);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body    = "Dear $username,<br><br>You requested a password reset. Click the link below to reset your password:<br><br><a href='$reset_link'>$reset_link</a><br><br>This link will expire in 1 hour.<br><br>If you did not request a password reset, please ignore this email.<br><br>Best regards,<br>Job Seeker Team";

                    $mail->send();
                    $success = 'A password reset link has been sent to your email address.';
                } catch (Exception $e) {
                    $error = 'Failed to send password reset email. Please try again.';
                }
            } else {
                $error = 'Database error. Please try again.';
            }
        } else {
            $error = 'No account found with that email address.';
        }
        $stmt->close();
    } else {
        $error = 'Database error. Please try again.';
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Job Seeker</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #dbd7df 30%, #d8e2ee 70%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            margin-top: 100px;
            max-width: 500px;
        }
        .card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .error-message, .success-message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <!-- Forgot Password Form -->
    <div class="container">
        <div class="card">
            <h3 class="text-center">Forgot Password</h3>
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success); ?>
                </div>
            <?php else: ?>
                <p class="text-center">Enter your email address to request a password reset.</p>
                <form action="forgot_password.php" method="post">
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email address" required>
                    </div>
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                    <!-- Login Link -->
                    <div class="text-center mt-3">
                        <p>Remembered your password? <a href="login.php">Login here</a></p>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <!-- Bootstrap JS and Dependencies -->
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
