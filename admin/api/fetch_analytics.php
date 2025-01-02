<?php
include '../db/connection.php';

// Fetch total number of affiliates
$totalAffiliatesResult = $mysqli->query("SELECT COUNT(*) AS total FROM affiliates");
$totalAffiliatesRow = $totalAffiliatesResult->fetch_assoc();
$totalAffiliates = $totalAffiliatesRow['total'];

// Fetch total sales
$totalSalesAmountResult = $mysqli->query("
   SELECT 
    (SELECT SUM(amount) FROM transactions) AS total_transactions,
    (SELECT SUM(amount) FROM sold_courses) AS total_sold_courses,
    (SELECT SUM(amount) FROM sold_programs) AS total_sold_programs;
");
$totalSalesAmountRow = $totalSalesAmountResult->fetch_assoc();
$totalTransactions = $totalSalesAmountRow['total_transactions'];
$totalSoldCourses = $totalSalesAmountRow['total_sold_courses'];
$totalSoldPrograms = $totalSalesAmountRow['total_sold_programs'];
$totalSalesAmount = $totalTransactions + $totalSoldCourses + $totalSoldPrograms;

// Fetch top affiliates
$topAffiliatesResult = $mysqli->query("SELECT c.name, SUM(s.amount) AS sales
                                       FROM affiliates a
                                       JOIN customers c ON a.customer_id = c.id
                                       JOIN affiliate_earnings s ON a.id = s.affiliate_id
                                       GROUP BY a.id
                                       ORDER BY sales DESC
                                       LIMIT 5");
$topAffiliates = [];
while ($row = $topAffiliatesResult->fetch_assoc()) {
    $topAffiliates[] = $row;
}

// Fetch sales by month
$salesByMonthResult = $mysqli->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(amount) AS sales
                                      FROM affiliate_earnings
                                      GROUP BY month
                                      ORDER BY month");
$salesByMonth = ['labels' => [], 'data' => []];
while ($row = $salesByMonthResult->fetch_assoc()) {
    $salesByMonth['labels'][] = $row['month'];
    $salesByMonth['data'][] = $row['sales'];
}

// Fetch affiliate growth by month
$affiliateGrowthResult = $mysqli->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS new_affiliates
                                         FROM affiliates
                                         GROUP BY month
                                         ORDER BY month");
$affiliateGrowth = ['labels' => [], 'data' => []];
while ($row = $affiliateGrowthResult->fetch_assoc()) {
    $affiliateGrowth['labels'][] = $row['month'];
    $affiliateGrowth['data'][] = $row['new_affiliates'];
}

// Return JSON response
echo json_encode([
    'totalAffiliates' => $totalAffiliates,
    'totalSales' => $totalSalesAmount,
    'topAffiliates' => $topAffiliates,
    'salesByMonth' => $salesByMonth,
    'affiliateGrowth' => $affiliateGrowth
]);
?>