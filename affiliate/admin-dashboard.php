<?php
session_start();
require_once __DIR__ . '../../config/loadENV.php';
include('../conn/conn.php');

// Environment settings
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

if (!isset($_SESSION['affiliate_id'])) {
    header('Location: auth/login.php');
    exit();
}

$affiliateId = $_SESSION['affiliate_index'];

// Generate affiliate link
function encodeReferralId($affiliateId) {
    $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
    return base64_encode(openssl_encrypt($affiliateId, 'aes-256-cbc', $key, 0, substr($key, 0, 16)));
}
$encodedReferral = encodeReferralId($affiliateId);
$affiliateLink = "https://wellnesscommunityacademy.com/acc/auth/register.php?rf=" . urlencode($encodedReferral);

// Fetch referred affiliates (direct)
$queryDirectAffiliates = "SELECT COUNT(*) AS direct_count FROM affiliates WHERE referrer_id = ?";
$stmtDirectAffiliates = $mysqli->prepare($queryDirectAffiliates);
$stmtDirectAffiliates->bind_param('i', $affiliateId);
$stmtDirectAffiliates->execute();
$resultDirectAffiliates = $stmtDirectAffiliates->get_result()->fetch_assoc();
$directAffiliateCount = $resultDirectAffiliates['direct_count'];
$stmtDirectAffiliates->close();

// Fetch referred affiliates (indirect)
$queryIndirectAffiliates = "
    SELECT COUNT(*) AS indirect_count 
    FROM affiliates a1 
    JOIN affiliates a2 ON a1.referrer_id = a2.id 
    WHERE a2.referrer_id = ?";
$stmtIndirectAffiliates = $mysqli->prepare($queryIndirectAffiliates);
$stmtIndirectAffiliates->bind_param('i', $affiliateId);
$stmtIndirectAffiliates->execute();
$resultIndirectAffiliates = $stmtIndirectAffiliates->get_result()->fetch_assoc();
$indirectAffiliateCount = $resultIndirectAffiliates['indirect_count'];
$stmtIndirectAffiliates->close();

// Fetch detailed commissions breakdown
$queryCommissionsDetail = "
    SELECT 
        ae.typeof_purchase AS level,
        ae.amount,
        ae.product,
        ae.created_at,
        c.name AS customer_name,
        c.email AS customer_email,
        a1.customer_id AS referring_affiliate_customer_id,
        (SELECT name FROM customers WHERE id = a1.customer_id) AS referring_affiliate_name
    FROM affiliate_earnings ae
    LEFT JOIN affiliates a1 ON ae.affiliate_id = a1.id
    LEFT JOIN customers c ON a1.customer_id = c.id
    WHERE ae.id = ?";
$stmtCommissionsDetail = $mysqli->prepare($queryCommissionsDetail);
echo "checking affiliate id: $affiliateId";
$stmtCommissionsDetail->bind_param('i', $affiliateId);
$stmtCommissionsDetail->execute();
$resultCommissionsDetail = $stmtCommissionsDetail->get_result();

$commissionDetails = [];
while ($row = $resultCommissionsDetail->fetch_assoc()) {
    $commissionDetails[] = $row;
}
$stmtCommissionsDetail->close();
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
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></h1>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['customer_email']); ?></p>
        <p><strong>Your Affiliate Link:</strong></p>
        <input type="text" id="affiliateLink" value="<?php echo htmlspecialchars($affiliateLink); ?>" readonly>
        <button onclick="copyAffiliateLink()">Copy Link</button>

        <h2>Affiliate Referrals</h2>
        <p><strong>Directly Referred Affiliates:</strong> <?php echo $directAffiliateCount; ?></p>
        <p><strong>Indirectly Referred Affiliates:</strong> <?php echo $indirectAffiliateCount; ?></p>

        <h2>Your Commissions Breakdown</h2>
        <?php if (!empty($commissionDetails)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Product</th>
                        <th>Amount</th>
                        <th>Customer Name</th>
                        <th>Customer Email</th>
                        <th>Referring Affiliate</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commissionDetails as $commission) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($commission['level']); ?></td>
                            <td><?php echo htmlspecialchars($commission['product']); ?></td>
                            <td>$<?php echo number_format($commission['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars($commission['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($commission['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($commission['referring_affiliate_name']); ?></td>
                            <td><?php echo htmlspecialchars($commission['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No commissions yet.</p>
        <?php endif; ?>
    </div>

    <script>
        function copyAffiliateLink() {
            const link = document.getElementById('affiliateLink');
            link.select();
            navigator.clipboard.writeText(link.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Copied',
                    text: 'Your affiliate link has been copied to the clipboard!'
                });
            });
        }
    </script>
</body>
</html>
