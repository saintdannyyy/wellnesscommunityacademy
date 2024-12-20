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

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $affiliate_id = $_SESSION['affiliate_id'];

    // Fetch affiliate payment details
    $stmt = $mysqli->prepare("SELECT account_name, phone_number, service_provider FROM affiliates WHERE id = ?");
    if (!$stmt) {
        $response['message'] = 'Database error: Unable to prepare statement.';
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $affiliate = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$affiliate || !$affiliate['account_name'] || !$affiliate['phone_number'] || !$affiliate['service_provider']) {
        $response['message'] = 'Payment details are missing. Please update your payment information.';
        echo json_encode($response);
        exit();
    }

    // Fetch total earnings for the affiliate
    $stmt = $mysqli->prepare("SELECT SUM(amount) AS total_earnings FROM affiliate_earnings WHERE affiliate_id = ? AND status = 'pending'");
    if (!$stmt) {
        $response['message'] = 'Database error: Unable to prepare statement.';
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $total_earnings = $result['total_earnings'] ?? 0;

    if ($total_earnings <= 0) {
        $response['message'] = 'No earnings available for payout.';
        echo json_encode($response);
        exit();
    }
        
    $amount = $total_earnings; // Payout amount
    echo $affiliate['account_name'];
    $phone ='233' . $affiliate['phone_number'];
    echo $phone;
    $provider = strtolower(trim($affiliate['service_provider'])) === 'vodafone' ? 'vodafonecash' : strtolower(trim($affiliate['service_provider']));
    echo $provider;
    $mobile_money_data = [
        'type' => 'mobile_money',
        'name' => $affiliate['account_name'],
        'phone' => $phone,
        'provider' => $provider,
        'currency' => 'GHS',
    ];


    var_dump($mobile_money_data);

    $ch = curl_init('https://api.paystack.co/transferrecipient');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mobile_money_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $paystack_secret_key,
        'Content-Type: application/json',
    ]);
    
    $recipient_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $recipient_data = json_decode($recipient_response, true);

    // Debug Response
    echo 'debugging <br/>';

    print_r([
        'http_code' => $http_code,
        'response' => $recipient_response,
        'payload' => $mobile_money_data,
    ]);

    if ($http_code === 200 && $response_data['status']) {
        echo "Recipient created successfully.";
    } else if($http_code !== 200 || !$recipient_data['status']) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create mobile money recipient.', 'data' => $recipient_data]);
        exit;
    }
    
    $recipient_code = $recipient_data['data']['recipient_code'];
    
    // Step 2: Initiate a Transfer
    $transfer_data = [
        'source' => 'balance',
        'amount' => $amount * 100, // Convert amount to kobo
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
    
    if ($http_code === 200 && $transfer_data['status']) {
        // Record successful payout in the database
        $stmt = $mysqli->prepare("
            INSERT INTO payouts (affiliate_id, amount, recipient_code, transaction_id, status)
            VALUES (:affiliate_id, :amount, :recipient_code, :transaction_id, :status)
        ");
        $stmt->execute([
            ':affiliate_id' => $affiliate_id,
            ':amount' => $amount,
            ':recipient_code' => $recipient_code,
            ':transaction_id' => $transfer_data['data']['reference'],
            ':status' => 'success',
        ]);
    
        echo json_encode(['status' => 'success', 'message' => 'Payout processed successfully.']);
    } else {
        // Log failed payout
        $stmt = $mysqli->prepare("
            INSERT INTO payouts (affiliate_id, amount, recipient_code, status)
            VALUES (:affiliate_id, :amount, :recipient_code, :status)
        ");
        $stmt->execute([
            ':affiliate_id' => $affiliate_id,
            ':amount' => $amount,
            ':recipient_code' => $recipient_code,
            ':status' => 'failed',
        ]);
    
        echo json_encode(['status' => 'error', 'message' => 'Failed to process payout.']);
    }
?>    