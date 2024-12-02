<?php
    // Start the session
    session_start();

    // Check if the user is signed in
    if (!isset($_SESSION['username'])) {
        // Redirect to login page if not signed in
        header("Location: ../admin/auth/login.html");
        exit();
    }

    // Connect to prod database
    // $mysqli = new mysqli("localhost", "wellnes1_wellnes1", "@1ph@5t0rmerh05t", "wellnes1_ebooks");

    // Connect to the dev database
    $mysqli = new mysqli("localhost", "root", "", "wellness");
    
    // Check for database connection error
    if ($mysqli->connect_error) {
        // Log the error and show a generic error message
        error_log("Database connection failed: " . $mysqli->connect_error);
        die("A connection error occurred. Please try again later.");
    }

    // Further code logic here
?>
