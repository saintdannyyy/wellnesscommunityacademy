<?php
    session_start();

    require_once __DIR__ . '../../../config/loadENV.php';
    if ($_ENV['APP_ENV'] === 'dev') {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $adminMail = $_ENV['ADMIN_dev_EMAIL'];
    } else {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        $adminMail = $_ENV['ADMIN_EMAIL'];
    }

    // Start session and check if the customer is already logged in
    if (isset($_SESSION['customer_id'])) {
        header('Location: ../');
        exit();
    }

    // Decode referral code
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- font awesome cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- google roboto font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Step 1: Terms and Conditions -->
    <div class="container" id="step-1" style="margin-top: 20vh; display: flex; justify-content: center; align-items: center;">
        <div class="card text-left" style="width: 700px; padding: 10px;">
            <div class="card-body">
                <div style="text-align: center;">
                    <i class="fa-solid fa-file-invoice fa-fade" style="font-size: 70px; color: #74C0FC;"></i>
                    <h5 style="font-weight: bold;">Terms and Conditions</h5>
                </div>
                <p style="font-weight: bold;">Welcome to Wellness Community Academy Affiliate Program!</p>
                <p>Are you passionate about health, wellness, and empowering others to live their best lives? Join our affiliate program and earn a 15-20% commission on every referral sale you make! When you sign up for one of our products, you sign up for them all (so you don't have to sign up multiple times). That means if you sign up to promote a course, you can promote all courses and eBooks [all our products]. With a wide variety of health courses and wellness eBooks available, you can help your audience discover valuable resources while earning for every sale you generate.</p>
                <p style="font-weight: bold;">Why Join?</p>
                <ul>
                    <li><span style="font-weight: bold;">Generous Commission Rates:</span> Earn 15%-20% for each sale made through your referral link.</li>
                    <li><span style="font-weight: bold;">Free to Join:</span> There’s no cost to sign up or participate as an affiliate.</li>
                    <li><span style="font-weight: bold;">Minimum Sales:</span> You can start earning from your first sale—no quotas or minimums are required.</li>
                    <li><span style="font-weight: bold;">JV Broker:</span> Earn an extra 2% commission on direct affiliates you refer (when they make sales).</li>
                    <li><span style="font-weight: bold;">Reporting:</span> Access analytics tools and insights to track your performance and optimize your strategies.</li>
                </ul>
                <p style="font-weight: bold;">How It Works?</p>
                <ol>
                    <li><span style="font-weight: bold;">Sign Up:</span> Joining the affiliate program is completely free. Simply sign up, and once approved, you will receive your unique referral link.</li>
                    <li><span style="font-weight: bold;">Share:</span> Promote Wellness Community Academy’s products (eBooks and courses) through your website, blog, social media, email marketing, PPC ads, YouTube, or any other channel you prefer.</li>
                    <li><span style="font-weight: bold;">Earn:</span> When someone makes a purchase through your affiliate link, you earn a commission. Sales are tracked when a customer clicks your link and completes a successful purchase.</li>
                </ol>
                <p style="font-weight: bold;">Payout Schedule?</p>
                <ul>
                    <li>Commissions are credited after a 30-day hold period for eBook purchases and up to 45 days for other products to account for potential refunds.</li>
                    <li>Affiliates are paid monthly between the 15th and 20th of each month. Timings may vary slightly as all affiliate accounts are reviewed manually.</li>
                    <li>We aim to ensure all qualifying affiliates are paid promptly.</li>
                </ul>
                <p style="font-weight: bold;">Affiliate Guidelines?</p>
                <ul>
                    <li><span style="font-weight: bold;">No Self-Purchasing:</span> Affiliates are prohibited from purchasing any products through their own affiliate links. Doing so will result in forfeiture of commissions and termination of affiliate status.</li>
                    <li><span style="font-weight: bold;">Tracking Limitations:</span> While we strive to ensure accurate tracking, there are factors beyond our control that can impact the ability to track every referral perfectly.</li>
                    <li><span style="font-weight: bold;">Taxes:</span> You are responsible for paying your own taxes.</li>
                </ul>
                <p><span style="font-weight: bold;">Promotion Methods:</span> We encourage you to promote in ways that best align with your strengths and audience! Here are some popular methods used by our affiliates:</p>
                <ul>
                    <li>Blogging and content marketing</li>
                    <li>SEO and Paid search (PCC)</li>
                    <li>Social media Promotion</li>
                    <li>YouTube videos and reviews</li>
                    <li>Email marketing campaigns</li>
                </ul>
                <p><span style="font-weight: bold;">Affiliate Support:</span> If you have any questions or need support, we’re here to help. Email us at <a href="mailto:cooperdockeryhealth@gmail.com">cooperdockeryhealth@gmail.com</a> for assistance.</p>
                <p>Thank you for being part of the Wellness Community Academy’s Affiliate Program. Together, we can spread the message of health and wellness while earning and growing!</p>
                <a id="next-to-step-2" class="btn btn-primary btn-block" style="color: white;">I Accept</a>
            </div>
        </div>
    </div>
    <!-- Step 2: Affiliate Registration -->
    <div class="container" id="step-2" style="display: none; margin-top: 20vh; display: none; justify-content: center; align-items: center;">
        <div class="card text-left" style="width: 500px; padding: 20px;">
            <div class="card-body">
                <h5 style="font-weight: bold; text-align: center;">Step 2: Affiliate Registration</h5>
                <form id="registrationForm">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>

                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" required>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>

                    <label for="referral_code">Referral Code (optional):</label>
                    <input type="text" id="referral_code" name="referral_code" value="<?php echo $referralCodeFromUrl; ?>">

                    <button type="button" id="next-to-step-3" class="btn btn-primary btn-block" style="color: white;">Continue</button>
                </form>
                <p>Already have an account? <a href="login.php">Sign In Here</a></p>
            </div>
        </div>
    </div>
    <!-- Step 3: Payment Details -->
    <div class="container" id="step-3" style="display: none; margin-top: 20vh; display: none; justify-content: center; align-items: center;">
        <div class="card text-left" style="width: 500px; padding: 20px;">
            <div class="card-body">
                <h5 style="font-weight: bold; text-align: center;">Step 3: Lets Add Your Payment Details</h5>
                <p>We will be paying you through the details you provide below.</p>
                <form id="paymentForm">
                    <label class="form-label">Mobile Network</label>
                    <select class="form-control" id="service_provider" name="service_provider" required>
                        <option value="MTN">MTN</option>
                        <option value="Vodafone">Vodafone</option>
                        <option value="AirtelTigo">AirtelTigo</option>
                    </select>
                    <label class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" required>

                    <label class="form-label">Account Holder</label>
                    <input type="text" class="form-control" id="accountHolder" name="accountHolder" required>

                    <button type="submit" id="complete-registration" class="btn btn-primary btn-block" style="color: white;">Submit Payment</button>
                </form>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let affiliateId;
    // Step 1 -> Step 2 Transition
    document.getElementById("next-to-step-2").addEventListener("click", function () {
        document.getElementById("step-1").style.display = "none";
        document.getElementById("step-2").style.display = "flex";
    });

    // Step 2 -> Step 3 Transition (with AJAX)
    document.getElementById("next-to-step-3").addEventListener("click", function () {
        const form = document.getElementById("registrationForm");
        if (form.checkValidity()) {
            const button = document.getElementById("next-to-step-3");
            button.disabled = true;
            button.style.backgroundColor = "grey";
            button.style.cursor = "not-allowed";
            button.innerHTML = "Creating Account...";

            const formData = new FormData(form);
            
            // Sending registration data via AJAX
            fetch('../aff_api/register.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                // console.log("returned id", data.id);
                affiliateId = data.id;
                // console.log("Affiliate ID", affiliateId);

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Click Continue to Add Payment Details',
                        text: data.message,
                        confirmButtonText: 'Continue',
                    }).then(() => {
                        document.getElementById("step-2").style.display = "none";
                        document.getElementById("step-3").style.display = "flex";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: data.message || 'Please try again.',
                        confirmButtonText: 'OK',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'An Error Occurred',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonText: 'OK',
                });
            })
            .finally(() => {
                button.disabled = false;
                button.style.backgroundColor = "";
                button.style.cursor = "";
                button.innerHTML = "Continue";
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Form',
                text: 'Please fill in all required fields.',
                confirmButtonText: 'OK',
            });
        }
    });

    // Step 3: Payment Submission (with AJAX)
    document.getElementById("paymentForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const button = document.getElementById("complete-registration");
        button.disabled = true;
        button.style.cursor = "not-allowed";
        button.style.backgroundColor = "grey";
        button.innerHTML = "Adding Payment Details...";

        const form = document.getElementById("paymentForm");
        
        const formData = new FormData();
        formData.append('affiliate_id', affiliateId);

        // Add other data fields to the formData if needed
        formData.append('service_provider', service_provider.value);
        formData.append('phone_number', phone_number.value);
        formData.append('accountHolder', accountHolder.value);

        // Send payment details via AJAX
        fetch('../aff_api/save_payment_details.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status = "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Congratulations!',
                    text: 'Registration complete! Check your email for confirmation.',
                    confirmButtonText: 'OK',
                }).then(() => {
                    window.location.href = 'login.php'; // Redirect to login page
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Submission Failed',
                    text: data.message || 'Please try again.',
                    confirmButtonText: 'OK',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'An Error Occurred',
                text: 'Something went wrong while submitting payment details.',
                confirmButtonText: 'OK',
            });
        })
        .finally(() => {
            button.disabled = false;
            button.style.backgroundColor = "";
            button.style.cursor = "";
            button.innerHTML = "Submit Payment";
        });
    });
</script>
</body>

</html>