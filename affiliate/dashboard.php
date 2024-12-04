<?php
session_start();
// Include the script to load environment variables
require_once __DIR__ . '../../config/loadENV.php';
if ($_ENV['APP_ENV'] === 'dev') { 
    ini_set('display_errors', 1);  // Show errors in development environment
    error_reporting(E_ALL);       // Report all errors
} else {
    ini_set('display_errors', 0);  // Hide errors in production environment
}


// Check if the affiliate is logged in
if (!isset($_SESSION['affiliate_id'])) {
    header('Location: <auth/login.php');
    exit();
}

// Include database connection
include('../conn/conn.php');

// Fetch affiliate information
$affiliateId = $_SESSION['affiliate_id'];

// Generate affiliate link
function encodeReferralId($affiliateId) {
    $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
    return base64_encode(openssl_encrypt($affiliateId, 'aes-256-cbc', $key, 0, substr($key, 0, 16)));
}
$encodedReferral = encodeReferralId($affiliateId);
$affiliateLink = "https://wellnesscommunityacademy.com/acc/auth/register.php?rf=" . urlencode($encodedReferral);

// Query to fetch referred users
$queryReferred = "SELECT name, email, affiliate, created_at 
                  FROM customers 
                  WHERE affiliate_referrer_id = ?";
$stmtReferred = $mysqli->prepare($queryReferred);
$stmtReferred->bind_param('i', $_SESSION['affiliate_index']);
$stmtReferred->execute();
$resultReferred = $stmtReferred->get_result();

// Arrays to store referred affiliates and customers
$referredAffiliates = [];
$referredCustomers = [];

// Classify the results
while ($row = $resultReferred->fetch_assoc()) {
    if ((int)$row['affiliate'] === 1) {
        $referredAffiliates[] = $row; // Add to affiliates array
    } else {
        $referredCustomers[] = $row; // Add to customers array
    }
}

// Close the prepared statement
$stmtReferred->close();


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
// log_activity($affiliateId, 'Viewed Dashboard', $mysqli);

// function log_activity($affiliateId, $action, $mysqli) {
//     $query = "INSERT INTO activity_log (affiliate_id, action, timestamp) VALUES (?, ?, NOW())";
//     $stmt = $mysqli->prepare($query);
//     $stmt->bind_param('is', $affiliateId, $action);
//     $stmt->execute();
// }
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
        <h1>Welcome, <?php echo $_SESSION['customer_name']; ?></h1>
        <p><strong>Email:</strong> <?php echo $_SESSION['customer_email']; ?></p>
        <p><strong>Your Affiliate Link:</strong></p>
        <input type="text" id="affiliateLink" value="<?php echo $affiliateLink; ?>" readonly>
        <button onclick="copyAffiliateLink()">Copy Link</button>

        <h2>Affiliates Referred By You</h2>
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
                    <?php
                         foreach ($referredAffiliates as $referral) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($referral['name']); ?></td>
                            <td><?php echo htmlspecialchars($referral['email']); ?></td>
                            <td><?php echo htmlspecialchars($referral['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have not referred any affiliate yet.</p>
        <?php endif; ?>

        <!-- referred customers -->
        <h2>Customers Referred By You</h2>
        <?php if (!empty($referredCustomers)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registration Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                         foreach ($referredCustomers as $referral) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($referral['name']); ?></td>
                            <td><?php echo htmlspecialchars($referral['email']); ?></td>
                            <td><?php echo htmlspecialchars($referral['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>You have not referred any customer yet.</p>
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