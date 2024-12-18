<?php
session_start();
require('../../conn/conn.php');

// Ensure the user is authenticated and has an affiliate ID
if (!isset($_SESSION['affiliate_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit;
}

$affiliate_id = $_SESSION['affiliate_id'];

try {
    // Query to check payment details for the affiliate
    $stmt = $mysqli->prepare("SELECT service_provider, phone_number, account_name FROM affiliates WHERE id = ?");
    $stmt->bind_param("i", $affiliate_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $details = $result->fetch_assoc();
        echo json_encode([
            'status' => 'exists',
            'data' => [
                'service_provider' => $details['service_provider'],
                'phone_number' => $details['phone_number'],
                'account_holder' => $details['account_name']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No payment details found.']);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>