<?php
// // Set Paystack secret key and Kartra details
// $paystack_secret_key = 'sk_test_99a10e136ed567bbd74fb6569bfe8b4b5f35d5f3';
// $kartra_api_url = 'https://app.kartra.com/api/transactions';
// $kartra_api_key = 'rBeYHyzl';

// // Retrieve the request body
// $request_body = file_get_contents('php://input');

// // Verify the webhook signature
// $paystack_signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';
// $generated_hash = hash_hmac('sha512', $request_body, $paystack_secret_key);

// if ($paystack_signature !== $generated_hash) {
//     // Signature mismatch
//     http_response_code(401);
//     exit('Unauthorized');
// }

// // Decode the request body
// $event = json_decode($request_body, true);

// if ($event['event'] === 'charge.success') {
//     // Extract relevant payment details
//     $email = $event['data']['customer']['email'];
//     $amount = $event['data']['amount'] / 100; // Convert from pesewas to GHS
//     $reference = $event['data']['reference'];

//     // Log the successful payment for debugging
//     file_put_contents('webhook_logs.txt', "Payment successful: $reference - $amount GHS\n", FILE_APPEND);

//     // Send the payment details to Kartra
//     $kartra_data = [
//         'api_key' => $kartra_api_key,
//         'email' => $email,
//         'amount' => $amount,
//         'reference' => $reference
//     ];

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $kartra_api_url);
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($kartra_data));
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//     $kartra_response = curl_exec($ch);

//     if ($kartra_response === false) {
//         // Log Kartra API errors
//         file_put_contents('webhook_logs.txt', "Kartra Error: " . curl_error($ch) . "\n", FILE_APPEND);
//     } else {
//         // Log Kartra API response
//         file_put_contents('webhook_logs.txt', "Kartra Response: $kartra_response\n", FILE_APPEND);
//     }

//     curl_close($ch);

//     // Respond to Paystack
//     http_response_code(200);
//     echo 'Webhook handled successfully';
// } else {
//     // Log unhandled events for debugging
//     file_put_contents('webhook_logs.txt', "Unhandled event: " . $event['event'] . "\n", FILE_APPEND);

//     http_response_code(200); // Always respond to Paystack
//     echo 'Event ignored';
// }
?>
