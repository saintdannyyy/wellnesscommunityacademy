<?php
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '../../../config/loadENV.php';
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

// Include database connection
include('../../conn/conn.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer-master/src/Exception.php';
require '../../PHPMailer-master/src/PHPMailer.php';
require '../../PHPMailer-master/src/SMTP.php';

// Retrieve POST data
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phoneNumber = htmlspecialchars(trim($_POST['phone'] ?? ''));
$password = htmlspecialchars($_POST['password'] ?? '');
$referralCode = htmlspecialchars(trim($_POST['referral_code'] ?? ''));

// Validate input
if (!$name || !$email || !$phoneNumber || !$password) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

if (!preg_match("/^\+?[0-9]{10,15}$/", $phoneNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number.']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if email already exists
$stmt = $mysqli->prepare("SELECT id FROM customers WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email address already exists.']);
    exit;
}

// Validate referral code
$referredAffiliateId = null;
if (!empty($referralCode)) {
    $stmt = $mysqli->prepare("SELECT id FROM affiliates WHERE id = ?");
    $stmt->bind_param('s', $decodedReferral);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $referredAffiliateId = $result->fetch_assoc()['id'];
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid referral code.']);
        exit;
    }
}

// Insert into database
try {
    $stmt = $mysqli->prepare("INSERT INTO customers (name, email, phone, password, affiliate, affiliate_referrer_id) VALUES (?, ?, ?, ?, ?, ?)");
    $isAffiliate = 1; // Default affiliate value
    $stmt->bind_param('ssssii', $name, $email, $phoneNumber, $hashedPassword, $isAffiliate, $referredAffiliateId);

    if ($stmt->execute()) {
        echo "<script> console.log('User registered successfully.'); </script>";
        $stmt->close();
        $customerId = $mysqli->insert_id;
        // echo "<script> console.log('Customer ID: $customerId'); </script>";

        // Add affiliate record
        if ($isAffiliate === 1) {
            $stmtAffiliate = $mysqli->prepare("INSERT INTO affiliates (customer_id, referrer_id, created_at) VALUES (?, ?, NOW())");
            $stmtAffiliate->bind_param('ii', $customerId, $referredAffiliateId);
            $stmtAffiliate->execute();
            $affiliateId = $mysqli->insert_id;
            echo json_encode(['success' => true, 'message' => 'Registration successful.', 'id' => $affiliateId]);
        }

        $mail = new PHPMailer(true);
        // Referrer notification email
        if($referredAffiliateId) {
            // Send email to affiliate referrer
            $stmtEmailReferrer = $mysqli->prepare("SELECT email FROM customers WHERE id = ?");
            $stmtEmailReferrer->bind_param('i', $referredAffiliateId);
            $stmtEmailReferrer->execute();
            $resultEmailReferrer = $stmtEmailReferrer->get_result();
            $referrerEmail = $resultEmailReferrer->fetch_assoc()['email'];

            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PWD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
            $mail->addAddress($$referrerEmail);
            $mail->addBCC($_ENV['BCC_EMAIL']);
            $mail->isHTML(true);
            $mail->Subject = 'New Referred Affiliate Registration';
            $mail->Body = "A new affiliate has registered using your referral code. Below are their details:<br><br>Name: $name<br>Email: $email<br>Phone: $phoneNumber";
            $mail->send();
        }
        // Admin and new affiliate notification emails
        if($stmtAffiliate) {
            $stmtAffiliate->close();
            $mail->isSMTP();
            $mail->Host = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['SMTP_USER'];
            $mail->Password = $_ENV['SMTP_PWD'];
            $mail->SMTPSecure = $_ENV['SMTP_SECURE'];
            $mail->Port = $_ENV['SMTP_PORT'];

            $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
            $mail->addAddress($adminMail);
            $mail->addBCC($_ENV['BCC_EMAIL']);
            $mail->isHTML(true);
            $mail->Subject = 'New Affiliate Registration';
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
                    $mailStatus = 'sent';
                } else {
                    $mailStatus = 'unsent';
                }
                $stmtUpdateMailStatus = $mysqli->prepare("UPDATE customers SET mail_status = ? WHERE id = ?");
                $stmtUpdateMailStatus->bind_param('si', $mailStatus, $customerId);
                $stmtUpdateMailStatus->execute();
                $stmtUpdateMailStatus->close();
            } else {
            $mailStatus = 'unsent';
            $stmtUpdateMailStatus = $mysqli->prepare("UPDATE customers SET mail_status = ? WHERE id = ?");
            $stmtUpdateMailStatus->bind_param('si', $mailStatus, $customerId);
            $stmtUpdateMailStatus->execute();
            $stmtUpdateMailStatus->close();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register user.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to register user.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred during registration.', 'error' => $e->getMessage()]);
}

// Close database connection
$mysqli->close();
?>
