<?php
session_start();
require '../../conn/conn.php';

$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $affiliate_id = $_POST['affiliate_id'];
    $service_provider = $_POST['service_provider'];
    $phone_number = $_POST['phone_number'];
    $accountHolder = $_POST['accountHolder'];
    
    if ($accountHolder && $phone_number && $service_provider) {
        $stmt = $mysqli->prepare("UPDATE affiliates SET account_name = ?, phone_number = ?, service_provider = ? WHERE id = ?");
        $stmt->bind_param('sisi', $accountHolder, $phone_number, $service_provider, $affiliate_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Payment details saved successfully.';
        } else {
            $response['message'] = 'Failed to save payment details. Please try again.';
            $response['debug'] = [
                'account_name' => $accountHolder,
                'phone_number' => $phone_number,
                'service_provider' => $service_provider,
                'affiliate_id' => $affiliate_id,
                'error' => $stmt->error
            ];
        }
        $stmt->close();
    } else {
        $response['message'] = 'All fields are required.';
        $response['details'] = [
            'accountHolder' => $accountHolder,
            'phone_number' => $phone_number,
            'service_provider' => $service_provider,
            'affiliate_id' => $affiliate_id
        ];
    }
}

echo json_encode($response);
