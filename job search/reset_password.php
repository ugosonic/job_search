<?php
session_start();
include './pages/header.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    // Show the password reset form
    $token = $_GET['token'];
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Process the password reset
    // Include database connection
    require_once './pages/db_connection.php';

    $token = $_POST['token'];
    $new_password = $_POST['password'];

    // Validate new password (e.g., length, complexity)
    if (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Check if token is valid and not expired
        if ($stmt = $conn->prepare('SELECT id FROM accounts WHERE reset_token = ? AND reset_token_expires > NOW()')) {
            $stmt->bind_param('s', $token);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                // Token is valid
                $stmt->bind_result($id);
                $stmt->fetch();

                // Update the user's password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                if ($update_stmt = $conn->prepare('UPDATE accounts SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?')) {
                    $update_stmt->bind_param('si', $new_password_hash, $id);
                    if ($update_stmt->execute()) {
                        $success = 'Your password has been reset successfully.';
                    } else {
                        $error = 'Error updating password: ' . $update_stmt->error;
                    }
                    $update_stmt->close();
                } else {
                    $error = 'Database error: ' . $conn->error;
                }
            } else {
                $error = 'Invalid or expired token.';
            }
            $stmt->close();
        } else {
            $error = 'Database error: ' . $conn->error;
        }
        $conn->close();
    }
} else {
    $error = 'Invalid request.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Job Seeker</title>
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
    <!-- Reset Password Form -->
    <div class="container">
        <div class="card">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="success-message">
                    <?= htmlspecialchars($success); ?>
                    <div class="text-center mt-3">
                        <p><a href="login.php">Click here to login</a></p>
                    </div>
                </div>
            <?php elseif (isset($token)): ?>
                <h3 class="text-center">Reset Your Password</h3>
                <form action="reset_password.php" method="post">
                    <!-- New Password -->
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> New Password</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter a new password" required>
                    </div>
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </form>
            <?php else: ?>
                <div class="error-message">
                    Invalid request.
                </div>
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
