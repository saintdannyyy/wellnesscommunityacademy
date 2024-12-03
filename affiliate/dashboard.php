<?php
session_start();

// Check if the affiliate is logged in
if (!isset($_SESSION['affiliate_id'])) {
    header('Location: login.php');
    exit();
}

// Include database connection
include('../conn/conn.php');

// Fetch affiliate information
$affiliateId = $_SESSION['affiliate_id'];
$query = "SELECT * FROM affiliates WHERE id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $affiliateId);
$stmt->execute();
$result = $stmt->get_result();
$affiliate = $result->fetch_assoc();

// Generate affiliate link
$affiliateLink = "https://wellnesscommunityacademy.com/register.php?referral=" . urlencode($affiliate['affiliate_id']);

// Fetch referred affiliates
$queryReferred = "SELECT name, email, created_at FROM affiliates 
                  WHERE id IN (SELECT affiliate_id FROM affiliate_referrals WHERE referred_by = ?)";
$stmtReferred = $mysqli->prepare($queryReferred);
$stmtReferred->bind_param('i', $affiliateId);
$stmtReferred->execute();
$resultReferred = $stmtReferred->get_result();
$referredAffiliates = $resultReferred->fetch_all(MYSQLI_ASSOC);

// Calculate commissions
// $queryCommissions = "SELECT 
//     SUM(CASE WHEN level = 'direct' THEN amount ELSE 0 END) AS direct_commissions,
//     SUM(CASE WHEN level = 'indirect' THEN amount ELSE 0 END) AS indirect_commissions
//     FROM commissions WHERE affiliate_id = ?";
// $stmtCommissions = $mysqli->prepare($queryCommissions);
// $stmtCommissions->bind_param('i', $affiliateId);
// $stmtCommissions->execute();
// $resultCommissions = $stmtCommissions->get_result();
// $commissions = $resultCommissions->fetch_assoc();

// // Total commissions
// $totalCommissions = $commissions['direct_commissions'] + $commissions['indirect_commissions'];

// Log the activity of viewing the dashboard
log_activity($affiliateId, 'Viewed Dashboard', $mysqli);

function log_activity($affiliateId, $action, $mysqli) {
    $query = "INSERT INTO activity_log (affiliate_id, action, timestamp) VALUES (?, ?, NOW())";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('is', $affiliateId, $action);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard">
        <h1>Welcome, <?php echo $affiliate['name']; ?></h1>
        <p><strong>Email:</strong> <?php echo $affiliate['email']; ?></p>
        <p><strong>Your Affiliate Link:</strong></p>
        <input type="text" id="affiliateLink" value="<?php echo $affiliateLink; ?>" readonly>
        <button onclick="copyAffiliateLink()">Copy Link</button>

        <h2>Your Referrals</h2>
        <?php if (!empty($referredAffiliates)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($referredAffiliates as $referral) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($referral['name']); ?></td>
                            <td><?php echo htmlspecialchars($referral['email']); ?></td>
                            <td><?php echo htmlspecialchars($referral['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have no referrals yet.</p>
        <?php endif; ?>

        <h2>Your Commissions</h2>
        <p><strong>Direct Commissions:</strong> $<?php echo number_format($commissions['direct_commissions'], 2); ?></p>
        <p><strong>Indirect Commissions:</strong> $<?php echo number_format($commissions['indirect_commissions'], 2); ?></p>
        <p><strong>Total Commissions:</strong> $<?php echo number_format($totalCommissions, 2); ?></p>
    </div>

    <script>
        function copyAffiliateLink() {
            const link = document.getElementById('affiliateLink');
            link.select();
            link.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            Swal.fire({
                icon: 'success',
                title: 'Link Copied',
                text: 'Your affiliate link has been copied to the clipboard!'
            });
        }
    </script>
</body>
</html>