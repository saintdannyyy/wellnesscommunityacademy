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
    $phone ='0' . $affiliate['phone_number'];
    $provider = strtolower(trim($affiliate['service_provider'])) === 'vodafone' ? 'vodafonecash' : strtolower(trim($affiliate['service_provider']));
    
    
    //from paystack
    $url = "https://api.paystack.co/transferrecipient";

    $mobile_money_data = [
        "type" => "mobile_money",
        "name" => $affiliate['account_name'],
        "phone" => $phone,
        "provider" => $provider,
        "currency" => "GHS",
    ];

    $mobile_money_data = json_encode($mobile_money_data);
    // echo $mobile_money_data;

    //open connection
    $ch = curl_init();
  
    //set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query(json_decode($mobile_money_data, true)));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $paystack_secret_key,
        "Content-Type: application/json"
    ));
  
    //So that curl_exec returns the contents of the cURL; rather than echoing it
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    
    //execute post
    $recipient_response = curl_exec($ch);
    curl_close($ch);
    
    $recipient_data = json_decode($recipient_response, true);
    
    // print_r($recipient_data['data'][0]['recipient_code']);


    if (!isset($recipient_data['status']) || !$recipient_data['status']) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create mobile money recipient.', 'data' => $recipient_data]);
        exit();
    }
    
    $recipient_code = $recipient_data['data'][0]['recipient_code'];
            
    // Step 2: Initiate a Transfer
    $transfer_data = [
        'source' => 'balance',
        'amount' => $amount * 100, // Convert amount to pesewas
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
    // print_r($transfer_data['data']);
    
    if ($http_code === 200 && isset($transfer_data['data']) && $transfer_data['data']) {
        if ($transfer_data['data']['status'] === 'otp') {
            $transfer_code = $transfer_data['data']['transfer_code'];
    
            // Store pending transfer in the database
            $stmt = $mysqli->prepare("
                INSERT INTO payouts (affiliate_id, amount, recipient_code, transaction_id, status)
                VALUES (?, ?, ?, ?, 'pending')
            ");
            $stmt->bind_param('iiss', $affiliate_id, $amount, $recipient_code, $transfer_code);
            $stmt->execute();
            $stmt->close();
    
            echo json_encode(['status' => 'pending', 'message' => 'Transfer pending OTP finalization.', 'transfer_code' => $transfer_code]);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to process payout.', 'data' => $transfer_data]);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error processing payout.', 'data' => $transfer_data]);
        exit();
    }
?>