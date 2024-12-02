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
error_reporting(E_ALL);

// Display errors on the screen (for development purposes)
ini_set('display_errors', 1);

// Log errors to the server's error log (optional)
ini_set('log_errors', 1);
// Check if reference is provided
if (!isset($_GET['reference'])) {
    echo "<script>
        alert('No reference supplied');
        window.location.href = 'https://wellnesscommunityacademy.com/books';
    </script>";
    exit;
}

require('../conn/conn.php');

$secretKey = "sk_test_99a10e136ed567bbd74fb6569bfe8b4b5f35d5f3";

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

    // Save transaction details to your database (optional)
    require_once('../conn/conn.php');
    
    if ($mysqli) {
        $stmt = $mysqli->prepare("INSERT INTO `one-time_donation` (email, amount, reference) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $mysqli->error);
        }
        $status = 'success';
        $stmt->bind_param("sis", $email, $amount, $reference);
        $stmt->execute();
        $stmt->close();
        $mysqli->close();
    }

    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Donation Successful!',
            html: '<p>Thank you for your donation.<br>Reference: $reference<br>Amount Paid: GHS " . number_format($amount, 2) . "</p>',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'https://www.faithfulpathinternational.org/giving';
        });
    </script>";
} else {
    // Display failure alert with SweetAlert and redirect
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Donation Verification Failed!',
            text: 'Please contact support if you were charged.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'https://www.faithfulpathinternational.org/giving';
        });
    </script>";
}
?>