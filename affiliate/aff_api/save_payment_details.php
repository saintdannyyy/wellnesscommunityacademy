<?php
session_start();
require '../../conn/conn.php';

$response = [
    'status' => 'error',
    'message' => 'An unexpected error occurred.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!isset($_SESSION['affiliate_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    $affiliate_id = $_SESSION['affiliate_id'];
    $bank_name = $data['bankName'] ?? null;
    $account_number = $data['accountNumber'] ?? null;
    $account_holder = $data['accountHolder'] ?? null;

    if ($account_holder && $account_number && $bank_name) {
        $stmt = $mysqli->prepare("UPDATE affiliates SET account_name = ?, account_number = ?, bank_name = ? WHERE id = ?");
        $stmt->bind_param('sisi', $account_holder, $account_number, $bank_name, $affiliate_id);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Payment details saved successfully.';
        } else {
            $response['message'] = 'Failed to save payment details. Please try again.';
            $response['debug'] = [
                'account_name' => $account_holder,
                'account_number' => $account_number,
                'bank_name' => $bank_name,
                'affiliate_id' => $affiliate_id,
                'error' => $stmt->error
            ];
        }
        $stmt->close();
    } else {
        $response['message'] = 'All fields are required.';
        $response['details'] = [
            'account_holder' => $account_holder,
            'account_number' => $account_number,
            'bank_name' => $bank_name,
            'affiliate_id' => $affiliate_id
        ];
    }
}

echo json_encode($response);
