<?php
session_start();
include('../../conn/conn.php');

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $username = filter_var(trim($_POST['username']));
    $password = trim($_POST['password']);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Verify the entered password with the hashed password from the database
        if (password_verify($password, $row['password'])) {
            // If the password is correct, store user data in session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $row['role'];
            $_SESSION['logged_in'] = true; // Optional: flag to check if the user is logged in

           // Activity logging
            // $stmt = $mysqli->prepare("INSERT INTO logs (user, action, ip_address, browser_info) VALUES (?, ?, ?, ?)");
            // $stmt->bind_param("isss", $user_id, $action, $ip_address, $browser_info);

            // Example data for demonstration purposes
            // $user_id = $_SESSION['username'];
            // $action = $_SESSION['username'] . " logged in"; // Example action
            // function getUserIP() {
            //     if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    // IP from shared internet
                //     return $_SERVER['HTTP_CLIENT_IP'];
                // } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    // IP passed from a proxy or load balancer
                //     return $_SERVER['HTTP_X_FORWARDED_FOR'];
                // } else {
                    // Direct IP address
            //         return $_SERVER['REMOTE_ADDR'];
            //     }
            // }
            
            // $ip_address = getUserIP();
            // $browser_info = $_SERVER['HTTP_USER_AGENT']; // User’s browser info

            // Execute the statement
            // $stmt->execute();



            // Send a success response in JSON format
            echo json_encode([
                "status" => "success",
                "message" => "Login successful!  Redirecting to dashboard..."
            ]);
        } else {
            // If the password is incorrect, send an error message
            echo json_encode([
                "status" => "error",
                "message" => "Invalid password!"
            ]);
        }
    } else {
        // If the user is not found, send an error message
        echo json_encode([
            "status" => "error",
            "message" => "No user found"
        ]);
    }

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();
} else {
    // If the request method is not POST, return an error
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
?>