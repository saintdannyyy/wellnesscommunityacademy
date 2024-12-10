<?php
include '../db/connection.php';

function getReferralTree($affiliate_id, $mysqli) {
    $referrals = [];
    $sql = "SELECT a.id, a.affiliate_id, c.name AS customer_name
            FROM affiliates a
            LEFT JOIN customers c ON a.customer_id = c.id
            WHERE a.referrer_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $affiliate_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $row['referrals'] = getReferralTree($row['id'], $mysqli);
        $referrals[] = $row;
    }

    return $referrals;
}

$affiliate_id = $_GET['affiliate_id'];
$tree = getReferralTree($affiliate_id, $mysqli);

echo json_encode($tree);
?>