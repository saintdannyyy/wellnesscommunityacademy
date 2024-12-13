<?php
require('../config/loadENV.php');
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);  // Show errors in development environment
    error_reporting(E_ALL);       // Report all errors
} else {
    ini_set('display_errors', 0);  // Hide errors in production environment
}

$open_exchange_api_key = $_ENV['open_exchange_api_key'] ?? null;

if (!$open_exchange_api_key) {
    echo json_encode(['success' => false, 'message' => 'API key for Open Exchange Rates is missing.']);
    exit;
}

header('Content-Type: application/json');

// Start session and check login
session_start();
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['success' => false, 'message' => 'You are not logged in. Please log in.']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Validate book ID
$bookId = intval($_POST['book_id'] ?? 0);
if ($bookId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid book ID.']);
    exit;
}

require_once('../conn/conn.php');
if (!$mysqli) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}

// Fetch book details
$sql = "SELECT title, price, path FROM books WHERE id = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL preparation error: ' . $mysqli->error]);
    exit;
}

$stmt->bind_param('i', $bookId);
$stmt->execute();
$stmt->bind_result($title, $price, $path);
$stmt->fetch();
$stmt->close();
$mysqli->close();

if (!$title || !$price) {
    echo json_encode(['success' => false, 'message' => 'Book not found.']);
    exit;
}

// Fetch exchange rates
$apiKey = $open_exchange_api_key;
$url = "https://openexchangerates.org/api/latest.json?app_id=$apiKey&symbols=GHS,NGN&base=USD";
$currencyData = @file_get_contents($url);
if ($currencyData === false) {
    echo json_encode(['success' => false, 'message' => 'Currency conversion API unavailable.']);
    exit;
}

$exchangeData = json_decode($currencyData, true);
if (!$exchangeData || !isset($exchangeData['rates']['GHS'], $exchangeData['rates']['NGN'])) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch exchange rate data.']);
    exit;
}

$usdToGhsRate = $exchangeData['rates']['GHS'];
$usdToNgnRate = $exchangeData['rates']['NGN'];
$priceInGhs = number_format($price * $usdToGhsRate, 2);
$priceInNgn = number_format($price * $usdToNgnRate, 2);

// Return JSON response
echo json_encode([
    'success' => true,
    'data' => [
        'title' => $title,
        'price_usd' => $price,
        'price_ghs' => $priceInGhs,
        'price_ngn' => $priceInNgn,
        'rate_usd_to_ghs' => $usdToGhsRate,
        'rate_usd_to_ngn' => $usdToNgnRate,
        'path' => $path
    ]
]);
?>