<?php
session_start(); // Always start the session before modifying it
$user_id = $_SESSION['username'];

// Unset all session variables
session_unset(); 

// Destroy the session
session_destroy(); 
// Activity logging
// include_once('../db/login_conn.php');
// $stmt = $mysqli->prepare("INSERT INTO logs (user, action, ip_address, browser_info) VALUES (?, ?, ?, ?)");
// $stmt->bind_param("isss", $user_id, $action, $ip_address, $browser_info);

// Example data for demonstration purposes
// $action = $user_id . " logged out"; // Example action
// function getUserIP() {
    // if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // IP from shared internet
        // return $_SERVER['HTTP_CLIENT_IP'];
    // } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // IP passed from a proxy or load balancer
        // return $_SERVER['HTTP_X_FORWARDED_FOR'];
    // } else {
        // Direct IP address
        // return $_SERVER['REMOTE_ADDR'];
    // }
// }

// $ip_address = getUserIP();
// $browser_info = $_SERVER['HTTP_USER_AGENT']; // User’s browser info

// Execute the statement
// $stmt->execute();

// Redirect to the login page
header('Location: ../../');
exit();
?>