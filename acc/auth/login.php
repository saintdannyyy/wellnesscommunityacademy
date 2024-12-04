<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Login</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Login</h1>
  <div class="container">
  <form method="POST" action="">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <input type="submit" value="Log In" style="background-color: #e65c00; color:white; cursor:pointer">
</form>
<p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>

<?php
require('../../config/loadENV.php');
if($_ENV['APP_ENV'] === 'prod') {
    error_reporting(0);
    ini_set('display_errors', '0');
} else if($_ENV['APP_ENV'] === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}
// Include database connection
include('../../conn/conn.php');

// Include SweetAlert library
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Collect form data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Information',
                        text: 'Please provide both email and password.',
                        timer: 2000, // 2-second timeout
                        timerProgressBar: true
                    });
                });
            </script>";
        exit();
    }

    try {
        // Prepare and execute query to fetch customer data
        $query = "SELECT id, name, email, phone, password FROM customers WHERE email = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch customer data
            $customer = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $customer['password'])) {
                // Start session and set session variables
                session_start();
                $_SESSION['customer_id'] = $customer['id'];
                $_SESSION['customer_name'] = $customer['name'];
                $_SESSION['customer_email'] = $customer['email'];
                $_SESSION['customer_phone'] = $customer['phone'];

                // Show success alert and redirect to the intended page
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sign-In Successful',
                            text: 'You have successfully signed in.',
                            timer: 2000, // 2-second timeout
                            timerProgressBar: true
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer || result.isConfirmed) {
                                window.location.href = '../../';
                            }
                        });
                    });
                </script>";
            } else {
                // Invalid password
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Sign-In Failed',
                                text: 'Invalid email or password.',
                                timer: 2000, // 2-second timeout
                                timerProgressBar: true
                            });
                        });
                    </script>";
            }
        } else {
            // User not found
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sign-In Failed',
                            text: 'Invalid email or password.',
                            timer: 2000, // 2-second timeout
                            timerProgressBar: true
                        });
                    });
                </script>";
        }
    } catch (Exception $e) {
        // Show database error alert
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Error',
                        text: 'Error: " . $e->getMessage() . "',
                        timer: 2000, // 2-second timeout
                        timerProgressBar: true
                    });
                });
            </script>";
    }
}
?>