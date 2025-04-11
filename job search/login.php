<?php
session_start();
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if registration success message is set in session
if (isset($_SESSION['registration_success']) && $_SESSION['registration_success']) {
    $registrationSuccess = true;
    unset($_SESSION['registration_success']);
} else {
    $registrationSuccess = false;
}

// Check if error message is set in session
if (isset($_SESSION['error_message']) && $_SESSION['error_message']) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
} else {
    $errorMessage = '';
}

// Set cookies if they exist
$savedUsername = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
$savedPassword = isset($_COOKIE['password']) ? $_COOKIE['password'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Job Seeker</title>
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
        .login-container {
            margin-top: 50px;
        }
        .login-card {
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .login-card .form-control {
            border-radius: 5px;
        }
        .login-card .btn-primary {
            border-radius: 5px;
            padding: 10px;
            font-size: 18px;
            width: 100%;
        }
        .registration-success, .error-message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }
        .registration-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .remember-me {
            display: flex;
            align-items: center;
        }
        .remember-me input {
            margin-right: 5px;
        }
        .footer {
            margin-top: 50px;
            padding: 20px 0;
            background-color: #fff;
            text-align: center;
            color: #6c757d;
        }
        @media (max-width: 576px) {
            .login-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="index.html"><img src="../job search/images/jobseeker.png" alt="JobSeeker Logo"></a>
        <div class="ml-auto">
            <a href="register.php" class="btn btn-outline-primary">Register</a>
        </div>
    </nav>

    

    <!-- Login Form -->
    <div class="container login-container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <h3 class="text-center mb-4">Login to Your Account</h3>
                    <!-- Registration Success Message -->
    <?php if ($registrationSuccess): ?>
    <div class="container">
        <div id="registrationSuccess" class="registration-success text-center">
            Registration successful! Please log in.
        </div>
    </div>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (!empty($errorMessage)): ?>
    <div class="container">
        <div id="errorMessage" class="error-message">
            <?= htmlspecialchars($errorMessage); ?>
        </div>
    </div>
    <?php endif; ?>
                    <form action="authenticate.php" method="post">
                        <!-- Username -->
                        <div class="form-group">
                            <label for="username"><i class="fas fa-user"></i> Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" value="<?php echo htmlspecialchars($savedUsername); ?>" required>
                        </div>
                        <!-- Password -->
                        <div class="form-group">
                            <label for="password"><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" value="<?php echo htmlspecialchars($savedPassword); ?>" required>
                        </div>
                        <!-- User Group -->
                        <div class="form-group">
                            <label for="usergroup"><i class="fas fa-users"></i> Select User Group</label>
                            <select name="usergroup" id="usergroup" class="form-control" required>
                                <option value="allusers">All Users</option>
                                <option value="employers">Employers</option>
                            </select>
                        </div>
                        <!-- Remember Me -->
                        <div class="form-group remember-me">
                            <input type="checkbox" name="rememberme" id="rememberme">
                            <label for="rememberme">Remember Me</label>
                        </div>
                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Login</button>
                        <!-- Forgot Password Link -->
                        <div class="text-center mt-3">
                            <a href="forgot_password.php">Forgot your password?</a>
                        </div>
                    </form>
                </div>
                <!-- Additional Links -->
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
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
    <!-- Custom JS to hide messages after 5 seconds -->
    <script>
        setTimeout(function() {
            var errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
            var registrationSuccess = document.getElementById('registrationSuccess');
            if (registrationSuccess) {
                registrationSuccess.style.display = 'none';
            }
        }, 5000); // 5000 milliseconds = 5 seconds
    </script>
</body>
</html>
