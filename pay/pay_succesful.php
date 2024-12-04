<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head
<body>

</body>
</html>

<?php
// // Check if reference is provided
// if (!isset($_GET['reference'])) {
//     echo "<script>
//         alert('No reference supplied');
//         window.location.href = 'https://wellnesscommunityacademy.com/books';
//     </script>";
//     exit;
// }

require('../conn/conn.php');
require('../config/loadENV.php');

$secretKey = $_ENV['PAYSTACK_SECRET_KEY'];

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
    $bookId = $responseData['data']['metadata']['custom_fields'][1]['value'];

    // Save transaction details to your database (optional)
    require_once('../conn/conn.php');
    
    if ($mysqli) {
        $stmt = $mysqli->prepare("INSERT INTO Transactions (reference, email, book_id, amount, status) VALUES (?, ?, ?, ?, ?)");
        $status = 'success';
        $stmt->bind_param("ssids", $reference, $email, $bookId, $amount, $status);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Payment Successful!',
            html: '<p>Thank you for your purchase.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'https://wellnesscommunityacademy.com/books';
        });
    </script>";
} else {
    // Display failure alert with SweetAlert and redirect
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
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