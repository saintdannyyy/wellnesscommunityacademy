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
    $stmt = $mysqli->prepare("SELECT account_name, account_number, bank_name FROM affiliates WHERE id = ?");
    if (!$stmt) {
        $response['message'] = 'Database error: Unable to prepare statement.';
        echo json_encode($response);
        exit();
    }

    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $affiliate = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$affiliate || !$affiliate['account_name'] || !$affiliate['account_number'] || !$affiliate['bank_name']) {
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

    // Paystack API integration
    $paystack_secret_key = $_ENV['PAYSTACK_SECRET_KEY_TEST'];
    $bank_code = getBankCode($affiliate['bank_name']); // Replace with the correct mapping function
    function getBankCode($bankName) {
        // Array of Ghanaian bank names and their corresponding codes
        $bankCodes = [
            'Absa Bank Ghana' => '001',
            'Access Bank Ghana' => '002',
            'Agricultural Development Bank' => '003',
            'Bank of Africa Ghana' => '004',
            'CalBank' => '005',
            'Consolidated Bank Ghana' => '006',
            'Ecobank Ghana' => '007',
            'Fidelity Bank Ghana' => '008',
            'First Atlantic Bank' => '009',
            'First National Bank Ghana' => '010',
            'GCB Bank' => '011',
            'Guaranty Trust Bank Ghana' => '012',
            'National Investment Bank' => '013',
            'OmniBSIC Bank Ghana' => '014',
            'Republic Bank Ghana' => '015',
            'Societe Generale Ghana' => '016',
            'Stanbic Bank Ghana' => '017',
            'Standard Chartered Bank Ghana' => '018',
            'United Bank for Africa Ghana' => '019',
            'Zenith Bank Ghana' => '020',
        ];
    
        // Standardize input for case-insensitive comparison
        $standardizedBankName = strtolower(trim($bankName));
    
        foreach ($bankCodes as $name => $code) {
            if (strtolower($name) === $standardizedBankName) {
                return $code;
            }
        }
    
        // Return null if no match is found
        return null;
    }
    

    if (!$bank_code) {
        $response['message'] = 'Invalid bank name or bank code missing.';
        $response['debug'] = $affiliate['bank_name'];
        echo json_encode($response);
        exit();
    }

    // Step 1: Create a Transfer Recipient
    $recipient_data = [
        'type' => 'nuban',
        'name' => $affiliate['account_name'],
        'account_number' => $affiliate['account_number'],
        'bank_code' => $bank_code,
        'currency' => 'GHS', // Adjust as needed
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
    var_dump($recipient_data);

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
            $stmt = $mysqli->prepare("UPDATE affiliate_earnings SET status = 'paid' WHERE affiliate_id = ? AND status = 'pending'");
            $stmt->bind_param('i', $affiliate_id);
            $stmt->execute();
            $stmt->close();

            $response['status'] = 'success';
            $response['message'] = 'Payout request processed successfully.';
        } else {
            $response['message'] = 'Failed to process payout. Paystack Error: ' . ($transfer_data['message'] ?? 'Unknown error.');
        }
    } else {
        $response['message'] = 'Failed to create a transfer recipient. Paystack Error: ' . ($recipient_data['message'] ?? 'Unknown error.');
    }
// }

echo json_encode($response);
?>