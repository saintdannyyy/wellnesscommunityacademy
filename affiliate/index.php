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

$affiliateId = $_SESSION['affiliate_id'];

// Generate affiliate link
function encodeReferralId($affiliateId)
{
    $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
    return base64_encode(openssl_encrypt($affiliateId, 'aes-256-cbc', $key, 0, substr($key, 0, 16)));
}
$encodedReferral = encodeReferralId($affiliateId);
$affiliateLink = "https://wellnesscommunityacademy.com/acc/auth/register.php?rf=" . urlencode($encodedReferral);

// Fetching all books
$books = [];
$sqlFetchBooks = "SELECT id, title FROM books";
$fetchBooksResult = $mysqli->query($sqlFetchBooks);
if ($fetchBooksResult->num_rows > 0) {
    while ($row = $fetchBooksResult->fetch_assoc()) {
        $books[] = ['id' => $row['id'], 'title' => $row['title']];
    }
}

// Fetching all courses
$courses = [];
$sqlFetchCourses = "SELECT id, course FROM courses";
$fetchCoursesResult = $mysqli->query($sqlFetchCourses);
if ($fetchCoursesResult->num_rows > 0) {
    while ($row = $fetchCoursesResult->fetch_assoc()) {
        $courses[] = ['id' => $row['id'], 'course' => $row['course']];
    }
}

// Fetching all programs
$programs = [];
$sqlFetchPrograms = "SELECT id, program FROM programs";
$fetchProgramsResult = $mysqli->query($sqlFetchPrograms);
if ($fetchProgramsResult->num_rows > 0) {
    while ($row = $fetchProgramsResult->fetch_assoc()) {
        $programs[] = ['id' => $row['id'], 'program' => $row['program']];
    }
}

// Combine all products into one array
$products = array_merge($books, $courses, $programs);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="d-flex">
        <!-- Include Sidebar -->
        <div>
            <?php include 'sidebar/sidebar.html'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <div class="p-4 border-bottom">
                <h5 class="text-dark">Products You Promote</h5>
            </div>

            <div class="d-flex align-items-center mb-3">
                <div class="me-auto">
                    <h6>All Products</h6>
                    <select class="form-select mb-3 selectpicker" data-live-search="true" id="productSelect">
                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                <?php echo htmlspecialchars($product['title'] ?? $product['course'] ?? $product['program']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-outline-primary" onclick="copyAffiliateLink()">My Link</button>
            </div>


            <div class="d-flex justify-content-between p-3 bg-light border rounded">
                <div>
                    <p class="mb-1">Sales + Rebill</p>
                    <h6>0 + 0</h6>
                </div>
                <div>
                    <p class="mb-1">Commissions</p>
                    <h6>USD 0</h6>
                </div>
                <div>
                    <p class="mb-1">Affiliate Commission</p>
                    <h6>15%</h6>
                </div>
                <div>
                    <p class="mb-1">JV Commission</p>
                    <h6>2%</h6>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyAffiliateLink() {
            const link = "<?php echo $affiliateLink; ?>";
            navigator.clipboard.writeText(link).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Copied',
                    text: 'Your affiliate link has been copied to the clipboard!'
                });
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#productSelect').selectpicker({
                liveSearch: true,
                noneSelectedText: 'Select a product',
            });
        });
    </script>
</body>

</html>