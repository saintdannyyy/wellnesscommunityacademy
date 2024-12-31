<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

</body>
</html>

<?php
session_start();

require('../conn/conn.php');

require('../config/loadENV.php');

$secretKey = ($_ENV['APP_ENV'] === 'prod')
    ? $_ENV['PAYSTACK_SECRET_KEY_LIVE']
    : $_ENV['PAYSTACK_SECRET_KEY_TEST'];

    if ($_ENV['APP_ENV'] === 'dev') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $adminMail = $_ENV['ADMIN_dev_EMAIL'];
    } else {
        ini_set('display_errors', 0);
        $adminMail = $_ENV['ADMIN_EMAIL'];
    }

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require('../config/loadENV.php');


// Get reference from query string
$reference = $_GET['reference'];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $reference);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $secretKey
]);

// Execute cURL request
$response = curl_exec($ch);
curl_close($ch);

// Decode the response
$responseData = json_decode($response, true);

// Check if the transaction was successful
if ($responseData['status'] && $responseData['data']['status'] === 'success') {
    // Retrieve transaction details
    $amount = $responseData['data']['amount'] / 100; // Convert from pesewas to GHS
    $email = $responseData['data']['customer']['email'];
    $phone = $responseData['data']['metadata']['custom_fields'][0]['value'];
    $course_no = $responseData['data']['metadata']['custom_fields'][1]['value'];
    $course_no = $responseData['data']['metadata']['custom_fields'][1]['value'];
    $course = $responseData['data']['metadata']['custom_fields'][2]['value'];
    $isAffiliate=0;

    // Checking if user already exists
    $sql = "SELECT * FROM customers WHERE email = ? OR phone = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $userExist = $stmt->get_result();
    $stmt->close();
    if ($userExist->num_rows == 0) {
        $referralCodeFromUrl = isset($_COOKIE['referralCode']) ? $_COOKIE['referralCode'] : null;
        $sqlAddCus = "INSERT INTO customers (name, email, phone, affiliate, affiliate_referrer_id) VALUES (?, ?, ?, ?, ?)";
        $stmtAddCus = $mysqli->prepare($sqlAddCus);
        $stmtAddCus->bind_param("sssii", $name, $email, $phone, $isAffiliate, $referralCodeFromUrl);
        $stmtAddCus->execute();
        $stmtAddCus->close();
    }

    $mail = new PHPMailer(true);
    try {
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
        // $mail->addBCC('seshun65@gmail.com');
        $mail->isHTML(true);
        $mail->Subject = 'NEW COURSE PURCHASE';
        $mail->Body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <style>
                    body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f9;
                    margin: 0;
                    padding: 0;
                    }
                    .container {
                        background-color: #fff;
                        max-width: 600px;
                        margin: 20px auto;
                        padding: 20px;
                        border-radius: 8px;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                    }
                    h1 {
                        font-size: 24px;
                        color: #333;
                        border-bottom: 2px solid #007bff;
                        padding-bottom: 10px;
                    }
                    p {
                        font-size: 16px;
                        color: #555;
                        margin: 10px 0;
                    }
                    .content {
                        margin-top: 20px;
                        padding: 15px;
                        background-color: #f9f9f9;
                        border-radius: 6px;
                        border: 1px solid #ddd;
                    }
                    .content p {
                        margin: 8px 0;
                    }
                    .footer {
                        margin-top: 20px;
                        text-align: center;
                        font-size: 14px;
                        color: #999;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h1>New Course Purchase Notification</h1>
                    <p>Hello <b>Wellness Community Academy</b>,</p>
                    <p>A new course has been purchase. Below are the details:</p>
                    <div class='content'>
                        <p><b>Email:</b> $email</p>
                        <p><b>Book Purchased:</b> $course</p>
                        <p><b>Amount Paid:</b> GHC $amount</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated notification from the Wellness Community Academy system.</p>
                    </div>
                </div>
            </body>
            </html>";

        if ($mail->send()) {
            // Customer Thank-You Email
            $mail->clearAddresses();
            $mail->clearBCCs();
            $mail->addAddress($email);
            $mail->addBCC($_ENV['BCC_EMAIL']);

            // Attach the course file if it exists
            // if ($bookpath && file_exists($bookpath)) {
            //     $mail->addAttachment($bookpath, basename($bookpath));
            // }
            $mail->isHTML(true);
            $mail->Subject = 'Thank You For Your Purchase';
            $mail->Body = "
                <!DOCTYPE html>
                <html lang='en'>
                    <head>
                        <meta charset='UTF-8'>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                            .container { padding: 20px; }
                            h1 { color: #007bff; }
                            p { font-size: 1rem; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h1>Thank You for Your Purchase!</h1>
                            <p>Dear $email,</p>
                            <p>Thank you for purchasing <b>$course</b> from Wellness Community Academy.</p>
                            <p>Your payment of GHC $amount has been successfully processed. The book is attached to this email for your convenience.</p>
                            <p>If you have any questions or need support, feel free to contact us.</p>
                            <p>Best regards,<br><b>Wellness Community Academy Team</b></p>
                        </div>
                    </body>
                </html>";
            $mail->send();
            
            $stmt = $mysqli->prepare("INSERT INTO sold_courses (course, email, amount, reference) VALUES (?, ?, ?, ?)");
            $status = 'success';
            $stmt->bind_param("ssis", $course, $email, $amount, $reference);
            $stmt->execute();
            $stmt->close();

            function addAffiliateEarnings($mysqli, $affiliate_id, $commission, $course_no, $course, $typeof_purchase) {
                try {
                    $stmt = $mysqli->prepare("INSERT INTO affiliate_earnings (affiliate_id, amount, product_id, product_name, typeof_purchase) VALUES (?, ?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $mysqli->error);
                    }
                    $stmt->bind_param("idiss", $affiliate_id, $commission, $course_no, $course, $typeof_purchase);
                    $stmt->execute();
                    $stmt->close();
                } catch (Exception $e) {
                    // Handle errors (log or display)
                    error_log("Affiliate Earnings Error: " . $e->getMessage());
                }
            }
            
            $typeof_purchase = "L1 Purchase";
            $sqlL1Affiliate = "SELECT affiliate_referrer_id FROM customers WHERE id = ?";
            // echo "making purchase:", $_SESSION['customer_id'], $_SESSION['customer_name'];
            $stmtL1 = $mysqli->prepare($sqlL1Affiliate);
            $stmtL1->bind_param("i", $_SESSION['customer_id']);
            $stmtL1->execute();
            $resultL1Affiliate = $stmtL1->get_result();
            $stmtL1->close();
            
            if ($resultL1Affiliate->num_rows > 0) {
                $rowL1Affiliate = $resultL1Affiliate->fetch_assoc();
                // echo "L1 affiliate for 15%:", $rowL1Affiliate['affiliate_referrer_id'];
                $affiliate_referrer_id = $rowL1Affiliate['affiliate_referrer_id'];
            
                if ($affiliate_referrer_id != 0) {
                    $affiliate_commission = $amount * 0.15;
                    addAffiliateEarnings($mysqli, $affiliate_referrer_id, $affiliate_commission, $course_no, $course, $typeof_purchase);
            
                    // Check for higher affiliate (L2)
                    $sqlL2Affiliate = "SELECT referrer_id FROM affiliates WHERE id = ?";
                    $stmtL2 = $mysqli->prepare($sqlL2Affiliate);
                    $stmtL2->bind_param("i", $affiliate_referrer_id);
                    $stmtL2->execute();
                    $resultL2Affiliate = $stmtL2->get_result();
                    $stmtL2->close();
            
                    if ($resultL2Affiliate->num_rows > 0) {
                        $rowL2Affiliate = $resultL2Affiliate->fetch_assoc();
                        // echo "L2 affiliate for 2%:", $rowL2Affiliate['referrer_id'];
                        $higher_affiliate_referrer_id = $rowL2Affiliate['referrer_id'];
            
                        if ($higher_affiliate_referrer_id != 0) {
                            $typeof_purchase = "L2 Purchase";
                            $higher_affiliate_commission = $amount * 0.02;
                            addAffiliateEarnings($mysqli, $higher_affiliate_referrer_id, $higher_affiliate_commission, $course_no, $course, $typeof_purchase);
                        }
                    }
                }
            }
    
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Course Purchase Successful!',
                    html: '<p>Thank you for your purchase.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'https://wellnesscommunityacademy.com/courses';
                });
            </script>";
        } else {
            // Display failure alert with SweetAlert and redirect
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to send email!',
                    text: 'Please contact support if you were charged.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'https://wellnesscommunityacademy.com/books';
                });
            </script>";
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Failed to send email. Error: " . $e->getMessage()]);
    }    
    
    } else {
        echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Payment Verification Failed',
                        text: 'Please contact support if you were charged.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'https://wellnesscommunityacademy.com/books';
                    });
                </script>";
}
$mysqli->close();
?>