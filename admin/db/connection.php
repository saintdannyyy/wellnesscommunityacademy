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
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $mysqli = new mysqli($_ENV['DB_DEV_HOST'], $_ENV['DB_DEV_USER'], $_ENV['DB_DEV_PWD'], $_ENV['DB_DEV_NAME']);
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
        $mysqli = new mysqli($_ENV['DB_LIVE_HOST'], $_ENV['DB_LIVE_USER'], $_ENV['DB_LIVE_PWD'], $_ENV['DB_LIVE_NAME']);
    }
    if ($mysqli->connect_error) {
        // Log the error and show a generic error message
        error_log("Database connection failed: " . $mysqli->connect_error);
        die("A connection error occurred. Please try again later.");
    }
?>
