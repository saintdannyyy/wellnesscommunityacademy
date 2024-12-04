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

$reference = $_GET['reference'];

// Initialize cURL for Paystack API verification
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $reference);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $secretKey]);
$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if ($responseData['status'] && $responseData['data']['status'] === 'success') {
    $amount = $responseData['data']['amount'] / 100; 
    $email = $responseData['data']['customer']['email'];
    $name = $responseData['data']['metadata']['custom_fields'][3]['value'];
    $duration = $responseData['data']['metadata']['custom_fields'][1]['value'];
    $datetime = $responseData['data']['metadata']['custom_fields'][2]['value'];
    $phone = $responseData['data']['metadata']['custom_fields'][0]['value'];
    $reason = $responseData['data']['metadata']['custom_fields'][4]['value'];
    
    $visitDateFormatted = date("F j, Y \a\\t g:i A", strtotime($datetime));
    
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
        $mail->addAddress('seshun65@gmail.com');
        $mail->addBCC('saintdannyyy@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'New Virtual Meeting Request';
        $mail->Body = "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <style>/* Your CSS styling here */</style>
            </head>
            <body>
                <div class='container'>
                    <h1>Virtual Meeting Request</h1>
                    <p>Hello <b>Wellness Community Academy</b>,</p>
                    <p>$name wants to meet you on $visitDateFormatted for $duration minutes. Here are the details:</p>
                    <div class='content'>
                        <p><b>Name:</b> $name</p>
                        <p><b>Email:</b> $email</p>
                        <p><b>Visit Date:</b> $visitDateFormatted</p>
                        <p><b>Message:</b> $reason</p>
                        <p><b>Duration:</b> $duration minutes</p>
                        <p><b>Contact:</b> $phone</p>
                    </div>
                </div>
            </body>
            </html>";

        if ($mail->send()) {
            include_once('conn/conn.php');    
            $stmt = $mysqli->prepare("INSERT INTO booked_appointments (name, email, number, visit_date, message, duration, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidsii", $name, $email, $phone, $datetime, $reason, $duration, $amount);
            $stmt->execute();
            $stmt->close();
            $mysqli->close();

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Booking Successful!',
                    html: '<p>Thank you for booking.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'https://wellnesscommunityacademy.com';
                });
            </script>";
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to send email."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Failed to send email. Error: " . $e->getMessage()]);
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Payment Verification Failed!',
            text: 'Please contact support if you were charged.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'https://wellnesscommunityacademy.com/books';
        });
    </script>";
}
?>


<!--<!DOCTYPE html>-->
<!--<html lang="en">-->

<!--<head>-->
<!--     <meta charset="UTF-8">-->
<!--     <title>Payment Status</title>-->
<!--     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>-->
<!-- </head-->
<!-- <body>-->
<!-- </body>-->
<!--</html>-->

<?php
//     use PHPMailer\PHPMailer\PHPMailer;
//     use PHPMailer\PHPMailer\Exception;
    
//     require 'PHPMailer-master/src/Exception.php';
//     require 'PHPMailer-master/src/PHPMailer.php';
//     require 'PHPMailer-master/src/SMTP.php';
// // Check if reference is provided
// if (!isset($_GET['reference'])) {
//     echo "<script>
//         alert('No reference supplied');
//         window.location.href = 'https://wellnesscommunityacademy.com/books';
//     </script>";
//     exit;
// }

// -------------------------------------------------------------------------------------------------------------------------------------
// payment verification for virtual meeting

// --------------------------------------------------------------------------------------------------------------------------------------------
// require('../conn/conn.php');

// $secretKey = "sk_test_99a10e136ed567bbd74fb6569bfe8b4b5f35d5f3";

// Get reference from query string
// $reference = $_GET['reference'];

// Initialize cURL
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, "https://api.paystack.co/transaction/verify/" . $reference);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     "Authorization: Bearer " . $secretKey
// ]);

// Execute cURL request
// $response = curl_exec($ch);
// curl_close($ch);

// Decode the response
// $responseData = json_decode($response, true);

// Check if the transaction was successful
// if ($responseData['status'] && $responseData['data']['status'] === 'success') {
    // Retrieve transaction details
    // $amount = $responseData['data']['amount'] / 100; // Convert from pesewas to GHS
    // $email = $responseData['data']['customer']['email'];
    // $name = $responseData['data']['metadata']['custom_fields'][3]['value'];
    // $duration = $responseData['data']['metadata']['custom_fields'][1]['value'];
    // $datetime = $responseData['data']['metadata']['custom_fields'][2]['value'];
    // $phone = $responseData['data']['metadata']['custom_fields'][0]['value'];
    // $reason = $responseData['data']['metadata']['custom_fields'][4]['value'];
    
    // Convert to a readable format
    // $visitDateFormatted = date("F j, Y \a\\t g:i A", strtotime($datetime));

    
        // $mail = new PHPMailer(true);
        // try {
            // Server settings
            // $mail->isSMTP();
            // $mail->Host = 'smtp.gmail.com';
            // $mail->SMTPAuth = true;
            // $mail->Username = 'danieltesla746@gmail.com';
            // $mail->Password = 'brotflirznvijgnl';
            //     $mail->SMTPSecure = 'tls';
            // $mail->Port = 587;
    
            // Recipients
            // $mail->setFrom('noreply@wellnesscommunityacademy.com', 'Wellness Community Academy');
            // $mail->addAddress('seshun65@gmail.com');
            // $mail->addBCC('saintdannyyy@gmail.com');
    
            // Content
            // $mail->isHTML(true);
            // $mail->Subject = 'New Virtual Meeting Request';
            // $mail->Body = "
            //     <!DOCTYPE html>
            //     <html lang='en'>
            //     <head>
            //         <meta charset='UTF-8'>
            //         <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            //         <style>
            //             body {
            //                 font-family: Arial, sans-serif;
            //                 background-color: #f4f4f4;
            //                 color: #333;
            //                 line-height: 1.6;
            //                 padding: 0;
            //                 margin: 0;
            //             }
            //             .container {
            //                 background-color: #ffffff;
            //                 padding: 20px;
            //                 border-radius: 10px;
            //                 box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            //                 max-width: 600px;
            //                 margin: 20px auto;
            //             }
            //             h1 {
            //                 color: #0056b3;
            //             }
            //             .content {
            //                 margin-top: 20px;
            //                 padding: 10px 0;
            //             }
            //             .content b {
            //                 color: #333;
            //             }
            //             .footer {
            //                 margin-top: 20px;
            //                 padding-top: 10px;
            //                 border-top: 1px solid #ddd;
            //                 font-size: 12px;
            //                 color: #777;
            //                 text-align: center;
            //             }
            //             a {
            //                 color: #0056b3;
            //                 text-decoration: none;
            //             }
            //             a:hover {
            //                 text-decoration: underline;
            //             }
            //             .btn {
            //                 background-color: #0056b3;
            //                 color: #ffffff;
            //                 padding: 10px 20px;
            //                 text-align: center;
            //                 display: inline-block;
            //                 border-radius: 5px;
            //                 text-decoration: none;
            //                 margin-top: 20px;
            //             }
            //             .btn:hover {
            //                 background-color: #004494;
            //             }
            //         </style>
            //     </head>
            //     <body>
            //         <div class='container'>
            //             <h1>Virtual Meeting Request</h1>
            //             <p>Hello there, <b>Wellness Community Academy</b>ðŸ‘‹,</p>
            //             <p>$name wants to meet you on $visitDateFormatted for $duration minutes. Here are the details:</p>
            //             <div class='content'>
            //                 <p><b>Name:</b> $name</p>
            //                 <p><b>Email:</b> $email</p>
            //                 <p><b>Visit Date:</b> $visitDateFormatted</p>
            //                 <p><b>Message:</b> $reason</p>
            //                 <p><b>Duration:</b> $duration minutes</p>
            //                 <p><b>Contact:</b> You can contact $name on <a href='tel:$phone'>$phone</a> or via <a href='mailto:$email'>$email</a>.</p>
            //             </div>
            //             <a href='https://wellnesscommunityacademy.com/admin' class='btn'>View Other Requests</a>
            //             <div class='footer'>
            //                 <p>&copy; 2024 Wellness Community Academy. All Rights Reserved.</p>
            //                 <p><a href='https://wellnesscommunityacademy.com'>Visit our website</a></p>
            //             </div>
            //         </div>
            //     </body>
            //     </html>";

            // if ($mail->send()) {
            //     include_once('conn/conn.php');    
        //         $stmt = $mysqli->prepare("INSERT INTO booked_appointments (name, email, number, visit_date, message, duration, amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        //         $stmt->bind_param("ssidsii", $name, $email, $phone, $datetime, $reason, $duration, $amount);
    
        //         if ($stmt->execute()) {
        //             echo json_encode(["status" => "success", "message" => "Appointment submitted successfully!"]);
        //         } else {
        //             echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
        //         }
    
        //         $stmt->close();
        //         $mysqli->close();
        //     } else {
        //         echo json_encode(["status" => "error", "message" => "Failed to send email."]);
        //     }
    
        // } catch (Exception $e) {
        //     echo json_encode(["status" => "error", "message" => "Failed to send email. Error: " . $e->getMessage()]);
        // }

    // Save transaction details to your database (optional)
//     require_once('../conn/conn.php');
    
//     if ($mysqli) {
//         $stmt = $mysqli->prepare("INSERT INTO booked_meetings (name, email, number, duration, cost, reference) VALUES (?, ?, ?, ?, ?, ?)");
//         $status = 'success';
//         $stmt->bind_param("ssiiis", $reference, $email, $bookId, $amount, $status, $reference);
//         $stmt->execute();
//         $stmt->close();
//         $mysqli->close();
//     }

//     echo "<script>
//         Swal.fire({
//             icon: 'success',
//             title: 'Payment Successful!',
//             html: '<p>Thank you for your purchase.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             window.location.href = 'https://wellnesscommunityacademy.com/books';
//         });
//     </script>";
// } else {
    // Display failure alert with SweetAlert and redirect
//     echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
//     echo "<script>
//         Swal.fire({
//             icon: 'error',
//             title: 'Payment Verification Failed!',
//             text: 'Please contact support if you were charged.',
//             confirmButtonText: 'OK'
//         }).then(() => {
//             window.location.href = 'https://wellnesscommunityacademy.com/books';
//         });
//     </script>";
// }
?>