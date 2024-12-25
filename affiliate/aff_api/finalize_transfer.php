<?php
session_start();
require '../../conn/conn.php';
require_once __DIR__ . '../../../config/loadENV.php';

// Environment settings
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $paystack_secret_key = $_ENV['PAYSTACK_SECRET_KEY_TEST'];
} else {
    ini_set('display_errors', 0);
    $paystack_secret_key = $_ENV['PAYSTACK_SECRET_KEY_LIVE'];
}

// Redirect if affiliate is not authenticated
if (!isset($_SESSION['affiliate_id'])) {
    header('Location: auth/login.php');
    exit();
}

// Initialize response
$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred.'
];

    $affiliate_id = $_SESSION['affiliate_id'];
    // Step 3: Finalize Transfer (OTP)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'], $_POST['transfer_code'])) {
    $otp = $_POST['otp'];
    // echo $otp;
    $transfer_code = $_POST['transfer_code'];

    $finalize_data = [
        'transfer_code' => $transfer_code,
        'otp' => $otp,
    ];

    $ch = curl_init('https://api.paystack.co/transfer/finalize_transfer');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($finalize_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $paystack_secret_key,
        "Content-Type: application/json"
    ]);

    $finalize_response = curl_exec($ch);
    curl_close($ch);

    $finalize_result = json_decode($finalize_response, true);

    if ($finalize_result['status']) {
        echo json_encode(['status' => 'success', 'message' => 'Transfer finalized successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to finalize transfer.', 'data' => $finalize_result]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Otp not received.']);
}
?>