<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseID = $_POST['courseID'] ?? null;

    // Validate course ID
    if (!is_numeric($courseID)) {
        echo json_encode(['success' => false, 'message' => 'Invalid course ID']);
        exit;
    }

    // Database connection
    require_once('../conn/conn.php');
    if ($mysqli->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
        exit;
    }

    // Fetch course details
    $sql = "SELECT course, price FROM courses WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL error: ' . $mysqli->error]);
        exit;
    }

    $stmt->bind_param('i', $courseID);
    $stmt->execute();
    $stmt->bind_result($course, $price);
    $stmt->fetch();
    $stmt->close();
    $mysqli->close();

    // Validate fetched data
    if (empty($course) || empty($price)) {
        echo json_encode(['success' => false, 'message' => 'Course not found']);
        exit;
    }

    // Fetch currency rate
    $open_exchange_api_key = '0d6f5687149b407fb1c561d00ecdb908';
    $open_exchange_url = "https://openexchangerates.org/api/latest.json?app_id={$open_exchange_api_key}&symbols=GHS&base=USD";
    
    $currency_data = @file_get_contents($open_exchange_url);
    if ($currency_data === false) {
        echo json_encode(['success' => false, 'message' => 'Currency conversion API unavailable']);
        exit;
    }
    
    $exchange_data = json_decode($currency_data, true);
    if (!isset($exchange_data['rates']['GHS'])) {
        echo json_encode(['success' => false, 'message' => 'Failed to fetch exchange rate']);
        exit;
    }
    
    $usd_to_ghs_rate = $exchange_data['rates']['GHS'];
    $price_in_ghs = $price * $usd_to_ghs_rate;


    // Success response
    echo json_encode([
        'success' => true,
        'courseID' => $courseID,
        'course' => $course,
        'price_usd' => $price,
        'price' => round($price_in_ghs, 2),
        'rate' => $usd_to_ghs_rate
    ]);
}
?>