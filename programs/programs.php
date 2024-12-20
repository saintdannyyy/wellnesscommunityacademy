<?php
require('../config/loadENV.php');
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);  // Show errors in development environment
    error_reporting(E_ALL);       // Report all errors
} else {
    ini_set('display_errors', 0);  // Hide errors in production environment
}

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pID = $_POST['instanceNumber'] ?? null;

    require_once('../conn/conn.php');
    if (!$mysqli) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }


    if (!is_numeric($pID)) {
        echo json_encode(['success' => false, 'message' => 'Invalid book ID']);
        exit;
    }

    $sql = "SELECT program, price FROM programs WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL preparation error: ' . $mysqli->error]);
        exit;
    }

    $stmt->bind_param('i', $pID);
    $stmt->execute();
    $stmt->bind_result($prog, $price);
    $stmt->fetch();

    // Close the statement and connection
    $stmt->close();
    $mysqli->close();

        
    // Check if title and price were fetched
    if ($prog && $price) {
        // Open Exchange Rates API setup
        $open_exchange_api_key = $_ENV['open_exchange_api_key'] ?? null;
        $open_exchange_url = "https://openexchangerates.org/api/latest.json?app_id={$open_exchange_api_key}&symbols=GHS&base=USD";
    
        // Fetch the exchange rate data
        $currency_data = file_get_contents($open_exchange_url);
    
        if ($currency_data === false) {
            // If API call fails to fetch data
            echo json_encode([
                'success' => false,
                'message' => 'Currency conversion API unavailable'
            ]);
            exit;
        }
    
        // Decode the JSON response
        $exchange_data = json_decode($currency_data, true);
    
        // Check if the 'rates' and 'GHS' keys exist in the response
        if (isset($exchange_data['rates']['GHS'])) {
            $usd_to_ghs_rate = $exchange_data['rates']['GHS'];
            $price_in_ghs = $price * $usd_to_ghs_rate;
    
            // Respond with the converted price and rate
            echo json_encode([
                'success' => true,
                'prog' => $prog,
                'id' => $pID,
                'price_usd' => $price,
                'price' => round($price_in_ghs, 2),
                'rate' => $usd_to_ghs_rate
            ]);
        } else {
            // If the response does not contain expected data
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch exchange rate'
            ]);
        }
    } else {
        // If title or price is missing
        echo json_encode([
            'success' => false,
            'message' => 'Invalid program details'
        ]);
    }
}
?>