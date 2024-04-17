<?php
session_start();

// Check if the request method is POST
if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the username and password (replace with your validation logic)
    if ($username === 'admin' && $password === 'pass') {
        // Authentication successful, set session variables
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php"); // Redirect to the admin dashboard
        exit();
    } else {
        // Authentication failed, redirect back to the login page with error parameter
        header("Location: admin_login.html?error=1");
        exit();
    }
}

// Redirect back to the login page if accessed directly
header("Location: admin_login.html");
exit();
?>
