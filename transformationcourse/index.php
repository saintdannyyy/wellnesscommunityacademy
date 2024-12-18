<?php
session_start();
// require_once __DIR__ . '../config/loadENV.php';
require('../config/loadENV.php');
if ($_ENV['APP_ENV'] === 'dev') {
    ini_set('display_errors', 1);  // Show errors in development environment
    error_reporting(E_ALL);       // Report all errors
} else {
    ini_set('display_errors', 0);  // Hide errors in production environment
}
$paystackPublicKey = ($_ENV['APP_ENV'] === 'prod')
    ? $_ENV['PAYSTACK_PUBLIC_KEY_LIVE']
    : $_ENV['PAYSTACK_PUBLIC_KEY_TEST'];

// Example usage
// echo "Using Paystack public Key: " . $paystackPublicKey;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transformation to a Healthier You Course</title>
    <meta name="description" content="Lose Weight, Improve Health, and Energy Level with Dr. Dona Cooper-Dockery">
    <meta name="keywords"
        content="14 days to amazing health, Dr. dona cooper, Dona Cooper-Dockery, get healthy with Dr. Cooper, Cooper wellness center, cooper internal medicine, transformation to a healthier you, cooper wellness center">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="Transformation to a Healthier You Course">
    <meta property="og:description" content="">
    <meta property="og:image"
        content="https://d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3829645_1554045518705New_Transformation_to_healthier_you_course.png">

    <!-- Font icons preconnect -->
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="//d2uolguxr56s4e.cloudfront.net" crossorigin>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//d2uolguxr56s4e.cloudfront.net">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--scripts for payments processing-->
    <script>
        // Function to open the modal
        function openPaymentSelection() {
            document.getElementById('paymentSelection').style.display = 'flex';
        }
                                    
        // Function to close the modal
        function closePaymentSelection() {
            document.getElementById('paymentSelection').style.display = 'none';
        }
                                    
        // Function to show the selected tab
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(function(tab) {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
        }
                                        
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("paymentModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
                                    
        let courseNO;
        let ghs_price;
        let courseName;
        function openPaymentPopup(courseID) {
            const formData = new FormData();
            formData.append('courseID', courseID);
            fetch('../courses/courses.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log("Raw response:", response);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log("Fetch successful:", data);
                    document.getElementById('price').innerText = data.price_usd;
                    document.getElementById('course_price').innerText = data.price_usd;
                    courseNO = data.courseID;
                    ghs_price = data.price;
                    course_name = data.course;
                    document.getElementById('paymentModal').style.display = 'flex';
                } else {
                    console.error("Server responded with an error:", data);
                    alert(`Error, ${data.message}`);
                }
            })
            .catch(err => console.error('Error fetching price:', err)); // Correctly placed catch block
        }

        function nextStep(stepNumber) {
            // Hide all form steps
            document.querySelectorAll('.form-step').forEach(function(step) {
                step.style.display = 'none';
            });
            
            // Show the current step
            document.getElementById(`step-${stepNumber}`).style.display = 'block';
        }
        
        // Payment script
        let course_no;
        let price_ghs;
        async function payWithPaystack(e) {
            e.preventDefault();
            const email = document.getElementById("mail").value;
            const phone = document.getElementById("phone").value;
            const course_no = courseNO;
            const price_ghs = ghs_price;
            const course_purchased = course_name
            console.log("Transaction amount:", price_ghs);
            // Validate required fields
            if (!email || !phone || !course_no || !price_ghs) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please ensure all fields are filled correctly.',
                    confirmButtonText: 'OK'
                });
                return;
            }
            const amountInPesewas = Math.round(price_ghs * 100);
            
            const paystackPublicKey = "<?php echo $paystackPublicKey; ?>";
                
            // Initialize Paystack payment
            const handler = PaystackPop.setup({
                key: paystackPublicKey,
                email: email,
                amount: amountInPesewas,
                currency: "GHS",
                ref: "COURSE" + Math.floor((Math.random() * 1000000000) + 1),
                metadata: {
                    custom_fields: [
                        {display_name: "Phone",variable_name: "phone",value: phone},
                        {display_name: "Course ID",variable_name: "courseID",value: course_no},
                        {display_name: "Course Name",variable_name: "course_purchased",value: course_purchased}
                    ]
                },
                callback: function(response) {
                    // Payment successful
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Reference: ' + response.reference,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "../pay/courses_pay.php?reference=" + response.reference;
                    });
                },
                onClose: function() {
                    // Payment was canceled
                    Swal.fire({
                        icon: 'info',
                        title: 'Transaction Cancelled',
                        text: 'Transaction was not completed. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
                                            
            handler.openIframe();
        }
    </script>

    <!--
        Google fonts are computed and loaded on page build via save.js
        Individual stylesheets required are listed in /css/pages/skeleton.css
    -->

    <!--<link href="cssskeleton.min.css" rel="stylesheet">-->
    <link type="text/css" rel="preload"
        href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
    </noscript>

    <script>
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        (function (w) { "use strict"; if (!w.loadCSS) { w.loadCSS = function () { } } var rp = loadCSS.relpreload = {}; rp.support = function () { var ret; try { ret = w.document.createElement("link").relList.supports("preload") } catch (e) { ret = false } return function () { return ret } }(); rp.bindMediaToggle = function (link) { var finalMedia = link.media || "all"; function enableStylesheet() { link.media = finalMedia } if (link.addEventListener) { link.addEventListener("load", enableStylesheet) } else if (link.attachEvent) { link.attachEvent("onload", enableStylesheet) } setTimeout(function () { link.rel = "stylesheet"; link.media = "only x" }); setTimeout(enableStylesheet, 3e3) }; rp.poly = function () { if (rp.support()) { return } var links = w.document.getElementsByTagName("link"); for (var i = 0; i < links.length; i++) { var link = links[i]; if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) { link.setAttribute("data-loadcss", true); rp.bindMediaToggle(link) } } }; if (!rp.support()) { rp.poly(); var run = w.setInterval(rp.poly, 500); if (w.addEventListener) { w.addEventListener("load", function () { rp.poly(); w.clearInterval(run) }) } else if (w.attachEvent) { w.attachEvent("onload", function () { rp.poly(); w.clearInterval(run) }) } } if (typeof exports !== "undefined") { exports.loadCSS = loadCSS } else { w.loadCSS = loadCSS } })(typeof global !== "undefined" ? global : this);

        window.global_id = 'fCAxF8e3UBYf';
        window.secure_base_url = '//app.kartra.com/';
    </script>

    <!--headerIncludes-->
    <style>
        .overlay_builder {
            position: relative;
        }

        .kartra_optin_footer-poweredby>p {
            font-size: 12px;
            line-height: 130%;
            font-weight: 300;
            color: #333;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        body.modal-open {
            overflow: hidden;
            overflow-x: ;
        }

        #page_background_color {
            background-color: #ffffff;
        }

        body {
            background-color: #ffffff;

        }


        [data-effect] {
            visibility: hidden;
        }
    </style>
    <script>
        var google_analytics = null;
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-175519445-2"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-175519445-2');
    </script>

    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "aqpjjtqipr");
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-217187331-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-217187331-1');
    </script>


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-217187331-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-217187331-1');
    </script>


    <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments) };
    t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
    y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "aqpjjtqipr");
    </script>
    <link rel=“canonical” href=“https://www.cooperwellnesscenter.com/” />

    <script src="js/build/front/pages/skeleton-above.js"></script>
    <link rel="preconnect" href="//vip.timezonedb.com">
    <link rel="dns-prefetch" href="//vip.timezonedb.com">
    <style id="pagesCustomCSS">
        .countdown-section--margin-top-medium {
            margin-top: 50px;
        }

        .countdown__item--bg-one {
            background-color: rgb(255, 49, 70);
        }

        .countdown__item--bg-two {
            background-color: rgb(255, 169, 58);
        }

        .countdown__item--bg-three {
            background-color: rgb(19, 152, 254);
        }

        .countdown__item--bg-four {
            background-color: rgb(232, 121, 51);
        }

        .kartra_headline_block--justify-content-center {
            justify-content: center;
        }

        .background-item--shadow-1 {
            box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.06);
        }

        .background-item--shadow-2 {
            box-shadow: 0px 3px 20px rgba(0, 0, 0, 0.1)
        }

        .background-item--rounded-top {
            border-radius: 4px 4px 0px 0px !important;
        }

        .background-item--rounded-bottom {
            border-radius: 0px 0px 4px 4px !important;
        }

        @media(max-width: 991px) {
            .kartra_image--sm-margin-bottom-extra-medium-important {
                margin-bottom: 40px !important;
            }
        }
    </style>
</head>

<body>
    <div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object"
        data-kt-type="kartra_page_tracking" data-kt-value="fCAxF8e3UBYf" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content" style="padding: 40px 0px; background-color: rgba(0, 0, 0, 0);" id="_0m3wi8oyv">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid" id="C8EQzwNDRO">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="wn5CkFUmTn">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 20px; margin-bottom: 20px;">
                                        <p style="text-align: center; font-size: 1.4rem;"><strong><span
                                                    style="font-size: 1.4rem;"><span
                                                        style="color: rgb(255, 0, 0); font-size: 1.4rem;">Lose Weight,
                                                        Improve Health and Energy levels</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="video"
                                    data-thumbnail="https://d11n7da8rpqbjy.cloudfront.net/strategicsecrets/generated-kartra-video-thumb-2547971_1542617601687Tumeric_for_Your_Health.mp4.jpg"
                                    data-screenshot="false" id="zT3yW4a3md">
                                    <div class="kartra_video kartra_video--player_3"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%"
                                            src="https://app.kartra.com/external_video/vimeo/249048844?autoplay=true"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="249048844?autoplay=true"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div data-component="button" style="width: auto; display: flex;" id="nhWX3JJ2K0">
                                    <button onclick="openPaymentSelection()"
                                        class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small"
                                        style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: 'Roboto Condensed'; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                        <span class="fa fa-arrow-right" style="color: rgb(255, 255, 255); font-weight: 700;"></span> START THIS COURSE TODAY
                                    </button>
                                    
                                    <!-- Payment Selection Modal-->
                                    <div id="paymentSelection" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                            <button onclick="closePaymentSelection()"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                            
                                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
                                    
                                            <!-- Tab Navigation -->
                                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; color: white; width: 50%;">Pay with Mobile Money</button>
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center toggle_product dg_popup"
                                                  style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; padding: 10px 20px; font-family: 'Roboto Condensed'; text-align: center;"
                                                data-frame-id="_0m3wi8oyv" data-kt-layout="0" data-kt-type="checkout"
                                                data-kt-owner="DpwDQa6g" data-kt-value="c7d4071f65bc70e18cfb89c667e6d154"
                                                data-funnel-id="154974" data-product-id="154974"
                                                data-price-point="c7d4071f65bc70e18cfb89c667e6d154"
                                                rel="c7d4071f65bc70e18cfb89c667e6d154" data-asset-id="54" target="_parent"><span
                                                    class="kartra_icon__icon fa fa-arrow-right"
                                                    style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                                Pay with Credit/PayPal</a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('2')"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay with Mobile Money</button>
                                            </div>
                                        </div>
                                    </div>     
                                </div>
                                <!--Modal-->
                                <div id="paymentModal" class="modal"
                                    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                    <div class="modal-content"
                                        style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: left; position: relative;">
                                        <h2 id="payment-header"
                                            style="text-align: center; font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">
                                            Complete Payment</h2>

                                        <!-- Step 1: Personal Information -->
                                        <div class="form-step active" id="step-1" style="display: block;">
                                            <label for="name" style="font-weight: bold; color: #555;">Full Name:</label>
                                            <input type="text" id="name" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="mail" style="font-weight: bold; color: #555;">Email:</label>
                                            <input type="email" id="mail" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="phone" style="font-weight: bold; color: #555;">Phone
                                                Number:</label>
                                            <input type="text" id="phone" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                                
                                                <p>This course costs <span style="font-weight: bold; color:red;">USD $<span id="course_price" style="color: red;"></span></span><br/>You'll be charged in your local currency.</p>    
                                                <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                        </div>

                                        <!-- Step 2: Payment Details -->
                                        <div class="form-step" id="step-2" style="display: none;">
                                            <h3 style="text-align: center; margin-bottom: 20px;">Payment Amount: $<span
                                                    id="price">0</span></h3>
                                            <button onclick="payWithPaystack(event)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay
                                                Now</button>
                                            <button onclick="nextStep(1)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <button onclick="document.getElementById('paymentModal').style.display='none';"
                                            style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                </div>
                                
                                <div data-component="image">
                                    <img class="kartra_image pull-center kartra_image--full"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="text" id="9BOxeoJaWl">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 20px; margin-bottom: 20px;">
                                        <h1><span style="font-size:1.60rem;">The Only 3 Things You'll Need:</span></h1>
                                    </div>
                                </div>
                                <div data-component="text" id="Wzwf7Q3lDl">
                                    <div class="kartra_text" style="position: relative;">
                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;"><span
                                                    style="font-size: 0.8rem;"><strong>1.</strong> A burning desire to
                                                    change our life and health for the better.</span></span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;"><span
                                                    style="font-size: 0.8rem;"><strong>2.</strong> A few minutes to
                                                    maintain a journal and document your health goals.</span></span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><strong>3.</strong> A commitment to complete this
                                            course.</p>

                                        <p style="font-size: 0.8rem;"> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid" id="GTaUHWMADH">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="IZm5DYddu1">
                                    <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                        style="position: relative; margin-top: 0px; margin-bottom: 30px;">
                                        <h4><span style="line-height: 1.2em;"><span
                                                    style="background-color: rgb(255, 255, 0); line-height: 1.2em; font-size: 1rem; color: rgb(128, 0, 0);">If
                                                    You're OK with Those 3 Things... </span><span
                                                    style="background-color: rgb(255, 255, 0); line-height: 1.2em; font-size: 1rem; color: rgb(128, 0, 0);">Then
                                                    This Course Is For You! </span></span></h4>
                                    </div>
                                </div>
                                <div data-component="text" id="vDqJDDM62S">
                                    <div class="kartra_text" style="position: relative;">
                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;"><strong><span
                                                        style="font-size: 0.8rem;">Transformation to a Healthier
                                                        You</span></strong> is everyone's goal but not all achieve
                                                it.</span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><strong><span style="font-size: 0.8rem;">Until
                                                    now...</span></strong></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">You <em><span
                                                        style="font-size: 0.8rem;">finally</span></em> have access to a
                                                course with a step-by-step approach to prevent diseases, improve or even
                                                reverse diseases. </span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">I have developed
                                                this course just for you because I have used it with so many of my
                                                patients who are now enjoying better health.</span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><strong><span style="font-size: 0.8rem;"><em><span
                                                            style="font-size: 0.8rem;">Some have even reversed their
                                                            disease and are off medications
                                                            entirely. </span></em></span></strong></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">There are many
                                                courses online promoting health and wellness.</span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">However, you're
                                                getting relevant expert insights from an actual medical doctor with over
                                                26 years of experience.</span></p>

                                        <p style="font-size: 0.8rem;"> </p>

                                        <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;"><strong><span
                                                        style="font-size: 0.8rem;">You're about discover
                                                        life-changing secrets</span></strong> about four pillars of
                                                health, which, when followed, will indeed propel you to optimal health
                                                for life.</span></p>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="dH9EUIRZVB">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319516_1587509248039Transformation_to_healthier_you_course_banner.png">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319516_1587509248039Transformation_to_healthier_you_course_banner.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            id="1544989749662_formbutton"
                                            style="border-color: rgb(194, 174, 131); border-style: solid; border-width: 3px; margin: 20px auto; opacity: 1;"
                                            data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319516_1587509248039Transformation_to_healthier_you_course_banner.png">
                                    </picture>
                                </div>
                                <div data-component="button" style="width: auto;" id="vXT09FblI6">
                                    <button onclick="openPaymentSelection()"
                                        class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center"
                                        style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: 'Roboto Condensed'; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                        <span class="kartra_icon__icon fa fa-shopping-cart" style="color: rgb(255, 254, 253); border-color: rgb(255, 254, 253); font-weight: 400;"></span>ENROLL IN THIS COURSE NOW
                                    </button>
                                    
                                    <!-- Payment Selection Modal -->
                                    <div id="paymentSelection" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                            <button onclick="closePaymentSelection()"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                            
                                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
                                    
                                            <!-- Tab Navigation -->
                                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Credit/PayPal</button>
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Mobile Money</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_product dg_popup"
                                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                                    data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                                    data-kt-owner="DpwDQa6g" data-kt-value="c7d4071f65bc70e18cfb89c667e6d154"
                                                    data-funnel-id="154974" data-product-id="154974"
                                                    data-price-point="c7d4071f65bc70e18cfb89c667e6d154"
                                                    rel="c7d4071f65bc70e18cfb89c667e6d154" data-asset-id="58" target="_parent">Pay with Card/Paypal</a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('2')"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay with Mobile Money</button>
                                            </div>
                                        </div>
                                    </div>     
                                </div>
                                <!--Modal-->
                                <div id="paymentModal" class="modal"
                                    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                    <div class="modal-content"
                                        style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: left; position: relative;">
                                        <h2 id="payment-header"
                                            style="text-align: center; font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">
                                            Complete Payment</h2>

                                        <!-- Step 1: Personal Information -->
                                        <div class="form-step active" id="step-1" style="display: block;">
                                            <label for="name" style="font-weight: bold; color: #555;">Full Name:</label>
                                            <input type="text" id="name" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="mail" style="font-weight: bold; color: #555;">Email:</label>
                                            <input type="email" id="mail" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="phone" style="font-weight: bold; color: #555;">Phone
                                                Number:</label>
                                            <input type="text" id="phone" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                                
                                            <p style="colour: red;">This course costs USD<span id="course_price"></span><br/>You'll be charged in your local currency.</p>    
                                            <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                        </div>

                                        <!-- Step 2: Billing Details -->
                                        <div class="form-step" id="step-2" style="display: none;">
                                            <label for="billing-address" style="font-weight: bold; color: #555;">Billing
                                                Address:</label>
                                            <input type="text" id="billing-address" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="city" style="font-weight: bold; color: #555;">City:</label>
                                            <input type="text" id="city" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="zip" style="font-weight: bold; color: #555;">Zip Code:</label>
                                            <input type="text" id="zip" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <button onclick="nextStep(3)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                            <button onclick="nextStep(1)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <!-- Step 3: Payment Details -->
                                        <div class="form-step" id="step-3" style="display: none;">
                                            <h3 style="text-align: center; margin-bottom: 20px;">Payment Amount: $<span
                                                    id="price">0</span></h3>
                                            <button onclick="payWithPaystack(event)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay
                                                Now</button>
                                            <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <button onclick="document.getElementById('paymentModal').style.display='none';"
                                            style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                </div>
                                    
                                </div>
                                <div data-component="text" id="JeC9dBJvIK">
                                    <div class="kartra_text" style="position: relative;">
                                        <p style="font-size: 0.65rem; text-align: center;"> </p>

                                        <p style="font-size: 0.65rem; text-align: center;"><span
                                                style="font-size: 0.65rem;"><em><span
                                                        style="font-size: 0.65rem;">+Starting as low as
                                                        <s>$97</s><strong><span style="font-size: 0.65rem;"><em><span
                                                                        style="font-size: 0.65rem;"> </span></em></span></strong>$59!</span></em></span>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="image" id="dlNtzWLeui">
                                    <img class="kartra_image pull-center kartra_image--full"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="text" id="8oU6YZLZpx">
                                    <div class="kartra_text" style="position: relative;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid" id="jSGLjI0aCM"></div>
                </div>
            </div>
            <script type="text/javascript" async="" class="everWebinarScript"
                src="//events.genndi.com/register.evergreen.extra.js"></script>
            <div class="content content--padding-extra-large content--padding-bottom-special-medium"
                style="background-color: rgb(49, 85, 40); padding: 0px 0px 30px;" id="_f4a6crvr6">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">

                                <div data-component="image" href="javascript: void(0);" id="i4pNCBdlOe">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3803061_1553795591074CLCI_As_Featured_in-.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3803061_1553795591074CLCI_As_Featured_in-.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-left background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            id="1553795522346_formbutton"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3803061_1553795591074CLCI_As_Featured_in-.png">
                                    </picture>
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--size-sm-giant kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--white kartra_headline--margin-bottom-big-tiny"
                                        style="position: relative;">
                                        <h2><span><span style="font-size:2.33rem;">In This Course You
                                                    Will:</span></span></h2>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--flex kartra_list__item--flex-md-reverse"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894236058_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-heart"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small-reverse kartra_item_info--md-text-right kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Be Motivated</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                                    style="position: relative;">
                                                    <p>To make the change to become a healthier version of yourself.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex kartra_list__item--flex-md-reverse"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894256149_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-clock-o"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small-reverse kartra_item_info--md-text-right kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Be Ready</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                                    style="position: relative;">
                                                    <p>To embrace lasting healthy lifestyle habits for good.</p>


                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex kartra_list__item--flex-md-reverse"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894292909_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-group"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small-reverse kartra_item_info--md-text-right kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Be Challenged</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--margin-bottom-small"
                                                    style="position: relative;">
                                                    <p>To actively participate in your own transformation to win big.
                                                    </p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--align-center kartra_element_wrapper--single-white-shadow-iphone7-mockup kartra_element_wrapper--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        class="background-item background_changer--blur0"
                                        data-bg='url("https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/17448750_16143211328Lwloss-weight.png")'>
                                    </div>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        class="hand_table_mock_up--frame"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp_feature_img_4.png">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894331717_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-mortar-board"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Learn More</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                                    style="position: relative;">
                                                    <p>About evidence-based health and longevity secrets.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894478499_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-apple"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Eat More</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                                    style="position: relative;">
                                                    <p>Healthy super foods to make it easier for you during this
                                                        journey.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                                id="1553894488527_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-male"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                                    style="position: relative;">
                                                    <p>Do More</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--margin-bottom-small"
                                                    style="position: relative;">
                                                    <p>Than you've done in a while with your newfound energy levels.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-large" style="background-color:rgb(255,255,255);" id="_7wthzxlhe">
                <div class="background_changer" style="opacity: 0.1"
                    data-bg="url(//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-testimonials/kp_testimonials_img_22.jpg)">
                </div>
                <div class="background_changer_overlay"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="cG4K5jBfjy">
                                    <div class="kartra_text" style="position: relative;">
                                        <h2><span>
                                                <font color="#000000" face="verdana"><span><span
                                                            style="font-size: 1.2rem;">This Course if <u><span
                                                                    style="font-size: 1.2rem;"><span
                                                                        style="font-size: 1.2rem;">NOT</span></span></u>
                                                            For You If:</span></span></font>
                                            </span></h2>

                                        <ul>
                                            <li><span><span><span style="font-size:0.80rem;">You don't like getting
                                                            results.</span></span></span></li>
                                            <li><span><span><span style="font-size:0.80rem;">You're not willing to put
                                                            in a little bit of work now to reap the rich benefits in a
                                                            few weeks.</span></span></span></li>
                                            <li><span><span><span style="font-size:0.80rem;">You want to do nothing but
                                                            sit on the couch, hoping the weight will magically fall
                                                            off.</span></span></span></li>
                                        </ul>

                                        <h3><span><strong><span><span style="font-size: 1rem;">This <u><span
                                                                    style="font-size: 1rem;"><span
                                                                        style="font-size: 1rem;">Is</span></span></u>
                                                            For You If:</span></span></strong></span></h3>

                                        <ul>
                                            <li><span><span><span style="font-size:0.80rem;">You're seeking to better
                                                            your health but need reliable
                                                            information.</span></span></span></li>
                                            <li><span><span><span style="font-size:0.80rem;">You truly want to be
                                                            happier, healthier, and more energetic.</span></span></span>
                                            </li>
                                            <li><span><span><span style="font-size:0.80rem;">You're willing to put in
                                                            less than 35 mins a day to learn and apply these proven
                                                            secret strategies.</span></span></span></li>
                                        </ul>

                                        <h4 style="text-align: center;"><strong>
                                                <font color="#ff0000"><span style="font-size: 1.2rem;">REVIEWS FROM
                                                        SATISFIED PARTICIPANTS</span></font>
                                            </strong></h4>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="SABHy5FUSH"><img
                                        class="kartra_image kartra_image--full pull-left background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        id="1554046969986_formbutton"
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1; width: 940px; max-width: 100%; height: auto;"
                                        data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2796135_15449367239815-Star-Anim.gif">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 0px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/279540014"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="279540014"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div class="kartra_element_bg column--md-padding-small column--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: rgba(255,255,255,1);"
                                        class="background-item background-item--box-shadow-light-01"></div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--text-center kartra_headline--h6 kartra_headline--black kartra_headline--font-weight-regular kartra_headline--margin-bottom-medium"
                                            style="position: relative;">
                                            <p><em>"This course has helped me to dream bigger and focus on my goal! I'm
                                                    very encouraged ..."</em></p>
                                        </div>
                                    </div>
                                    <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                        data-component="bundle"
                                        style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                        <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                            class="background-item background_changer--blur0 js-bg-next-gen"
                                            data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3830058_1554050385158Sandra_VMota.jpg")'>
                                        </div>
                                    </div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                            style="position: relative;">
                                            <p>Sandra</p>
                                        </div>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                            style="position: relative;">
                                            <p><em>Student</em></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 0px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/318379283"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="318379283"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div class="kartra_element_bg column--md-padding-small column--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: rgba(255,255,255,1);"
                                        class="background-item background-item--box-shadow-light-01"></div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--text-center kartra_headline--h6 kartra_headline--black kartra_headline--font-weight-regular kartra_headline--margin-bottom-medium"
                                            style="position: relative;">
                                            <p><em>"I found help and motivation from this practical and student-oriented
                                                    course. It's worth the investment!"</em></p>
                                        </div>
                                    </div>
                                    <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                        data-component="bundle"
                                        style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                        <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                            class="background-item background_changer--blur0"
                                            data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/iconfinder_1554051333.png")'>
                                        </div>
                                    </div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                            style="position: relative;">
                                            <p>J.M.</p>
                                        </div>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                            style="position: relative;">
                                            <p><em>Student</em></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 0px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/279538069"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="279538069"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div class="kartra_element_bg column--md-padding-small column--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: rgba(255,255,255,1);"
                                        class="background-item background-item--box-shadow-light-01"></div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--text-center kartra_headline--h6 kartra_headline--black kartra_headline--font-weight-regular kartra_headline--margin-bottom-medium"
                                            style="position: relative;">
                                            <p><em>"It was very informative and practical. This is a life-changing
                                                    course!"</em></p>


                                        </div>
                                    </div>
                                    <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                        data-component="bundle"
                                        style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                        <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                            class="background-item background_changer--blur0 js-bg-next-gen"
                                            data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3829961_1554049178924Percy_Arroyo.jpg")'>
                                        </div>
                                    </div>
                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                            style="position: relative;">
                                            <p>Percy Arroyo</p>
                                        </div>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                            style="position: relative;">
                                            <p><em>Student</em></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" style="padding: 20px 0px; background-color: rgba(227, 227, 227, 0.19);"
                id="_qedjabpbf">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 0.3;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="o9WlcmIIMt">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                        <p style="font-size: 2.33rem; text-align: center; line-height: 1.2em;"><b><span
                                                    style="color: rgb(0, 0, 0); font-size: 2.33rem; line-height: 1.2em;"><span
                                                        style="line-height: 1.2em; font-size: 2.33rem; color: rgb(0, 0, 0);">COURSE
                                                        MODULES</span></span></b><b><span
                                                    style="font-size: 2.33rem; line-height: 1.2em; color: rgb(0, 0, 0);"><span
                                                        style="line-height: 1.2em; font-size: 2.33rem; color: rgb(0, 0, 0);"> &amp;
                                                        LESSONS</span></span></b></p>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="LKUVr3vYW5">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2766436_1544654794048download_1.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2766436_1544654794048download_1.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            id="1544923142819_formbutton"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto -20px; opacity: 1; width: 235px; max-width: 100%; height: auto;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2766436_1544654794048download_1.png">
                                    </picture>
                                </div>
                                <div data-component="headline" id="19JyXfFJcF">
                                    <div class="kartra_headline kartra_headline--font-weight-black kartra_headline--size-sm-giant kartra_headline--mat-black kartra_headline--text-center"
                                        style="position: relative;">
                                        <p style="font-size: 2rem; line-height: 1.2em;"><span
                                                style="line-height: 1.2em; font-size: 2rem; color: rgb(0, 128, 0);">Transformation
                                                To</span><span
                                                style="color: rgb(31, 47, 79); line-height: 1.2em; font-size: 2rem;">
                                            </span><span
                                                style="line-height: 1.2em; font-size: 2rem; color: rgb(255, 165, 0);">A
                                                Healthier You! </span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal background_changer--blur0" data-component="grid" id="CrlERgLcjn"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 30px; margin-bottom: 0px; background-image: none; opacity: 1;">
                        <div class="background_changer--blur0 col-md-4"
                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                            <div class="js_kartra_component_holder">
                                <div data-component="list" id="dIWzwpReTD">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--table"
                                            href="javascript: void(0);" id="8G8zVELUgM">
                                            <div class="kartra_icon kartra_icon--light-coral-two kartra_icon--negative-top-like-tiny kartra_icon--large"
                                                id="1544536093487_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(194, 174, 131);"
                                                    class="kartra_icon__icon fa fa-play-circle"></span>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                                <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-small"
                                                    style="position: relative;">
                                                    <p style="font-size: 1.8rem; line-height: 1.4em;"><b><span
                                                                style="color: rgb(31, 47, 79); font-size: 1.8rem; line-height: 1.4em;"><span
                                                                    style="font-size: 1.8rem; line-height: 1.4em; color: rgb(31, 47, 79);">12
                                                                    VIDEO LESSONS</span></span></b></p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--table"
                                            href="javascript: void(0);" id="LssklaqbhA">
                                            <div class="kartra_icon kartra_icon--light-coral-two kartra_icon--negative-top-like-tiny kartra_icon--large"
                                                id="1544536093487_formbutton"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(194, 174, 131);"
                                                    class="kartra_icon__icon fa fa-file-text-o"></span>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                                <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-small"
                                                    style="position: relative;">
                                                    <p style="font-size: 1.6rem; line-height: 1.4em;"><b><span
                                                                style="color: rgb(31, 47, 79); font-size: 1.6rem; line-height: 1.4em;"><span
                                                                    style="line-height: 1.4em; font-size: 1.6rem; color: rgb(31, 47, 79);">FULL-TIME
                                                                    ACCESS</span></span></b></p>
                                                </div>
                                            </div>
                                        </li>


                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4" id="9mca4M8V94">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="8htq3u7hvo_jSivufwMoc_JUH7sQKkpL_DzziMZCDvh"
                                    style="margin: 0px 0px 30px; padding: 25px 25px 10px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 8px; border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; background-image: none; opacity: 1;"
                                        class="background-item background-item--shadow-2 background_changer--blur0">
                                    </div>
                                    <div data-component="text" id="wjrBv5Dqoy">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;">
                                            <p style="text-align: center; font-size: 0.8rem;"><span
                                                    style="color: rgb(194, 174, 131); font-size: 0.8rem;">GET
                                                    STARTED</span></p>
                                        </div>
                                    </div>
                                    <div data-component="headline" id="jTocOfLEGZ">
                                        <div class="kartra_headline kartra_headline--h4"
                                            style="position: relative; margin: 0px 0px 15px;">
                                            <p style="text-align: center;">
                                                <font color="#02173e" face="roboto condensed"><b>GOALS</b></font>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center;">You can't hit a target you can see so lets
                                                establish desired outcomes and look beyond the impossible.</p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="FcijNrb2TO">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;"></div>
                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="nCekTaSeEy_30IUmhKDcn"
                                    style="margin: 0px 0px 30px; padding: 25px 25px 10px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 8px; border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; background-image: none; opacity: 1;"
                                        class="background-item background-item--shadow-2 background_changer--blur0">
                                    </div>
                                    <div data-component="text" id="wjrBv5Dqoy">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;">
                                            <p style="text-align: center; font-size: 0.8rem;"><span
                                                    style="color: rgb(194, 174, 131); font-size: 0.8rem;">GET
                                                    FUELED</span></p>
                                        </div>
                                    </div>
                                    <div data-component="headline" id="jTocOfLEGZ">
                                        <div class="kartra_headline kartra_headline--h4"
                                            style="position: relative; margin: 0px 0px 15px;">
                                            <p style="text-align: center;"><strong><span
                                                        style='color: rgb(2, 23, 62); font-family: "roboto condensed";'>NUTRITION</span></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="NCEWBfBDWc">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center;">Defining important nutrients and the health
                                                benefits of fruits, whole grains, nuts, vegetables and legumes.</p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="FcijNrb2TO">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal background_changer--blur0" data-component="grid"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin: 0px -15px; background-image: none; opacity: 1;"
                        id="m60gvZZ3IC">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">

                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="AR8DTGhu0M_FOtBqCRJzD"
                                    style="margin: 0px 0px 30px; padding: 25px 25px 10px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 8px; border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; background-image: none; opacity: 1;"
                                        class="background-item background-item--shadow-2 background_changer--blur0">
                                    </div>
                                    <div data-component="text" id="wjrBv5Dqoy">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;">
                                            <p style="text-align: center; font-size: 0.8rem;"><span
                                                    style="color: rgb(194, 174, 131); font-size: 0.8rem;">GET
                                                    GOING</span></p>
                                        </div>
                                    </div>
                                    <div data-component="headline" id="jTocOfLEGZ">
                                        <div class="kartra_headline kartra_headline--h4"
                                            style="position: relative; margin: 0px 0px 15px;">
                                            <p style="text-align: center;">
                                                <font color="#02173e" face="roboto condensed"><b>EXERCISE</b></font>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="QS4Yhnh2yK">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center;">The need for adequate and regular exercise
                                                and health dangers of inactivity. Life-changing!</p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="FcijNrb2TO">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="XJ0ZhOevvY_wrwHmNy5hB"
                                    style="margin: 0px 0px 30px; padding: 25px 25px 10px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 8px; border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; background-image: none; opacity: 1;"
                                        class="background-item background-item--shadow-2 background_changer--blur0">
                                    </div>
                                    <div data-component="text" id="wjrBv5Dqoy">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;">
                                            <p style="text-align: center; font-size: 0.8rem;">
                                                <font color="#c2ae83">GET SCREENED</font>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="headline" id="jTocOfLEGZ">
                                        <div class="kartra_headline kartra_headline--h4"
                                            style="position: relative; margin: 0px 0px 15px;">
                                            <p style="text-align: center;">
                                                <font color="#02173e" face="roboto condensed"><b>HEALTHCARE ACCESS</b>
                                                </font>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="cOc97mTrpF">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center;">Defining recommended healthcare screening
                                                tests, when these tests should be done. </p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="FcijNrb2TO">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">

                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="NJFanfBsFE_1deyUEh60T"
                                    style="margin: 0px 0px 30px; padding: 25px 25px 10px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 8px; border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; background-image: none; opacity: 1;"
                                        class="background-item background-item--shadow-2 background_changer--blur0">
                                    </div>
                                    <div data-component="text" id="wjrBv5Dqoy">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;">
                                            <p style="text-align: center; font-size: 0.8rem;">
                                                <font color="#c2ae83">GET IT DONE</font>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="headline" id="jTocOfLEGZ">
                                        <div class="kartra_headline kartra_headline--h4"
                                            style="position: relative; margin: 0px 0px 15px;">
                                            <p style="text-align: center;"><strong><span
                                                        style='color: rgb(2, 23, 62); font-family: "roboto condensed";'>THE HEALTHIER
                                                        YOU</span></strong></p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="9l04e3AtyO">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center;">Summary of diseases that are preventable with
                                                early detection. What to do to prevent and win.</p>
                                        </div>
                                    </div>
                                    <div data-component="text" id="FcijNrb2TO">
                                        <div class="kartra_text kartra_text--font-weight-regular"
                                            style="position: relative;"></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">
                                <div data-component="image">
                                    <img class="kartra_image pull-center kartra_image--full"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="text" id="am04oykvVC">
                                    <div class="kartra_text" style="position: relative;">
                                        <h2><span><span>What You Can Expect From This Course:</span></span></h2>

                                        <ul>
                                            <li><span><span style="line-height: 1.4em;"><span
                                                            style="line-height: 1.4em; font-size: 0.8rem;">Plan meals
                                                            that will promote weight loss and improve overall
                                                            health.</span></span></span></li>
                                            <li><span><span style="line-height: 1.4em;"><span
                                                            style="line-height: 1.4em; font-size: 0.8rem;">Structure
                                                            regular physical exercise to prevent (or improve) chronic
                                                            diseases.</span></span></span></li>
                                            <li><span><span style="line-height: 1.4em;"><span
                                                            style="line-height: 1.4em; font-size: 0.8rem;">Know exactly
                                                            what healthcare screening tests are relevant for your age
                                                            and family history.</span></span></span></li>
                                            <li><span><span style="line-height: 1.4em;"><span
                                                            style="line-height: 1.4em; font-size: 0.8rem;">If you do what
                                                            the Dr. recommends in this course, you will lose weight and
                                                            improve overall health and energy
                                                            levels.</span></span></span></li>
                                        </ul>
                                    </div>
                                </div>
                                <div data-component="button" style="width: auto;" id="lsX0UqnAJm">
                                    <button onclick="openPaymentSelection()" class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center"
                                        style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'>
                                        <span class="kartra_icon__icon fa fa-shopping-cart" style="color: rgb(255, 254, 253); border-color: rgb(255, 254, 253); font-weight: 400;"></span>ENROLL IN THIS COURSE NOW
                                    </button>
                                    
                                    <!-- Payment Selection Modal -->
                                    <div id="paymentSelection" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                            <button onclick="closePaymentSelection()"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                            
                                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
                                    
                                            <!-- Tab Navigation -->
                                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Credit/PayPal</button>
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Mobile Money</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_product dg_popup"
                                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                                    data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                                    data-kt-owner="DpwDQa6g" data-kt-value="c7d4071f65bc70e18cfb89c667e6d154"
                                                    data-funnel-id="154974" data-product-id="154974"
                                                    data-price-point="c7d4071f65bc70e18cfb89c667e6d154"
                                                    rel="c7d4071f65bc70e18cfb89c667e6d154" data-asset-id="58" target="_parent">
                                                    Pay with Card/Paypal
                                                </a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('2')"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay with Mobile Money</button>
                                            </div>
                                        </div>
                                    </div>     
                                    <!--Modal-->
                                    <div id="paymentModal" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: left; position: relative;">
                                            <h2 id="payment-header"
                                                style="text-align: center; font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">
                                                Complete Payment</h2>
    
                                            <!-- Step 1: Personal Information -->
                                            <div class="form-step active" id="step-1" style="display: block;">
                                                <label for="name" style="font-weight: bold; color: #555;">Full Name:</label>
                                                <input type="text" id="name" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
    
                                                <label for="mail" style="font-weight: bold; color: #555;">Email:</label>
                                                <input type="email" id="mail" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
    
                                                <label for="phone" style="font-weight: bold; color: #555;">Phone
                                                    Number:</label>
                                                <input type="text" id="phone" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                                    
                                                <p style="colour: red;">This course costs USD<span id="course_price"></span><br/>You'll be charged in your local currency.</p>    
                                                <button onclick="nextStep(2)"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                            </div>
    
                                            <!-- Step 2: Billing Details -->
                                            <div class="form-step" id="step-2" style="display: none;">
                                                <label for="billing-address" style="font-weight: bold; color: #555;">Billing
                                                    Address:</label>
                                                <input type="text" id="billing-address" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
    
                                                <label for="city" style="font-weight: bold; color: #555;">City:</label>
                                                <input type="text" id="city" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
    
                                                <label for="zip" style="font-weight: bold; color: #555;">Zip Code:</label>
                                                <input type="text" id="zip" required
                                                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
    
                                                <button onclick="nextStep(3)"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                                <button onclick="nextStep(1)"
                                                    style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                            </div>
    
                                            <!-- Step 3: Payment Details -->
                                            <div class="form-step" id="step-3" style="display: none;">
                                                <h3 style="text-align: center; margin-bottom: 20px;">Payment Amount: $<span
                                                        id="price">0</span></h3>
                                                <button onclick="payWithPaystack(event)"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay
                                                    Now</button>
                                                <button onclick="nextStep(2)"
                                                    style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                            </div>
    
                                            <button onclick="document.getElementById('paymentModal').style.display='none';"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script type="text/javascript" async="" class="everWebinarScript"
                src="//events.genndi.com/register.evergreen.extra.js"></script>
            <div class="content content--padding-extra-large"
                style="padding: 30px 0px 10px; background-color: rgb(49, 85, 40);" id="_1etxtiyvq">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">

                                <!--Text Block -->


                                <!-- Heading-->

                                <!--Text Block -->

                                <!--Divider -->

                                <!-- List-->

                                <!-- Buttons-->
                                <div class="inline_elements_wrapper" style="justify-content: center;">


                                </div>
                                <div data-component="headline" id="QBow2NpkIe">
                                    <div class="kartra_headline kartra_headline--letter-spacing-extra-tiny kartra_headline--orange-tomato kartra_headline--text-center kartra_headline--h5 kartra_headline--font-weight-medium kartra_headline--montserrat-font"
                                        style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                        <p style="font-size: 2.33rem;"><strong><span style="font-size: 2.33rem;"><span
                                                        style="font-size: 2rem; color: rgb(255, 255, 255); font-family: roboto;">MEET
                                                        YOUR </span><span
                                                        style="font-size: 2rem; font-family: roboto; color: rgb(255, 165, 0);">INSTRUCTOR</span></span></strong>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="text" id="OdnPJ3VuRO">
                                    <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>
                                            <font color="#ffffff"><span style="font-size: 20.425px;"><i>Physician,
                                                        author, speaker, humanitarian, wife, mother, and TV
                                                        producer.</i></span></font>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-extra-large"
                style="background-color: rgb(255, 255, 255); padding: 40px 0px 20px;" id="_sysp3czxc">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-4 column--vertical-center background_changer--blur0"
                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                            <div class="js_kartra_component_holder">
                                <div data-component="image" href="javascript: void(0);" id="o3unMi35Kf">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/809341939333Dr.Cooper_Headshot.png">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/809341939333Dr.Cooper_Headshot.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 294px; max-width: 100%; height: auto;"
                                            id="1544923456889_formbutton"
                                            data-original=https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/809341939333Dr.Cooper_Headshot.png">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 column--vertical-center background_changer--blur0"
                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 10px; background-image: none; opacity: 1;">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline" id="CyrQ38iDS9">
                                    <div class="kartra_headline kartra_headline--letter-spacing-extra-tiny kartra_headline--orange-tomato kartra_headline--text-center kartra_headline--h5 kartra_headline--font-weight-medium kartra_headline--montserrat-font"
                                        style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                        <p style="font-size: 1.6rem;"><strong><span
                                                    style="color: rgb(0, 100, 0); font-size: 1.6rem;"><span
                                                        style="font-family: roboto; font-size: 1.6rem; color: rgb(0, 100, 0);">Dr.
                                                        Dona </span></span></strong>
                                            <font face="roboto"><span style="font-size: 1.6rem;"><span
                                                        style="font-size: 1.6rem; color: rgb(255, 140, 0);">Cooper-Dockery,</span><span
                                                        style="color: rgb(194, 174, 131); font-size: 1.6rem;">
                                                    </span></span></font><strong><span style="font-size: 1.6rem;"><span
                                                        style="font-family: roboto; font-size: 1.6rem; color: rgb(0, 100, 0);">MD.</span></span></strong>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="text" id="8Ao2Xi1axy">
                                    <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                        style="position: relative; margin-top: 0px; margin-bottom: 10px;">
                                        <p style="text-align: justify;"><span style="color:#696969;">Is <span
                                                    style="color: rgb(105, 105, 105);"><span
                                                        style="color: rgb(105, 105, 105);">an accomplished</span></span>
                                                board-certified physician, #1 bestselling author, <i><span
                                                        style="color: rgb(105, 105, 105);"><span
                                                            style="color: rgb(105, 105, 105);">speaker, humanitarian,
                                                            and philanthropist </span></span></i>who has dedicated over
                                                27 years to positively changing healthcare outcomes both nationally and
                                                internationally. She holds active memberships in the <em><span
                                                        style="color: rgb(105, 105, 105);"><span
                                                            style="color: rgb(105, 105, 105);">American Academy of
                                                            Lifestyle Medicine</span></span></em> and the <em><span
                                                        style="color: rgb(105, 105, 105);">American Medical
                                                        Association</span></em>.</span></p>

                                        <p style="text-align: justify;"> </p>

                                        <p style="text-align: justify;"><span style="color:#696969;">She's
                                            </span>actively engaged in various communities giving healthy lifestyle
                                            seminars and free medical care not only in the USA but also in countries
                                            such as Haiti, Jamaica, Ghana, the Philippines, and Europe. Some of her
                                            intellectual works include <span style="color:#696969;"><em>Get Healthy for
                                                    Life, Fourteen Days to Amazing Health, </em><em><span
                                                        style="color: rgb(105, 105, 105);"><span
                                                            style="color: rgb(105, 105, 105);">My Health and The
                                                            Creator</span></span></em>, <em>Incredibly Delicious Vegan
                                                    Recipes,</em> <i><span style="color: rgb(105, 105, 105);">and
                                                        several manuals and courses and a quarterly
                                                        magazine. </span></i></span></p>

                                        <p style="text-align: justify;"> </p>

                                        <p style="text-align: justify;"><span style="color:#696969;">Dr. Cooper-Dockery
                                                is also the founder and director of <em><span
                                                        style="color: rgb(105, 105, 105);"><span
                                                            style="color: rgb(105, 105, 105);">Cooper Internal Medicine
                                                            and the Cooper Wellness and Disease Prevention
                                                            Center</span></span></em> where m</span>any of her patients
                                            are enjoying more health with less medication. Some have even gotten off
                                            medication entirely! She also <span style="color:#696969;">hosts the popular
                                                TV show, <em><span style="color: rgb(105, 105, 105);"><span
                                                            style="color: rgb(105, 105, 105);">Get Healthy with Dr.
                                                            Cooper</span></span></em>, which airs bi-weekly locally and
                                                on Fox, and <span style="color: rgb(105, 105, 105);">can be seen in
                                                    40 million homes across America. </span></span></p>
                                    </div>
                                </div>
                                <div data-component="text" id="hXglQBuYna">
                                    <div class="kartra_text" style="position: relative;"></div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="T6xeVWpYNQ">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-left background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            id="1553795522346_formbutton"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                            data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                    </picture>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row background_changer--blur0" data-component="grid" id="VSSxBqRaVi"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 20px; margin-bottom: 30px; background-image: none; opacity: 1;">
                        <div class="col-md-10 col-md-offset-1" id="fzJQLUlpXy">
                            <div class="js_kartra_component_holder">

                                <!--Text Block -->


                                <!-- Heading-->

                                <!--Text Block -->

                                <!--Divider -->

                                <!-- List-->

                                <!-- Buttons-->
                                <div class="inline_elements_wrapper" style="justify-content: center;">


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/311989473"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="311989473"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/305106445"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="305106445"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div data-component="image">
                                    <img class="kartra_image pull-center kartra_image--full"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div data-component="video" id="3i0GnFAi1q">
                                    <div class="kartra_video kartra_video--player_2"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/305107066"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="305107066"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-medium content--padding-bottom-none content--padding-top-none"
                style="background-color: #ffffff;" id="_mozjubfm5">
                <div class="background_changer"></div>
                <div class="background_changer_overlay"></div>
                <div>
                    <div>
                        <div class="row row--margin-left-right-none" data-component="grid">
                            <div class="col-md-12 column--padding-none">
                                <div class="js_kartra_component_holder">
                                    <div data-component="button" style="width: auto;" id="28oDoIYvu4">
                                        <button onclick="openPaymentSelection()"
                                            class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center"
                                            style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: 'Roboto Condensed'; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                            <span class="kartra_icon__icon fa fa-shopping-cart" style="color: rgb(255, 254, 253); border-color: rgb(255, 254, 253); font-weight: 400;"></span>TAKE
                                            THE COURSE NOW
                                        </button>
                                    
                                    <!-- Payment Selection Modal -->
                                    <div id="paymentSelection" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                            <button onclick="closePaymentSelection()"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                            
                                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
                                    
                                            <!-- Tab Navigation -->
                                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Credit/PayPal</button>
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Mobile Money</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_product dg_popup"
                                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                                    data-frame-id="_mozjubfm5" data-effect="kartra_css_effect_6"
                                                    data-kt-layout="1" data-kt-type="checkout" data-kt-owner="DpwDQa6g"
                                                    data-kt-value="c7d4071f65bc70e18cfb89c667e6d154" data-funnel-id="154974"
                                                    data-product-id="154974" data-price-point="c7d4071f65bc70e18cfb89c667e6d154"
                                                    rel="c7d4071f65bc70e18cfb89c667e6d154" data-asset-id="66"
                                                    target="_parent"><span class="kartra_icon__icon fa fa-shopping-cart"
                                                        style="color: rgb(255, 254, 253); border-color: rgb(255, 254, 253); font-weight: 400;"></span>Pay with Card/Paypal</a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('2')"
                                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay with Mobile Money</button>
                                            </div>
                                        </div>
                                    </div>     
                                <!--Modal-->
                                <div id="paymentModal" class="modal"
                                    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                    <div class="modal-content"
                                        style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: left; position: relative;">
                                        <h2 id="payment-header"
                                            style="text-align: center; font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">
                                            Complete Payment</h2>

                                        <!-- Step 1: Personal Information -->
                                        <div class="form-step active" id="step-1" style="display: block;">
                                            <label for="name" style="font-weight: bold; color: #555;">Full Name:</label>
                                            <input type="text" id="name" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="mail" style="font-weight: bold; color: #555;">Email:</label>
                                            <input type="email" id="mail" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="phone" style="font-weight: bold; color: #555;">Phone
                                                Number:</label>
                                            <input type="text" id="phone" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                                
                                            <p style="colour: red;">This course costs USD<span id="course_price"></span><br/>You'll be charged in your local currency.</p>    
                                            <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                        </div>

                                        <!-- Step 2: Billing Details -->
                                        <div class="form-step" id="step-2" style="display: none;">
                                            <label for="billing-address" style="font-weight: bold; color: #555;">Billing
                                                Address:</label>
                                            <input type="text" id="billing-address" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="city" style="font-weight: bold; color: #555;">City:</label>
                                            <input type="text" id="city" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="zip" style="font-weight: bold; color: #555;">Zip Code:</label>
                                            <input type="text" id="zip" required
                                                style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <button onclick="nextStep(3)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                            <button onclick="nextStep(1)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <!-- Step 3: Payment Details -->
                                        <div class="form-step" id="step-3" style="display: none;">
                                            <h3 style="text-align: center; margin-bottom: 20px;">Payment Amount: $<span
                                                    id="price">0</span></h3>
                                            <button onclick="payWithPaystack(event)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay
                                                Now</button>
                                            <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <button onclick="document.getElementById('paymentModal').style.display='none';"
                                            style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                </div>
                                    </div>
                                    <div data-component="text" id="orDxtrhmW5">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="font-size: 0.65rem; text-align: center;"><strong><span
                                                        style="font-size: 0.65rem;"><span
                                                            style="color: rgb(178, 34, 34); font-size: 0.65rem;">Only</span>
                                                        <s><span style="font-size: 0.65rem;"><span
                                                                    style="font-size: 0.65rem;">$97</span></span></s> <span
                                                            style="color: rgb(178, 34, 34); font-size: 0.65rem;">$59!</span></span></strong>
                                            </p>
                                        </div>
                                    </div>
                                    <div data-component="image" id="xgDM5GxoXO">
                                        <img class="kartra_image pull-center kartra_image--full"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text" style="position: relative;">
                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"> </p>

                                            <p style="text-align: center; font-size: 0.5rem;"><em><span
                                                        style="font-size: 0.5rem;"><span
                                                            style="font-size: 0.5rem;">Every individual is different so
                                                            no one can predict your result nor can we guarantee any
                                                            results whatsoever. The testimonials herein presented should
                                                            not be construed as "typical." As with all information
                                                            regarding health, be sure to research and review carefully.
                                                            You should consult your professional health care provider
                                                            before implementing any exercise or dietary program. The
                                                            info presented on this page is for educational purposes only
                                                            and not intended to treat or diagnose any disease, nor
                                                            should it substitute medical advice offered by your
                                                            physician or other licensed healthcare provider. Use at your
                                                            own risk.</span></span></em></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="container">
                            <div class="row row--equal background_changer--blur0" data-component="grid"
                                style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 10px; background-image: none; opacity: 1;">




                            </div>
                        </div>
                    </div>
                    <div class="kartra_element_bg kartra_element_bg--padding-top-special-medium kartra_element_bg--padding-bottom-tiny"
                        data-component="bundle" style="margin-top: 0px; margin-bottom: 0px; padding: 30px 0px 10px;">
                        <div style="background-color: rgb(49, 85, 40); border-radius: 0px; border-color: rgb(250, 250, 250); border-style: none; border-width: 0px; background-image: none; opacity: 1;"
                            class="background-item background_changer--blur0"></div>
                        <div class="container">
                            <div class="row row--equal" data-component="grid">
                                <div class="column--vertical-center col-md-6">
                                    <div class="js_kartra_component_holder js_kartra_component_holder--height-auto">

                                        <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--align-right kartra_link_wrapper--sm-align-center kartra_link_wrapper--margin-bottom-big-tiny pull-left"
                                            data-component="bundle" id="Zek7peHwwQ_J7FTJoomB0"
                                            style="margin: 0px 0px 15px;">
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                                href="javascript: void(0);" data-frame-id="_mozjubfm5"
                                                style='color: rgb(251, 250, 248); font-weight: 400; font-family: "Open Sans";'
                                                target="_parent">DISCLAIMERS</a>
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                                href="javascript: void(0);" data-frame-id="_mozjubfm5"
                                                style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                                target="_parent">TERMS</a>
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                                href="javascript: void(0);" data-frame-id="_mozjubfm5"
                                                style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                                target="_parent">DCMA NOTICE</a><a
                                                class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                                href="javascript: void(0);" data-frame-id="_mozjubfm5"
                                                style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                                target="_parent">PRIVACY POLICY</a>

                                        </div>
                                    </div>
                                </div>
                                <div class="column--vertical-center col-md-6">
                                    <div class="js_kartra_component_holder js_kartra_component_holder--height-auto">

                                        <div data-component="text">
                                            <div class="kartra_text kartra_text--open-sans-font kartra_text--font-weight-regular kartra_text--dim-black kartra_text--text-right kartra_text--sm-text-center"
                                                style="position: relative;">
                                                <p style="font-size: 16px; font-family: Georgia;"><span
                                                        style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;">Copyright
                                                        © 2020 by <b><span
                                                                style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;"><span
                                                                    style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;">Cooper
                                                                    Wellness Center </span></span></b>| All Rights
                                                        Reserved.</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script async defer src="https://app.kartra.com/resources/js/popup"></script>
        <script src="js/build/front/pages/jquery.lwtCountdown-1.0.js"></script>
        <script async defer src="js/build/front/pages/countdown.js"></script>
    </div>
    <!-- /#page -->
    <div style="height:0px;width:0px;opacity:0;position:fixed">
        <script>!function(){function e() { var e = ((new Date).getTime(), document.createElement("script")); e.type = "text/javascript", e.async = !0, e.setAttribute("embed-id", "e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf"), e.src = "https://embed.adabundle.com/embed-scripts/e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf"; var t = document.getElementsByTagName("script")[0]; t.parentNode.insertBefore(e, t) }var t=window;t.attachEvent?t.attachEvent("onload",e):t.addEventListener("load",e,!1)}();</script>
    </div>
    <div style="height:0px;width:0px;opacity:0;position:fixed">
        <!-- Meta Pixel Code -->
        <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod ?
        n.callMethod.apply(n, arguments) : n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1445107009514995');
    fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=1445107009514995&ev=PageView&noscript=1" /></noscript>
        <!-- End Meta Pixel Code -->

        <!-- Facebook Pixel Code -->
        <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod ?
        n.callMethod.apply(n, arguments) : n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '307347596535190');
    fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=307347596535190&ev=PageView&noscript=1" /></noscript>
        <!-- End Facebook Pixel Code -->

        <!-- Facebook Pixel Code -->
        <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod ?
        n.callMethod.apply(n, arguments) : n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1163530910472602');
    fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=1163530910472602&ev=PageView&noscript=1" /></noscript>
        <!-- End Facebook Pixel Code -->

    </div>
    <!-- Load JS here for greater good =============================-->
    <script src="js/build/front/pages/skeleton-immediate.js"></script>
    <script src="js/build/front/pages/skeleton-below.js" async defer></script>
    <script src="//app.kartra.com/resources/js/analytics/DpwDQa6g" async defer></script>
    <script src="//app.kartra.com/resources/js/page_check?page_id=fCAxF8e3UBYf" async defer></script>
    <script>
    if ( typeof window['jQuery'] !== 'undefined') {
        window.jsVars = { "page_title": "Transformation to a Healthier You Course", "page_description": "Lose Weight, Improve Health, and Energy Level with Dr. Dona Cooper-Dockery", "page_keywords": "14 days to amazing health, Dr. dona cooper, Dona Cooper-Dockery, get healthy with Dr. Cooper, Cooper wellness center, cooper internal medicine, transformation to a healthier you, cooper wellness center", "page_robots": "index, follow", "secure_base_url": "\/\/app.kartra.com\/", "global_id": "fCAxF8e3UBYf" };
    window.global_id = 'fCAxF8e3UBYf';
    window.secure_base_url = '//app.kartra.com/';
    if( typeof Porthole !== 'undefined' ) {
        windowProxy = new Porthole.WindowProxy('//app.kartra.com/front/deal/proxy');
        }
    }
    </script>
    <footer>
        <div style="height:0px;width:0px;opacity:0;position:fixed">
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-217187331-1"></script>
            <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-217187331-1');
            </script>


        </div>
    </footer>
    <!-- GDPR cookie BANNER -->
    <!-- GDPR cookie BANNER -->
    <div class="gdpr_flapjack_banner js_gdpr_flapjack_banner lang-var-{language_code}" style="display: none;">
        <button type="button" class="gdpr-uncollapse-button js_show_gdpr_banner">
            {:lang_general_banner_cookies}
        </button>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="grid-gdpr-banner">
                        <div>
                            <div class="js_gdrp_cookie_banner_text gdpr-text">
                                <div>
                                    <div>
                                        {:lang_general_banner_cookie_disclaimer}
                                    </div>
                                    <div class="gdpr_link_wrapper">
                                        <a href="" target="_blank" class="js_gdpr_button">
                                            {:lang_general_banner_cookie_privacy}
                                        </a>
                                        <span></span>
                                        <a href="" target="_blank" class="">
                                            {:lang_general_banner_cookie_cookie}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="gdpr_button_block">
                            <div class="gdpr_toggler">
                                <label class="toggler_label selected">
                                    {:lang_general_banner_cookie_accept_all}
                                </label>
                                <div class="switcher">
                                    <input type="checkbox" name="gdpr_cookies" id="gdpr_cookies"
                                        class="cmn-toggle js_accepted_cookies" value="2">
                                    <label for="gdpr_cookies"></label>
                                </div>
                                <label class="toggler_label ">
                                    {:lang_general_banner_cookie_only_essential}
                                </label>
                            </div>
                            <div>
                                <button class="gdpr_close js_gdpr_close" type="button" data-type="kartra_page"
                                    data-type-id="198" data-type-owner="DpwDQa6g">
                                    {:lang_general_save}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--// GDPR cookie BANNER -->

    <script src="//app.kartra.com/resources/js/kartra_embed_wild_card?type=kartra_page&amp;owner=DpwDQa6g"></script>

</body>

</html>