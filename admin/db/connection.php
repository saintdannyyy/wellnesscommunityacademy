<?php
    // Start the session
    session_start();

    // Check if the user is signed in
    if (!isset($_SESSION['username'])) {
        // Redirect to login page if not signed in
        header("Location: ../admin/auth/login.html");
        exit();
    }

    require_once __DIR__ . '../../../config/loadENV.php';
    if ($_ENV['APP_ENV'] === 'dev') {
        $mysqli = new mysqli("localhost", "root", "", "wellness");
    } else {
        $mysqli = new mysqli("localhost", "wellnes1_wellnes1", "@1ph@5t0rmerh05t", "wellnes1_ebooks");
    }
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }    
    // Check for database connection error
    if ($mysqli->connect_error) {
        // Log the error and show a generic error message
        error_log("Database connection failed: " . $mysqli->connect_error);
        die("A connection error occurred. Please try again later.");
    }

    // Further code logic here
?>
