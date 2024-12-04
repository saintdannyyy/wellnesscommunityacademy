<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Program Payment</title>
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

// Example usage
// echo "Using Paystack secret Key: " . $secretKey;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

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
    $prog = $responseData['data']['metadata']['custom_fields'][1]['value'];
//     if (empty($prog)) {
//     echo 'Program information is not available.';
// } else {
//     echo $prog;
// }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PWD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
        $mail->addAddress('seshun65@gmail.com','cooperdockeryhealth@gmail.com');
        $mail->addBCC('saintdannyyy@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'NEW PROGRAM PURCHASE';
        $mail->Body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <style>/* Your CSS styling here */</style>
            </head>
            <body>
                <div class='container'>
                    <h1>PROGRAM PURCHASE</h1>
                    <p>Hello <b>Wellness Community Academy</b>,</p>
                    <p>$email just made payment for a program. Here are the details:</p>
                    <div class='content'>
                        <p><b>Email:</b> $email</p>
                        <p><b>Program Purchased:</b> $prog</p>
                        <p><b>Amount Paid:</b>GHC $amount</p>
                    </div>
                </div>
            </body>
            </html>";

        if ($mail->send()) {
            $stmt = $mysqli->prepare("INSERT INTO sold_programs (program, email, amount, reference) VALUES (?, ?, ?, ?)");
            $status = 'success';
            $stmt->bind_param("ssis", $prog, $email, $amount, $reference);
            $stmt->execute();
            $stmt->close();
            
            function addAffiliateEarnings($mysqli, $affiliate_id, $commission, $product, $typeof_purchase) {
                try {
                    $stmt = $mysqli->prepare("INSERT INTO affiliate_earnings (affiliate_id, amount, product, typeof_purchase) VALUES (?, ?, ?, ?)");
                    if (!$stmt) {
                        throw new Exception("Prepare failed: " . $mysqli->error);
                    }
                    $stmt->bind_param("idss", $affiliate_id, $commission, $product, $typeof_purchase);
                    $stmt->execute();
                    $stmt->close();
                } catch (Exception $e) {
                    // Handle errors (log or display)
                    error_log("Affiliate Earnings Error: " . $e->getMessage());
                }
            }
            
            $typeof_purchase = "L1 Purchase";
            $sqlL1Affiliate = "SELECT affiliate_referrer_id FROM customers WHERE id = ?";
            echo "making purchase:", $_SESSION['customer_id'], $_SESSION['customer_name'];
            $stmtL1 = $mysqli->prepare($sqlL1Affiliate);
            $stmtL1->bind_param("i", $_SESSION['customer_id']);
            $stmtL1->execute();
            $resultL1Affiliate = $stmtL1->get_result();
            $stmtL1->close();
            
            if ($resultL1Affiliate->num_rows > 0) {
                $rowL1Affiliate = $resultL1Affiliate->fetch_assoc();
                echo "L1 affiliate for 15%:", $rowL1Affiliate['affiliate_referrer_id'];
                $affiliate_referrer_id = $rowL1Affiliate['affiliate_referrer_id'];
            
                if ($affiliate_referrer_id != 0) {
                    $affiliate_commission = $amount * 0.15;
                    addAffiliateEarnings($mysqli, $affiliate_referrer_id, $affiliate_commission, $prog, $typeof_purchase);
            
                    // Check for higher affiliate (L2)
                    $sqlL2Affiliate = "SELECT referrer_id FROM affiliates WHERE id = ?";
                    $stmtL2 = $mysqli->prepare($sqlL2Affiliate);
                    $stmtL2->bind_param("i", $affiliate_referrer_id);
                    $stmtL2->execute();
                    $resultL2Affiliate = $stmtL2->get_result();
                    $stmtL2->close();
            
                    if ($resultL2Affiliate->num_rows > 0) {
                        $rowL2Affiliate = $resultL2Affiliate->fetch_assoc();
                        echo "L2 affiliate for 2%:", $rowL2Affiliate['referrer_id'];
                        $higher_affiliate_referrer_id = $rowL2Affiliate['referrer_id'];
            
                        if ($higher_affiliate_referrer_id != 0) {
                            $typeof_purchase = "L2 Purchase";
                            $higher_affiliate_commission = $amount * 0.02;
                            addAffiliateEarnings($mysqli, $higher_affiliate_referrer_id, $higher_affiliate_commission, $prog, $typeof_purchase);
                        }
                    }
                }
            }
            
    
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Program Purchase Successful!',
                    html: '<p>Thank you for your purchase.<br>You will receive an email soon.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'https://wellnesscommunityacademy.com/programs';
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
    // Display failure alert with SweetAlert and redirect
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Payment Verification Failed!',
            text: 'Please contact support if you were charged.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'https://wellnesscommunityacademy.com/programs';
        });
    </script>";
}
mysqli_close($mysqli);
?>