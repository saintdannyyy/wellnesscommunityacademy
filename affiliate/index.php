<?php
session_start();
require_once __DIR__ . '../../config/loadENV.php';
include('../conn/conn.php');
require_once 'aff_api/urlShortener.php';
$apiKey = $_ENV['TINY_KEY']; 

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
$affiliateRef1 = "https://wellnesscommunityacademy.com/affiliate/auth/register.php?rf=" . urlencode($encodedReferral);
$cusRe1 = "https://wellnesscommunityacademy.com?rf=" . urlencode($encodedReferral);

$affiliateRef = shortenUrl($affiliateRef1 , $apiKey);
$cusRef = shortenUrl($cusRe1, $apiKey);

//Inserting referral links into database
$sqlAddLinks = "UPDATE affiliates SET cus_ref = ?, affiliate_ref = ? WHERE id = ?";
$stmt = $mysqli->prepare($sqlAddLinks);
$stmt->bind_param('ssi', $cusRef, $affiliateRef, $affiliateId);
$stmt->execute();
$stmt->close();


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
// var_dump($products);

// $sqlFetchAffiliateEarning = "SELECT * FROM affiliate_earnings where product_id = products['id']; ";
// $fetchAffiliateEarningResult = $mysqli->query($sqlFetchAffiliateEarning);
// if ($fetchAffiliateEarningResult -> num_rows >0){

// }
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
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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
            <h3 class="text-dark">Hello <?php echo $_SESSION['customer_name'] ?></h3>
            <button id="mcusref" class="btn btn-outline-primary m-2 p-2 d-sm-none" onclick="copyCusRef()">Refer A Customer</button>
            <button id="maffref" class="btn btn-outline-primary p-2 d-sm-none" onclick="copyAffiliateRef()">Refer Another Affiliate</button>

                <h5 class="text-dark">Products You Promote</h5>
            </div>

            <!-- Product selection here -->
            <div class=" p-4 d-flex align-items-center mb-3">
                <div class="me-auto">
                    <h6>All Products</h6>
                    <select class="form-select mb-3 selectpicker" data-live-search="true" id="productSelect" style="background-color:#007bff; color:white;">

                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo htmlspecialchars($product['id']); ?>">
                                <?php echo htmlspecialchars($product['title'] ?? $product['course'] ?? $product['program']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn btn-outline-primary m-2 p-2 d-none d-sm-inline-block" onclick="copyCusRef()">Refer A Customer</button>
                <button class="btn btn-outline-primary p-2 d-none d-sm-inline-block" onclick="copyAffiliateRef()">Refer Another Affiliate</button>
            </div>

            <!-- Affiliate Earning Table -->
            <div class="p-4 border-top">
                <h5 class="text-dark">Affiliate Earnings</h5>
                <table id="earningsTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sales</th>
                            <th>Commission</th>
                            <th>Type</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="earningsTableBody">
                        <tr>
                            <td colspan="5" class="text-center">Select a product to view earnings</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script to copy affiliate link to clipboard
        function copyAffiliateRef() {
            const link = "<?php echo $affiliateRef; ?>";
            navigator.clipboard.writeText(link).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Copied',
                    html: `
                        <p>Your link to refer another affiliate has been copied to the clipboard!</p>
                        <div class="input-group">
                            <input type="text" value="${link}" class="form-control" readonly style="background-color: #f0f0f0;">
                            <button class="btn btn-primary" onclick="navigator.clipboard.writeText('${link}')">
                                <i class="bi bi-clipboard"></i> Copy Again
                            </button>
                        </div>
                    `,
                });
            });
        }

        // Script to copy customer link to clipboard
        function copyCusRef() {
            const link = "<?php echo $cusRef; ?>";
            navigator.clipboard.writeText(link).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link Copied',
                    html: `
                        <p>Your link to refer a customer has been copied to the clipboard!</p>
                        <div class="input-group">
                            <input type="text" value="${link}" class="form-control" readonly style="background-color: #f0f0f0;">
                            <button class="btn btn-primary" onclick="navigator.clipboard.writeText('${link}')">
                                <i class="bi bi-clipboard"></i> Copy Again
                            </button>
                        </div>
                    `,
                });
            });
        }

        // Script for fetching affiliate earnings
        document.addEventListener('DOMContentLoaded', function () {
            const productSelect = document.getElementById('productSelect');
            const tableBody = document.getElementById('earningsTableBody');

            productSelect.addEventListener('change', function () {
                const productId = this.value;
                // Clear the table and show a loading message while data is being fetched
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Loading...</td></tr>';

                console.log("Product id sent to api = ", productId);


                if (!productId) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Select a product to view earnings</td></tr>';
                    return;
                }

                fetch(`aff_api/fetch_affiliate_earnings.php?product_id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        tableBody.innerHTML = `<tr><td colspan="5" class="text-center">${data.error}</td></tr>`;
                    } else if (data.length === 0) {
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No earnings found for this product</td></tr>';
                    } else {
                        tableBody.innerHTML = data.map(earning => `
                            <tr>
                                <td>${earning.product_name || 'N/A'}</td>
                                <td>${earning.total || 0}</td>
                                <td>GHS ${earning.amount || 0}</td>
                                <td>${earning.typeof_purchase || 0}</td>
                                <td>${new Date(earning.created_at).toLocaleString('en-US', { day: 'numeric', month: 'long', year: 'numeric', hour: 'numeric', minute: 'numeric', hour12: true }) || 'N/A'}</td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error fetching affiliate earnings:', error);
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">An error occurred while fetching earnings</td></tr>';
                });
            });
        });

        //Script for datatables
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize DataTable with custom options
            const table = $('#earningsTable').DataTable({
                paging: true, // Enable pagination
                searching: true, // Enable search
                ordering: true, // Enable column sorting
                responsive: true, // Make table responsive
                language: {
                    emptyTable: "No data available", // Message for empty table
                    search: "Filter:", // Label for search input
                    lengthMenu: "Show _MENU_ entries", // Label for entries dropdown
                },
            });

            const productSelect = document.getElementById('productSelect');
            const tableBody = document.getElementById('earningsTableBody');

            productSelect.addEventListener('change', function () {
                const productId = this.value;
                table.clear().draw(); // Clear DataTable before loading new data

                if (!productId) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Select a product to view earnings</td></tr>';
                    return;
                }

                fetch(`aff_api/fetch_affiliate_earnings.php?product_id=${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            tableBody.innerHTML = `<tr><td colspan="5" class="text-center">${data.error}</td></tr>`;
                        } else if (data.length === 0) {
                            tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No earnings found for this product</td></tr>';
                        } else {
                            table.rows.add(
                                data.map(earning => [
                                    earning.product_name || 'N/A',
                                    earning.total || 0,
                                    `GHS ${earning.amount || 0}`,
                                    earning.typeof_purchase || 'N/A',
                                    new Date(earning.created_at).toLocaleString('en-US', { 
                                        day: 'numeric', 
                                        month: 'long', 
                                        year: 'numeric', 
                                        hour: 'numeric', 
                                        minute: 'numeric', 
                                        hour12: true 
                                    }) || 'N/A'
                                ])
                            ).draw(); // Add new rows and redraw the table
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching affiliate earnings:', error);
                        tableBody.innerHTML = '<tr><td colspan="5" class="text-center">An error occurred while fetching earnings</td></tr>';
                    });
            });
        });
    </script>
</body>

</html>