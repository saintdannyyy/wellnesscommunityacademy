<?php
session_start();
require '../../conn/conn.php';
require_once __DIR__ . '../../../config/loadENV.php';
// Environment settings
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

if (!isset($_SESSION['affiliate_id'])) {
    header('Location: auth/login.php');
    exit();
}

$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $affiliate_id = $_SESSION['affiliate_id'];

    // Fetch affiliate details and total earnings
    $stmt = $mysqli->prepare("SELECT account_name, account_number, bank_name FROM affiliates WHERE id = ?");
    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $affiliate = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$affiliate || !$affiliate['account_name'] || !$affiliate['account_number'] || !$affiliate['bank_name']) {
        $response['message'] = 'Payment details are missing. Please update your payment information.';
        echo json_encode($response);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT SUM(amount) AS total_earnings FROM affiliate_earnings WHERE affiliate_id = ? AND status = 'pending'");
    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $total_earnings = $result['total_earnings'] ?? 0;

    if ($total_earnings <= 0) {
        $response['message'] = 'No earnings available for payout.';
        echo json_encode($response);
        exit;
    }

    // Integrate Paystack Payout
    $paystack_secret_key = $_ENV['PAYSTACK_SECRET_KEY_TEST'];

    // Step 1: Create a Transfer Recipient
    $recipient_data = [
        'type' => 'nuban',
        'name' => $affiliate['account_name'],
        'account_number' => $affiliate['account_number'],
        'bank_code' => $affiliate['bank_name'], // Replace with the correct bank code
        'currency' => 'NGN',
    ];

    $ch = curl_init('https://api.paystack.co/transferrecipient');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($recipient_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $paystack_secret_key,
        'Content-Type: application/json',
    ]);

    $recipient_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $recipient_data = json_decode($recipient_response, true);

    if ($http_code === 200 && isset($recipient_data['status']) && $recipient_data['status']) {
        $recipient_code = $recipient_data['data']['recipient_code'];

        // Step 2: Initiate the Transfer
        $transfer_data = [
            'source' => 'balance',
            'amount' => $total_earnings * 100, // Convert to kobo
            'recipient' => $recipient_code,
            'reason' => 'Affiliate payout',
        ];

        $ch = curl_init('https://api.paystack.co/transfer');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transfer_data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $paystack_secret_key,
            'Content-Type: application/json',
        ]);

        $transfer_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $transfer_data = json_decode($transfer_response, true);

        if ($http_code === 200 && isset($transfer_data['status']) && $transfer_data['status']) {
            // Step 3: Mark payouts as complete in the database
            $stmt = $mysqli->prepare("UPDATE affiliate_earnings SET status = 'paid' WHERE affiliate_id = ?");
            $stmt->bind_param('i', $affiliate_id);
            $stmt->execute();
            $stmt->close();

            $response['status'] = 'success';
            $response['message'] = 'Payout request processed successfully.';
        } else {
            $response['message'] = 'Failed to process payout. Please try again later.';
        }
    } else {
        $response['message'] = 'Failed to create a transfer recipient. Please try again.';
    }
}

echo json_encode($response);
?>