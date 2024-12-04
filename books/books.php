<?php
// Check if user is logged in
session_start();
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'You are not logged in.' . "\n" . 'Please log in']);
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookId = intval($_POST['book_id'] ?? 0);

    require_once('../conn/conn.php');
    if (!$mysqli) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $sql = "SELECT title, price FROM Books WHERE id = ?";
    $stmt = $mysqli->prepare($sql);

    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL preparation error: ' . $mysqli->error]);
        exit;
    }

    $stmt->bind_param('i', $bookId);
    $stmt->execute();
    $stmt->bind_result($title, $price);
    $stmt->fetch();
    $stmt->close();
    $mysqli->close();

    if ($title && $price) {
        $open_exchange_api_key = '0d6f5687149b407fb1c561d00ecdb908';
        $open_exchange_url = "https://openexchangerates.org/api/latest.json?app_id={$open_exchange_api_key}&symbols=GHS,NGN&base=USD";

        $currency_data = file_get_contents($open_exchange_url);
        if ($currency_data === false) {
            echo json_encode(['success' => false, 'message' => 'Currency conversion API unavailable']);
            exit;
        }

        $exchange_data = json_decode($currency_data, true);
        if (!$exchange_data || !isset($exchange_data['rates']['GHS'], $exchange_data['rates']['NGN'])) {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch exchange rate data']);
            exit;
        }

        $usd_to_ghs_rate = $exchange_data['rates']['GHS'];
        $usd_to_ngn_rate = $exchange_data['rates']['NGN'];

        $price_in_ghs = number_format($price * $usd_to_ghs_rate, 2);
        $price_in_ngn = number_format($price * $usd_to_ngn_rate, 2);

        echo json_encode([
            'success' => true,
            'data' => [
                'title' => $title,
                'price_usd' => $price,
                'price_ghs' => $price_in_ghs,
                'price_ngn' => $price_in_ngn,
                'rate_usd_to_ghs' => $usd_to_ghs_rate,
                'rate_usd_to_ngn' => $usd_to_ngn_rate
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid book details']);
    }
}
?>