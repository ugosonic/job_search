<?php
session_start();
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'phplogin';

// Create connection
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Check connection
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the data from the login form was submitted.
if (!isset($_POST['username'], $_POST['password'])) {
    // Set error message and redirect back to login page
    $_SESSION['error_message'] = 'Please fill in all required fields!';
    header('Location: login.php');
    exit();
}

$username = trim($_POST['username']);
$password = $_POST['password'];

// Prepare SQL statement to prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, usergroup FROM accounts WHERE username = ?')) {
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $usergroup);
        $stmt->fetch();
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Successful login, set session variables.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $username;
            $_SESSION['id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['user_id'] = $id;
            $_SESSION['usergroup'] = $usergroup; // Store user group in session

            // Redirect to appropriate home page based on user group.
            if ($usergroup === 'allusers') {
                header('Location: ./pages/home_allusers.php');
            } elseif ($usergroup === 'employers') {
                header('Location: ./pages/home_employers.php');
            } else {
                // Default redirect if usergroup is not recognized
                header('Location: ./pages/home.php');
            }
            exit();
        } else {
            // Incorrect password
            $_SESSION['error_message'] = 'Incorrect username or password!';
            header('Location: login.php');
            exit();
        }
    } else {
        // Incorrect username
        $_SESSION['error_message'] = 'Incorrect username or password!';
        header('Location: login.php');
        exit();
    }
    $stmt->close();
} else {
    // SQL statement failed to prepare
    $_SESSION['error_message'] = 'Database error: Could not prepare statement!';
    header('Location: login.php');
    exit();
}
?>
