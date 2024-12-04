<?php
// Enable error reporting for debugging (only for development)
// Include the script to load environment variables
require_once __DIR__ . '../../../config/loadENV.php';

if ($_ENV['APP_ENV'] === 'dev') { 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}

// Check if the affiliate is logged in
if (isset($_SESSION['affiliate_id'])) {
    header('Location: login.php');
    exit();
}


// Include database connection
include('../../conn/conn.php');
session_start();

// Include SweetAlert library globally
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

// Extract referral code from the URL if available
$referralCodeFromUrl = isset($_GET['rf']) ? htmlspecialchars(trim($_GET['rf'])) : '';

// Decode the referral code if it is encoded
function decodeReferralId($referralCodeFromUrl) {
    $key = $_ENV['AFFILIATE_ID_ENCRYPTION_KEY'];
    return openssl_decrypt(base64_decode($referralCodeFromUrl), 'aes-256-cbc', $key, 0, substr($key, 0, 16));
}

if (!empty($referralCodeFromUrl)) {
    $referralCodeFromUrl = decodeReferralId($referralCodeFromUrl);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phoneNumber = htmlspecialchars(trim($_POST['phone']));
    $password = htmlspecialchars($_POST['password']);
    $referralCode = !empty($_POST['referral_code']) ? htmlspecialchars(trim($_POST['referral_code'])) : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        displayAlert('error', 'Invalid Email', 'Please enter a valid email address.');
        exit();
    }

    // Validate phone number (10–15 digits, optional '+')
    if (!preg_match("/^\+?[0-9]{10,15}$/", $phoneNumber)) {
        displayAlert('error', 'Invalid Phone Number', 'The phone number you entered is not valid.');
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
                // Referral code is valid, get the affiliate ID of the person who referred
                $referredAffiliateId = $result->fetch_assoc()['id'];
            } else {
                // Referral code is invalid, alert the user and keep the referral code in the URL
                displayAlert('error', 'Invalid Referral Code', 'The referral code you entered does not exist.', true);
                exit();
            }
        }

        // Generate a unique affiliate ID
        $uniqueAffiliateId = generateUniqueAffiliateId();

        // Insert affiliate into the database
        $stmt = $mysqli->prepare("INSERT INTO affiliates (affiliate_id, name, email, phone, password, referral_code, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        $stmt->bind_param('ssssss', $uniqueAffiliateId, $name, $email, $phoneNumber, $hashedPassword, $referralCode);

        if ($stmt->execute()) {
            $affiliateDbId = $stmt->insert_id;

            // Log referral relationship if applicable
            if ($referredAffiliateId !== null) {
                $stmtReferral = $mysqli->prepare("INSERT INTO affiliate_referrals (affiliate_id, referred_by) VALUES (?, ?)");
                $stmtReferral->bind_param('ii', $affiliateDbId, $referredAffiliateId);
                $stmtReferral->execute();
            }

            // Success alert
            displayAlert('success', 'Registration Successful', 'You have successfully registered as an affiliate.', 'login.php');
        } else {
            throw new Exception("Database error: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        displayAlert('error', 'Registration Error', 'An error occurred during registration. Please try again later.');
    } finally {
        // Cleanup
        if (isset($stmt)) $stmt->close();
        if (isset($mysqli)) $mysqli->close();
    }
}

// Helper function: Display SweetAlert
function displayAlert($icon, $title, $text, $keepReferral = false)
{
    $currentUrl = htmlspecialchars($_SERVER['REQUEST_URI']); // Get the current URL with query string
    echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                timer: 2000, // 2-second timeout
                timerProgressBar: true, // Show a progress bar for the timer
            }).then((result) => {
                // Redirect after alert closes
                if (result.dismiss === Swal.DismissReason.timer || result.isConfirmed) {
                    " . ($keepReferral ? "window.location.href = '$currentUrl';" : "") . "
                }

            });
        });
    </script>";
}

// Helper function: Generate unique affiliate ID
function generateUniqueAffiliateId()
{
    return 'AFF' . time() . strtoupper(substr(md5(uniqid()), 0, 6));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Affiliate Registration</h1>
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

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Sign In Here</a></p>
    </div>
</body>
</html>