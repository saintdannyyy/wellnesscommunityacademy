<?php
include '../db/connection.php';

$sql = "SELECT a.id, a.affiliate_id, c.name AS customer_name, 
               r.affiliate_id AS referrer_affiliate_id, a.status, a.created_at
        FROM affiliates a
        LEFT JOIN customers c ON a.customer_id = c.id
        LEFT JOIN affiliates r ON a.referrer_id = r.id";
$result = $mysqli->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode(['data' => $data]);
?>