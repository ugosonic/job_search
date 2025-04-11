<?php
// Start the session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
  
// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo "<div class='not-logged-in'>";
    echo "<h1>Welcome, Visitor!</h1>";
    echo "<p>Access to this page is restricted to logged-in users only.</p>";
    echo "<a href='../login.php' class='btn-login'>Login</a> or <a href='../register.php' class='btn-register'>Register</a>";
    echo "</div>";
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "phplogin";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User-specific content goes here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .not-logged-in {
            text-align: center;
            margin-top: 50px;
        }
        .not-logged-in h1 {
            font-size: 2em;
            color: #333;
        }
        .not-logged-in p {
            font-size: 1.2em;
            color: #666;
        }
        .btn-login, .btn-register {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 1em;
            color: #fff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-login:hover, .btn-register:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Page content for logged-in users -->
</body>
</html>