<?php
session_start();
require_once __DIR__ . '../../../config/loadENV.php';
include('../../conn/conn.php');

if (!isset($_SESSION['affiliate_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$affiliateId = $_SESSION['affiliate_id'];
$productId = $_GET['product_id'] ?? null;
// echo $productId;
// echo "<br>";
// echo $affiliateId;

if (!$productId) {
    echo json_encode(['error' => 'No product ID provided']);
    exit();
}

$sql = "SELECT * FROM affiliate_earnings WHERE product_id = ? AND affiliate_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $productId, $affiliateId);
$stmt->execute();
$result = $stmt->get_result();

// $sqlCount = "SELECT COUNT(*) as total FROM affiliate_earnings WHERE product_id = ? AND affiliate_id = ?";
// $stmtCount = $mysqli->prepare($sqlCount);
// $stmtCount->bind_param("ii", $productId, $affiliateId);
// $stmtCount->execute();
// $resultCount = $stmtCount->get_result();
// $rowCount = $resultCount->fetch_assoc();
// $total = $rowCount['total'];


$earnings = [];
while ($row = $result->fetch_assoc()) {
    $earnings[] = $row;
    // $earnings['total'] = $total;
}

echo json_encode($earnings);
?>