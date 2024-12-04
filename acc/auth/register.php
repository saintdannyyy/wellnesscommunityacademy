<?php
// Enable error reporting for debugging in development environment
require_once __DIR__ . '../../../config/loadENV.php';

if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

// Start the session and check if the customer is logged in
session_start();
if (isset($_SESSION['customer_id'])) {
    header('Location: ../../');
    exit();
}

// Include database connection
include('../../conn/conn.php');

// Include SweetAlert library globally
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Decode referral code if it is encoded
function decodeReferralId($referralCode)
{
    $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
    return openssl_decrypt(base64_decode($referralCode), 'aes-256-cbc', $key, 0, substr($key, 0, 16));
}

// Extract and decode referral code from the URL if available
$referralCodeFromUrl = isset($_GET['rf']) ? htmlspecialchars(trim($_GET['rf'])) : '';
if (!empty($referralCodeFromUrl)) {
    $referralCodeFromUrl = decodeReferralId($referralCodeFromUrl);
}

// Process registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone']));
    $password = htmlspecialchars($_POST['password']);
    $isAffiliate = isset($_POST['is_affiliate']) && $_POST['is_affiliate'] === '1' ? 1 : 0;
    $referralCode = !empty($_POST['referral_code']) ? htmlspecialchars(trim($_POST['referral_code'])) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Email',
                text: 'Please enter a valid email address.'
            });
    });
        </script>";
        exit();
    }

    // Validate phone number (10–15 digits, optional '+')
    if (!preg_match("/^\+?[0-9]{10,15}$/", $phoneNumber)) {
        echo "<script>
                        document.addEventListener('DOMContentLoaded', function () {

            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone Number',
                text: 'The phone number you entered is not valid.'
            });
    });
        </script>";
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Validate referral code if provided
        $referredAffiliateId = null;
        if (!empty($referralCode)) {
            $stmt = $mysqli->prepare("SELECT id FROM affiliates WHERE affiliate_id = ?");
            $stmt->bind_param('s', $referralCode);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $referredAffiliateId = $result->fetch_assoc()['id'];
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Referral Code',
                        text: 'The referral code you entered does not exist.'
                    });
                </script>";
                exit();
            }
        }

        // Insert customer into the database
        $stmt = $mysqli->prepare("INSERT INTO customers (name, email, phone, password, affiliate, affiliate_referrer_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssisii', $name, $email, $phoneNumber, $hashedPassword, $isAffiliate, $referredAffiliateId);

        if ($stmt->execute()) {
            // If the user opted to be an affiliate, add to the `affiliates` table
            if ($isAffiliate === 1) {
                // Helper function: Generate unique affiliate ID
                function generateUniqueAffiliateId()
                {
                    return 'AFF' . time() . strtoupper(substr(md5(uniqid()), 0, 6));
                }
                $affiliateId = generateUniqueAffiliateId();
                

                $customerId = $mysqli->insert_id; // Get the newly inserted customer ID
                $stmtAffiliate = $mysqli->prepare("INSERT INTO affiliates (customer_id, affiliate_id, referrer_id, created_at) VALUES (?, ?, ?, NOW())");
                $stmtAffiliate->bind_param('iss', $customerId, $affiliateId, $referralCode);

                if (!$stmtAffiliate->execute()) {
                    throw new Exception("Database error: " . $stmtAffiliate->error);
                }

                echo "<script>
                    document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: 'You have successfully registered as an affiliate.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
            });
                </script>";
                exit();
            }

            // Registration success
            echo "<script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration Successful',
                        text: 'You have successfully created an account.',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                });
            </script>";
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: 'Registration Error',
                text: 'An error occurred during registration. Please try again later.'
            });
    });
        </script>";
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($mysqli)) $mysqli->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Free Account</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>User Registration</h1>
        <form method="POST" action="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="referral_code">Referral Code (optional):</label>
            <input type="text" id="referral_code" name="referral_code" 
                value="<?php echo $referralCodeFromUrl; ?>">

            <div>
                <label for="is_affiliate">Do you want to be an affiliate?</label>
                <input type="checkbox" id="is_affiliate" name="is_affiliate" value="1">
            </div>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Sign In Here</a></p>

    </div>
</body>
</html>