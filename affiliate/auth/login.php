<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Affiliate Login</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Affiliate Login</h1>
  <div class="container">
  <form method="POST" action="login.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
        
    <input type="submit" id="logInButton" value="Log In" style="background-color: #e65c00; color:white; cursor:pointer">
    <script>
        document.querySelector('form').addEventListener('submit', function() {
            const inputWhenloading = document.getElementById('logInButton');
            inputWhenloading.disabled = true;
            inputWhenloading.style.backgroundColor = 'grey';
            inputWhenloading.innerHTML = 'Logging In...'
        });
            </script>
</form>
<p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>

<?php
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
            // Prepare and execute query to fetch user data along with affiliate details
            $query = "SELECT c.id AS customer_id, c.name AS customer_name, c.email AS customer_email, c.password, a.id
                FROM customers c JOIN affiliates a ON c.id = a.customer_id WHERE c.email = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result->num_rows > 0) {
                // Fetch user and affiliate data
                $user = $result->fetch_assoc();
        
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Start session and set session variables
                    session_start();
                    $_SESSION['customer_id'] = $user['customer_id'];
                    $_SESSION['customer_name'] = $user['customer_name'];
                    $_SESSION['customer_email'] = $user['customer_email'];
                    $_SESSION['affiliate_id'] = $user['id'];
                    // $_SESSION[''] = $user['affiliate_id'];
                // Show success alert and redirect to dashboard
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
                                    window.location.href = '../index.php';
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
            // User not found or not an affiliate
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sign-In Failed',
                            text: 'You must be a registered affiliate to log in.',
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