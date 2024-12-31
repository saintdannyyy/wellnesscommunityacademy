<?php
    session_start();

    // Enable error reporting for debugging in development environment
    require_once __DIR__ . '../../../config/loadENV.php';

    //Environment variables
    if ($_ENV['APP_ENV'] === 'dev') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $adminMail = $_ENV['ADMIN_dev_EMAIL'];
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        $adminMail = $_ENV['ADMIN_EMAIL'];
    }

    // Start session and check if the customer is already logged in
    if (isset($_SESSION['customer_id'])) {
        header('Location: ../../');
        exit();
    }

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;


    require '../../PHPMailer-master/src/Exception.php';
    require '../../PHPMailer-master/src/PHPMailer.php';
    require '../../PHPMailer-master/src/SMTP.php';

    // Include database connection
    include('../../conn/conn.php');

    // Decode referral code
    function decodeReferralId($referralCode)
    {
        $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
        return openssl_decrypt(base64_decode($referralCode), 'aes-256-cbc', $key, 0, substr($key, 0, 16));
    }

    // Extract and decode referral code from the URL if available
    $referralCodeFromUrl = isset($_GET['rf']) ? htmlspecialchars(trim($_GET['rf'])) : '';
    if (!empty($referralCodeFromUrl)) {
        $referralCodeFromUrl = decodeReferralId($referralCodeFromUrl);
    }

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // Process registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize user input
        $name = htmlspecialchars(trim($_POST['name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phoneNumber = htmlspecialchars(trim($_POST['phone']));
        $password = htmlspecialchars($_POST['password']);
        $isAffiliate = 1;
        $referralCode = !empty($_POST['referral_code']) ? htmlspecialchars(trim($_POST['referral_code'])) : '';

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                            icon: 'error',
                            title: 'Invalid Email',
                            text: 'Please enter a valid email address..',
                            timer: 2000, // 2-second timeout
                            timerProgressBar: true
                        });
                    });
                </script>";
            exit();
        }

        // Validate phone number
        if (!preg_match("/^\+?[0-9]{10,15}$/", $phoneNumber)) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                            icon: 'error',
                            title: 'Invalid Phone Number',
                            text: 'The phone number you entered is not valid.',
                            timer: 2000, // 2-second timeout
                            timerProgressBar: true
                        });
                    });
                </script>";
            exit();
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Validate referral code
            $referredAffiliateId = null;
            if (!empty($referralCode)) {
                $stmt = $mysqli->prepare("SELECT id FROM affiliates WHERE id = ?");
                $stmt->bind_param('s', $referralCode);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $referredAffiliateId = $result->fetch_assoc()['id'];
                } else {
                    echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                            icon: 'error',
                            title: 'Invalid Referral Code',
                            text: 'The referral code you entered doesnt exist',
                            timer: 2000, // 2-second timeout
                            timerProgressBar: true
                        });
                    });
                </script>";
                    exit();
                }
            }

            // Send email notifications
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['SMTP_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['SMTP_USER'];
                $mail->Password = $_ENV['SMTP_PWD'];
                $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
                $mail->Port = $_ENV['SMTP_PORT'];

                // Admin notification email
                $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
                $mail->addAddress($adminMail);
                $mail->addBCC($_ENV['BCC_EMAIL']);
                // $mail->addBCC('seshun65@gmail.com');
                $mail->isHTML(true);
                $mail->Subject = 'New User Registration';
                $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.6;
                                background-color: #f9f9f9;
                                color: #333;
                                margin: 0;
                                padding: 0;
                            }
                            .email-container {
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                border: 1px solid #ddd;
                                border-radius: 8px;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                overflow: hidden;
                            }
                            .email-header {
                                background-color: #4CAF50;
                                color: #ffffff;
                                padding: 15px;
                                text-align: center;
                                font-size: 18px;
                                font-weight: bold;
                            }
                            .email-body {
                                padding: 20px;
                            }
                            .email-body p {
                                margin-bottom: 15px;
                            }
                            .email-body ul {
                                list-style: none;
                                padding: 0;
                                margin: 0;
                            }
                            .email-body li {
                                margin: 10px 0;
                                padding: 8px;
                                background: #f4f4f4;
                                border-radius: 4px;
                            }
                            .email-body li strong {
                                color: #333;
                            }
                            .email-footer {
                                background-color: #f4f4f4;
                                color: #555;
                                text-align: center;
                                padding: 10px;
                                font-size: 12px;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='email-header'>
                                New User Registration
                            </div>
                            <div class='email-body'>
                                <p>A new affiliate has registered on Wellness Community Academy:</p>
                                <ul>
                                    <li><strong>Name:</strong> $name</li>
                                    <li><strong>Email:</strong> $email</li>
                                    <li><strong>Phone Number:</strong> $phoneNumber</li>
                                </ul>
                            </div>
                            <div class='email-footer'>
                                This is an automated notification. Please do not reply to this email.
                            </div>
                        </div>
                    </body>
                    </html>";


                if ($mail->send()) {
                    // Send welcome email to user
                    $mail->clearAddresses(); // Clear previous recipients
                    $mail->addAddress($email);
                    $mail->addBCC($_ENV['BCC_EMAIL']);
                    $mail->Subject = 'Welcome to Wellness Community Academy!';
                    $mail->Body = "
                    <!DOCTYPE html>
                    <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                line-height: 1.8;
                                background-color: #f4f4f4;
                                color: #333;
                                margin: 0;
                                padding: 0;
                            }
                            .email-container {
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                border: 1px solid #ddd;
                                border-radius: 8px;
                                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                overflow: hidden;
                            }
                            .email-header {
                                background-color: #4CAF50;
                                color: #ffffff;
                                padding: 20px;
                                text-align: center;
                                font-size: 22px;
                                font-weight: bold;
                            }
                            .email-body {
                                padding: 20px;
                            }
                            .email-body p {
                                margin-bottom: 15px;
                            }
                            .email-body a {
                                color: #4CAF50;
                                text-decoration: none;
                                font-weight: bold;
                            }
                            .email-body a:hover {
                                text-decoration: underline;
                            }
                            .email-footer {
                                background-color: #f4f4f4;
                                color: #555;
                                text-align: center;
                                padding: 10px;
                                font-size: 12px;
                                border-top: 1px solid #ddd;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='email-header'>
                                Welcome to Wellness Community Academy
                            </div>
                            <div class='email-body'>
                                <p>Dear $name,</p>
                                <p>Thank you for joining Wellness Community Academy. We’re excited to have you onboard.</p>
                                <p>Your affiliate journey starts here. Login to your dashboard to explore: 
                                    <a href='https://wellnesscommunityacademy.com/affiliate'>Login</a>
                                </p>
                                <p>Warm Regards,<br>Wellness Community Academy Team</p>
                            </div>
                            <div class='email-footer'>
                                © 2024 Wellness Community Academy. All Rights Reserved.
                            </div>
                        </div>
                    </body>
                    </html>";

                    if ($mail->send()) {
                        // Insert customer into the database
                        $stmt = $mysqli->prepare("INSERT INTO customers (name, email, phone, password, affiliate, affiliate_referrer_id) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param('ssssii', $name, $email, $phoneNumber, $hashedPassword, $isAffiliate, $referredAffiliateId);

                        if ($stmt->execute()) {
                            $stmt->close();
                            // If the user opted to be an affiliate, add to the affiliates table
                            if ($isAffiliate === 1) {
                                // // Helper function: Generate unique affiliate ID
                                // function generateUniqueAffiliateId()
                                // {
                                //     return 'AFF' . time() . strtoupper(substr(md5(uniqid()), 0, 6));
                                // }
                                // $affiliateId = generateUniqueAffiliateId();

                                $customerId = $mysqli->insert_id; // Get the newly inserted customer ID
                                $stmtAffiliate = $mysqli->prepare("INSERT INTO affiliates (customer_id, referrer_id, created_at) VALUES (?, ?, NOW())");
                                $stmtAffiliate->bind_param('ii', $customerId, $referralCode);

                                if (!$stmtAffiliate->execute()) {
                                    throw new Exception("Database error: " . $stmtAffiliate->error);
                                }
                                echo "<script>
                                    document.addEventListener('DOMContentLoaded', function () {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Registration Successful',
                                            text: 'You have successfully created your affiliate account. An email has been sent to you for confirmation.',
                                            timer: 2000, // 2-second timeout
                                            timerProgressBar: true
                                        }).then(function() {
                                            window.location.href = 'login.php';
                                        });

                                    });
                                </script>";
                                exit();
                            }
                        }
                    } else {
                        echo "<script>document.addEventListener('DOMContentLoaded', function() {Swal.fire('Email Error', 'We couldn't not send you a welcome email.<br>Try again later', 'error');});</script>";
                    }
                } else {
                    echo "<script>document.addEventListener('DOMContentLoaded', function() {Swal.fire('Email Error', 'Could not notify the admin of the new registration.<br>Try again later', 'error');});</script>";
                }
            } catch (Exception $e) {
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Network Issue',
                            text: 'Failed to send email.Please check your network.',
                            timer: 3000, // 3-second timeout
                            timerProgressBar: true
                        });
                    });
                    console.log('Failed to send email. Error: " . $e->getMessage() . "');
                </script>";
                // echo json_encode(["status" => "error", "message" => "Failed to send email. Error: " . $e->getMessage()]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            echo "<script>document.addEventListener('DOMContentLoaded', function() {Swal.fire('Registration Error', 'An error occurred. Please try again later.', 'error');});</script>";
        }
        $mysqli->close();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Affiliate Registration</h1>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="referral_code">Referral Code (optional):</label>
            <input type="text" id="referral_code" name="referral_code"
                value="<?php echo $referralCodeFromUrl; ?>">

            <button type="submit" id="registerButton">Register</button>
            <script>
                document.querySelector('form').addEventListener('submit', function() {
                    const buttonwhenloading = document.getElementById('registerButton');
                    buttonwhenloading.disabled = true;
                    buttonwhenloading.style.backgroundColor = 'grey';
                    buttonwhenloading.innerHTML = 'Creating Account....'
                });
            </script>
        </form>
        <p>Already have an account? <a href="login.php">Sign In Here</a></p>
    </div>
</body>

</html>