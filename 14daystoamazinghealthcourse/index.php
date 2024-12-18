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
    <title>14 Days to Amazing Health Course</title>
    <meta name="description"
        content="Let Dr. Dona Cooper help you lose weight, lower cholesterol, and reduce blood pressure and meds in ONLY 14 Days!">
    <meta name="keywords"
        content="get healthy for life, 14 days to amazing health, Dr. dona cooper, Dona Cooper-Dockery, get healthy with Dr. Cooper, Cooper wellness center, cooper internal medicine">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="14 Days to Amazing Health Course">
    <meta property="og:description" content="">
    <meta property="og:image"
        content="https://d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3797007_155374820656314DTAH-combine-1st024x726.png">

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
        let course_name;
        function openPaymentPopup(courseID) {
            const formData = new FormData();
            formData.append('courseID', courseID);
            console.log("courseid", courseID);
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
                    console.log("CourseName:", course_name)
                    document.getElementById('paymentModal').style.display = 'flex';
                } else {
                    console.error("Server responded with an error:", data);
                    alert(`Error: ${data.message}`);
                    window.location.href = "../acc/auth/login.php";
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
        let course_purchased;
        async function payWithPaystack(e) {
            e.preventDefault();
            const email = document.getElementById("mail").value;
            const phone = document.getElementById("phone").value;
            const course_no = courseNO;
            const price_ghs = ghs_price;
            const course_purchased = course_name
            console.log("Transaction amount:", price_ghs);
            console.log("Course name:", course_name);
            console.log("Course purchased:", course_purchased);
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
        href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
    </noscript>

    <script>
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        (function (w) { "use strict"; if (!w.loadCSS) { w.loadCSS = function () { } } var rp = loadCSS.relpreload = {}; rp.support = function () { var ret; try { ret = w.document.createElement("link").relList.supports("preload") } catch (e) { ret = false } return function () { return ret } }(); rp.bindMediaToggle = function (link) { var finalMedia = link.media || "all"; function enableStylesheet() { link.media = finalMedia } if (link.addEventListener) { link.addEventListener("load", enableStylesheet) } else if (link.attachEvent) { link.attachEvent("onload", enableStylesheet) } setTimeout(function () { link.rel = "stylesheet"; link.media = "only x" }); setTimeout(enableStylesheet, 3e3) }; rp.poly = function () { if (rp.support()) { return } var links = w.document.getElementsByTagName("link"); for (var i = 0; i < links.length; i++) { var link = links[i]; if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) { link.setAttribute("data-loadcss", true); rp.bindMediaToggle(link) } } }; if (!rp.support()) { rp.poly(); var run = w.setInterval(rp.poly, 500); if (w.addEventListener) { w.addEventListener("load", function () { rp.poly(); w.clearInterval(run) }) } else if (w.attachEvent) { w.attachEvent("onload", function () { rp.poly(); w.clearInterval(run) }) } } if (typeof exports !== "undefined") { exports.loadCSS = loadCSS } else { w.loadCSS = loadCSS } })(typeof global !== "undefined" ? global : this);

        window.global_id = 'YDxmyuNWgBZa';
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


    </script>

    <script src="js/build/front/pages/skeleton-above.js"></script>
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
        data-kt-type="kartra_page_tracking" data-kt-value="YDxmyuNWgBZa" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content" style="padding: 40px 0px; background-color: rgba(0, 0, 0, 0);" id="_0m3wi8oyv">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-10 col-md-offset-1">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="6z9O8thecK">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 20px; margin-bottom: 20px;">
                                        <p style="font-size: 1.2rem; text-align: center; line-height: 1.2em;">
                                            <strong><span style="font-size: 1.2rem; line-height: 1.2em;"><span
                                                        style="font-size: 1.2rem; line-height: 1.2em; color: rgb(255, 0, 0);">27-Year
                                                        Veteran Physician Reveals How to Lose up to 7 lbs, Improve Blood
                                                        Pressure, Reduce Cholesterol and Meds ...</span></span></strong>
                                        </p>

                                        <p style="font-size: 1.2rem; text-align: center; line-height: 1.2em;">
                                            <strong><span
                                                    style="color: rgb(0, 0, 0); font-size: 1.2rem; line-height: 1.2em;"><em><span
                                                            style="font-size: 1.2rem; line-height: 1.2em; color: rgb(0, 0, 0);"><span
                                                                style="font-size: 1.2rem; line-height: 1.2em; color: rgb(0, 0, 0);">in
                                                                Just 14 Days!</span></span></em></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="video"
                                    data-thumbnail="https://d11n7da8rpqbjy.cloudfront.net/strategicsecrets/generated-kartra-video-thumb-2547971_1542617601687Tumeric_for_Your_Health.mp4.jpg"
                                    data-screenshot="false" id="LAPmZbxOcA">
                                    <div class="kartra_video kartra_video--player_1"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%"
                                            src="https://app.kartra.com/external_video/youtube/TbUmVHmjDTQ?autoplay=true"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="youtube" data-video="TbUmVHmjDTQ?autoplay=true"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);">
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
                                <div data-component="image">
                                    <img class="kartra_image pull-center kartra_image--full"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div style="width: auto; display: flex; justify-content: center; gap: 10px;">
                                    <button onclick="openPaymentSelection()"
                                        class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center"
                                        data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                        data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                        data-funnel-id="154973" data-product-id="154973"
                                        data-price-point="9d647b7641994f7cf4e2527587235214"
                                        rel="9d647b7641994f7cf4e2527587235214" data-asset-id="47"
                                        target="_parent">
                                        <span class="fa fa-arrow-right" style="color: rgb(255, 255, 255); font-weight: 700;"></span>START THIS COURSE TODAY
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
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; color: white; width: 50%;">Pay with Mobile Money</button>
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
                                            </div>
                                            
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center toggle_product dg_popup pull-center"
                                                    data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                                    data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                                    data-funnel-id="154973" data-product-id="154973"
                                                    data-price-point="9d647b7641994f7cf4e2527587235214"
                                                    rel="9d647b7641994f7cf4e2527587235214" data-asset-id="47"
                                                    target="_parent">
                                                    <span class="fa fa-arrow-right" style="color: rgb(255, 255, 255); font-weight: 700;"></span>Pay with Credit/PayPal
                                                </a>
                                            </div>

                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('1')"
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
                                       
                                </div>
        
                                <div data-component="divider" id="ssN85Alqhw">
                                    <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 pull-center kartra_divider--full"
                                        style="border-color: rgba(33, 33, 33, 0.25); border-top-style: solid; border-top-width: 1px; margin: 0px auto;">
                                </div>
                                <div data-component="text" id="9BOxeoJaWl">
                                    <div class="kartra_text" style="position: relative; margin-top: 20px; margin-bottom: 20px;">
                                        <p style="font-size: 1.2rem; text-align: center; line-height: 1.2em;"><strong><span
                                                    style="font-size: 1.2rem; line-height: 1.2em;"><span
                                                        style="line-height: 1.2em; font-family: montserrat; font-size: 1.2rem;">Are
                                                        you still struggling to lose weight, reduce blood pressure, lower
                                                        cholesterol, and get off meds?</span></span></strong></p>
                                    </div>
                                </div>
                    </div>
                </div>
            </div>
            <div class="row" data-component="grid" id="GTaUHWMADH">
                <div class="col-md-10 col-md-offset-1">
                    <div class="js_kartra_component_holder">
                        <div data-component="text" id="l8TEEGrktJ">
                            <div class="kartra_text" style="position: relative;">
                                <p style="text-align: center; font-size: 0.8rem;"><strong><span
                                            style="color: rgb(255, 0, 0); font-size: 0.8rem;"><span
                                                style="font-size: 0.8rem; color: rgb(255, 0, 0);">If your're sick and
                                                tired of the merry-go-round, then read carefully. This simple but
                                                extremely effective system is finally the breakthrough you've been
                                                longing for…</span></span></strong></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><em><span style="font-size: 0.8rem;"><strong><span
                                                    style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">"Hi
                                                        there!</span></span></strong></span></em></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">My name is Dr. Dona
                                        Cooper-Dockery, a physician with over 27 years of experience and patient care at
                                        my companies,<em><span style="font-size: 0.8rem;"><span
                                                    style="font-size: 0.8rem;"> Cooper Internal
                                                    Medicine</span></span></em> and <em><span
                                                style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">Cooper
                                                    Wellness Center</span></span></em>. <strong><span
                                                style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">I know what
                                                    you’re going through.</span></span></strong> </span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><em><span style="font-size: 0.8rem;"><span
                                                style="font-size: 0.8rem;">Believe me… </span></span></em></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">Trying to lose weight,
                                        reduce blood pressure, and lower cholesterol isn’t nearly as easy as they make
                                        it seem! </span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><strong><span style="font-size: 0.8rem;"><span
                                                style="font-size: 0.8rem;">Tell Me… Does Any of This Sound
                                                Familiar?</span></span></strong></p>

                                <ul>
                                    <li><span><span><span style="font-size:0.80rem;">You've tried this diet plan and
                                                    that program without success just to feel left hopeless and let
                                                    down.</span></span></span></li>
                                    <br>
                                    <li><span><span><span style="font-size:0.80rem;">You've gone from doctor to doctor
                                                    but your blood work shows no improvement which always leads
                                                    to discouragement and maybe, even depression.</span></span></span>
                                    </li>
                                    <br>
                                    <li><span><span><span style="font-size:0.80rem;">You've spent a lot of time, money,
                                                    and energy on hype that over promised and under delivered  which
                                                    just ended up another quick failure as you struggle on to try to get
                                                    in shape or take charge of your health.</span></span></span></li>
                                </ul>

                                <p style="font-size: 0.8rem;">Yet in all your sincerity, all of those so-called,
                                    '<em><span style="font-size: 0.8rem;"><span style="font-size:0.80rem;"><span
                                                    style="font-size: 0.8rem;">solutions' </span>just left you feeling
                                                more frustrated than ever!"</span></span></em></p>
                            </div>
                        </div>
                        <div data-component="text" id="vYCMWZgFMp">
                            <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                style="position: relative; margin-top: 0px; margin-bottom: 30px;">
                                <h4><span style="line-height: 1.2em;"><strong><span style="line-height: 1.2em;"><span
                                                    style="line-height: 1.2em; font-size: 1.6rem; color: rgb(0, 0, 0);">BUT... </span><span
                                                    style="background-color: rgb(255, 255, 0); line-height: 1.2em; font-size: 1.6rem; color: rgb(0, 0, 0);">All </span><span
                                                    style="background-color: rgb(255, 255, 0); line-height: 1.2em; font-size: 1.6rem; color: rgb(0, 0, 0);">HOPE
                                                    is not Lost! </span></span></strong></span></h4>
                            </div>
                        </div>
                        <div data-component="text" id="l8TEEGrktJ">
                            <div class="kartra_text" style="position: relative;">
                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">'Tis true that I've
                                        achieve quite a bit of success…</span></p>

                                <blockquote><span><span style="font-size:0.80rem;">I'm able to help my patients reduce
                                            medications and some get off them all together.<br>
                                            <br>
                                            I can show you how many of them also effectively lose weight and feel
                                            great.<br>
                                            <br>
                                            I'm extremely blessed that I can show patients who were once diabetic how to
                                            have normal blood sugar without meds .</span></span></blockquote>

                                <p style="font-size: 0.8rem;"><br>
                                    <strong><span style="font-size: 0.8rem;">But to be completely
                                            honest…</span></strong>
                                </p>
                            </div>
                        </div>
                        <div data-component="text" id="ApJTHvO3Qy">
                            <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                style="position: relative; margin-top: 0px; margin-bottom: 30px;">
                                <h4><strong><span><span style="font-size: 1.2rem; color: rgb(0, 0, 0);">Things
                                                Definitely Didn't Start Out That Way For Me...</span></span></strong>
                                </h4>
                            </div>
                        </div>
                        <div data-component="text" id="GkENpfj6mI">
                            <div class="kartra_text" style="position: relative;">
                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">Before I ever got to
                                        taste the tiniest glimpse of helping patients to enjoy amazing health, I hit a
                                        lot of roadblocks and obstacles along the way. It was always tough seeing family
                                        and friends suffer unnecessarily and other who just wanted to throw in the towel
                                        because they were just plain tired of let downs.<br>
                                        <br>
                                        Even doctors can sometimes feel like they don't know ANYTHING! At least in terms
                                        of the simple things I now know that truly make a difference in lifestyle and
                                        health outcomes, that it's not about pills and text book solutions, and that
                                        healthy nutrition and regular physical exercise work wonders and miracles. <br>
                                        <br>
                                        <strong><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">So if
                                                    nothing ever seem to work out for you</span></span></strong> and
                                        you're at the point of, for lack of a better expression, feeling like a complete
                                        failure, hang with me for a bit.<br>
                                        <br>
                                        I've seen so many well meaning people who just never seem to be able to lose
                                        weight. <em><span style="font-size: 0.8rem;"><span
                                                    style="font-size: 0.8rem;">The thought of having to be
                                                    fat</span></span></em> is constantly on many people's mind.</span>
                                </p>

                                <ul>
                                </ul>

                                <h4 style="text-align: center;"><strong><span><span style="font-size: 0.8rem;">Before
                                                You Entertain Another Thought of Throwing In The Towel, Let Me Tell You
                                                How Today Can Change Everything.</span></span></strong></h4>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">You can only try so many
                                        things, hit so many walls and experience so many failures before the only option
                                        left seems to be quitting.</span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">Before too many of my
                                        precious clients reached a breaking point, I'd finally stumbled on this simple
                                        and effective plan to amazing health in just days.</span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">As soon as I discovered
                                        this little secret, everything instantly began to change.</span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">Before I knew it, they
                                        were reporting that the weight began to peel off and the pounds began to melt
                                        away.</span></p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">New life was literally
                                        emerging from under all the unwanted fat and slowly, but surely, they began
                                        finding their new selves!</span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">I was excited and
                                        completely blown away. All I had to do was teach them a few simple easy steps
                                        and their bodies began to transform on autopilot.</span></p>

                                <p style="font-size: 0.8rem;"> </p>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">I knew I was onto
                                        something BIG, but more importantly…</span></p>

                                <h2 style="text-align: center;"><strong><span><span>I knew I couldn't keep this to
                                                myself.</span></span></strong></h2>

                                <p style="font-size: 0.8rem;"><span style="font-size:0.80rem;">Since then, I've helped
                                        multiple others who had the same, never-ending battle of the bulge and fake
                                        programs. And it seemed pretty unfair for me to keep this life-changing secret
                                        to myself… Especially since it's been such a huge and integral step in many
                                        transformational experiences.</span></p>

                                <h4><span><strong><span style="font-size: 0.8rem;">So, today… I'd like to let you in on
                                                my little <em><span
                                                        style="font-size: 0.8rem;">"secret."</span></em></span></strong></span>
                                </h4>
                            </div>
                        </div>
                        <div data-component="headline">
                            <div class="kartra_headline kartra_headline--font-weight-bold kartra_headline--h2 kartra_headline--light-teal kartra_headline--text-center"
                                style="position: relative;">
                                <p style="font-size: 2rem;"><strong><span style="font-size: 2rem;"><span
                                                style="color: rgb(255, 0, 0); font-size: 2rem;">INTRODUCING</span></span></strong>
                                </p>
                            </div>
                        </div>
                        <div data-component="image" href="javascript: void(0);" id="UwQ4vJPXZG">
                            <picture>
                                <!--<source type="image/webp"-->
                                <!--    data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png">-->
                                <!--</source>-->
                                <!--<source type="image/jpeg"-->
                                <!--    data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png">-->
                                <!--</source>-->
                                <img
                                    class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                    style="border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; margin: 10px auto; opacity: 1;"
                                    id="1553804861265_formbutton"
                                    data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png">
                            </picture>
                        </div>
                        <div data-component="text" id="Mz9fWlFL2q">
                            <div class="kartra_text kartra_text--size-big-special-small kartra_text--light-slate-grey kartra_text--text-center kartra_text--font-weight-regular"
                                style="position: relative;">
                                <p style="font-size: 0.8rem;"><span style="font-size: 0.8rem; color: rgb(0, 0, 0);">The
                                        <strong><span style="font-size: 0.8rem; color: rgb(0, 0, 0);"><span
                                                    style="font-size: 0.8rem; color: rgb(0, 0, 0);">NEW BREAKTHROUGH
                                                    COURSE</span></span></strong> that helps you lose weight, lower
                                        cholesterol, and reduce blood pressure and medications <strong><span
                                                style="font-size: 0.8rem; color: rgb(0, 0, 0);"><em><span
                                                        style="font-size: 0.8rem; color: rgb(0, 0, 0);"><span
                                                            style="background-color: rgb(255, 255, 0); font-size: 0.8rem; color: rgb(0, 0, 0);">i</span></span></em><em><span
                                                        style="font-size: 0.8rem; color: rgb(0, 0, 0);"><strong><span
                                                                style="font-size: 0.8rem; color: rgb(0, 0, 0);"><em><span
                                                                        style="font-size: 0.8rem; color: rgb(0, 0, 0);"><span
                                                                            style="background-color: rgb(255, 255, 0); font-size: 0.8rem; color: rgb(0, 0, 0);">n</span></span></em></span></strong><span
                                                            style="background-color: rgb(255, 255, 0); font-size: 0.8rem; color: rgb(0, 0, 0);">
                                                            only 14 days!</span></span></em></span></strong></span></p>
                            </div>
                        </div>
                        <div data-component="image" href="javascript: void(0);" id="dH9EUIRZVB">
                            <picture>
                                <!--<source type="image/webp"-->
                                <!--    data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3797007_155374820656314DTAH-combine-1024x726.webp">-->
                                <!--</source>-->
                                <source type="image/png"
                                    data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                                </source><img
                                    class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                    id="1544989749662_formbutton"
                                    style="border-color: rgb(194, 174, 131); border-style: solid; border-width: 4px; margin: 20px auto; opacity: 1;"
                                    data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                            </picture>
                        </div>
                        <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small"
                            data-component="bundle" id="yDN3hAjo7C_ShbAQ7iQbt"
                            style="margin-top: 0px; margin-bottom: 20px; padding: 0px 10px;">
                            <div style="border-radius: 0px; border-color: rgb(234, 46, 46); border-style: dashed; border-width: 4px; background-image: none; opacity: 1; background-color: rgba(225, 225, 225, 0.12);"
                                class="background-item background_changer--blur0"></div>
                            <div class="row" data-component="grid">

                                <div class="col-md-12">
                                    <div class="js_kartra_component_holder">
                                        <div data-component="text" id="dyJ0czwWO2">
                                            <div class="kartra_text"
                                                style="position: relative; margin-top: 0px; margin-bottom: 0px;">
                                                <h3 style="text-align: center;"><span
                                                        style="font-size:1.40rem;"><strong><span
                                                                style="font-size: 1.4rem;"><span
                                                                    style="background-color: rgb(255, 255, 0); font-size: 1.4rem; color: rgb(255, 0, 0);">WARNING:</span></span></strong> This
                                                        Could Really Change Your Life!</span></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-component="button" style="width: auto;" id="OwVEy1vVmA">
                            <button onclick="openPaymentSelection()"
                                        class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center"
                                        style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: 'Roboto Condensed'; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                        <span class="fa fa-arrow-right" style="color: rgb(255, 255, 255); font-weight: 700;"></span> START THIS COURSE TODAY
                                    </button>
                                    
                                    <!-- Payment Selection Modal -->
                                    <div id="paymentSelection" class="modal"
                                        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                        <div class="modal-content"
                                            style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                            <button onclick="closePaymentSelection()"
                                                style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                            
                                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Enroll Now!</h2>
                                    
                                            <!-- Tab Navigation -->
                                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Credit/PayPal</button>
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Mobile Money</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center toggle_product dg_popup"
                                                    style='font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";'
                                                    data-frame-id="_0m3wi8oyv" data-kt-layout="0" data-kt-type="checkout"
                                                    data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                                    data-funnel-id="154973" data-product-id="154973"
                                                    data-price-point="9d647b7641994f7cf4e2527587235214"
                                                    rel="9d647b7641994f7cf4e2527587235214" data-asset-id="44" target="_parent"><span
                                                        class="kartra_icon__icon fa fa-arrow-right"
                                                        style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>Pay with card/paypal
                                                </a
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
        style="background-color: rgb(31, 47, 79); padding: 0px 0px 30px;" id="_f4a6crvr6">
        <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
        <div class="background_changer_overlay" style="background-image: none;"></div>
        <div class="container">
            <div class="row row--equal" data-component="grid">
                <div class="col-md-10 col-md-offset-1">
                    <div class="js_kartra_component_holder">

                        <div data-component="headline">
                            <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--size-sm-giant kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--white kartra_headline--margin-bottom-big-tiny"
                                style="position: relative;">
                                <h2><span><span style="font-size: 2.33rem; color: rgb(255, 255, 0);">This Course Helps
                                            You!</span></span></h2>
                            </div>
                        </div>
                        <div data-component="text">
                            <div class="kartra_text kartra_text--size-big-special-small kartra_text--text-center kartra_text--white-dim-grey kartra_text--margin-bottom-semi-large"
                                style="position: relative;">
                                <p style="font-size: 1rem;"><span style="color: rgb(255, 255, 255); font-size: 1rem;">So
                                        what exactly can <em><span
                                                style="color: rgb(255, 255, 255); font-size: 1rem;"><span
                                                    style="color: rgb(255, 255, 255); font-size: 1rem;">14 Days to
                                                    Amazing Health Online Course</span></span></em> do for you and can a
                                        simple course like this really turn everything around for you <em><span
                                                style="color: rgb(255, 255, 255); font-size: 1rem;"><span
                                                    style="color: rgb(255, 255, 255); font-size: 1rem;">in the next
                                                    14 days</span></span></em>?</span></p>
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
                                            class="kartra_icon__icon fa fa-heart-o"></span>
                                    </div>
                                    <div
                                        class="kartra_item_info kartra_item_info--padding-left-special-small-reverse kartra_item_info--md-text-right kartra_item_info--flex-1">
                                        <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                            style="position: relative;">
                                            <p>Increase Energy</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                            style="position: relative;">
                                            <p>Healthy 4-Week Meal plans ---An excellent solution for you because it
                                                contains a series of GUILT-FREE recipes that you and your family can
                                                enjoy anytime. <strong>(Value: $49)</strong></p>
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
                                            <p>Improve Sleep</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                            style="position: relative;">
                                            <p>Rich multimedia presentations ---This is very important to help you learn
                                                at your own pace and stimulate multiple learning
                                                modalities. <strong>(Value: $ 297)</strong></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="kartra_list__item kartra_list__item--flex kartra_list__item--flex-md-reverse"
                                    href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                        id="1553894292909_formbutton"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                        <span style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-stethoscope"></span>
                                    </div>
                                    <div
                                        class="kartra_item_info kartra_item_info--padding-left-special-small-reverse kartra_item_info--md-text-right kartra_item_info--flex-1">
                                        <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                            style="position: relative;">
                                            <p>Reduce Meds</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--margin-bottom-small"
                                            style="position: relative;">
                                            <p>Encouraging supportive community. Get inspired by the stories and results
                                                of others as they share their trials and triumphs.  <strong>(Value:<em>
                                                        Pricele$$</em>)</strong></p>
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
                                class="background-item background_changer--blur0 js-bg-next-gen"
                                data-bg='url("https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/72317571890Medically_Managed_Weight_Flyer__8.5_x_11_in_.pngg")'>
                            </div>
                            <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                class="hand_table_mock_up--frame"
                                data-original="https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp_feature_img_4.png">
                        </div>
                    </div>
                </div>
                <div class="col-md-4 column--vertical-center">
                    <div class="js_kartra_component_holder">
                        <div data-component="list">
                            <ul class="kartra_list">
                                <li class="kartra_list__item kartra_list__item--flex" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                        id="1553894331717_formbutton"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                        <span style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-female"></span>
                                    </div>
                                    <div
                                        class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                        <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                            style="position: relative;">
                                            <p>Lose weight</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                            style="position: relative;">
                                            <p>Exercises an quizzes --- This is HUGE because you'll be able to reinforce
                                                what you're learning (<em>but don't worry, this is not for grade, it is
                                                    self-test</em>). <strong>(Value: $ 27)</strong></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="kartra_list__item kartra_list__item--flex" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                        id="1553894478499_formbutton"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                        <span style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-ambulance"></span>
                                    </div>
                                    <div
                                        class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                        <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                            style="position: relative;">
                                            <p>Reverse Diabetes</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--md-margin-bottom-medium"
                                            style="position: relative;">
                                            <p>Customized support and feedback ---This great because you don't have to
                                                go at it alone. At Cooper Wellness Center, we have over 50 years of
                                                combined support to help you. <strong>(Value: $ 197)</strong></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="kartra_list__item kartra_list__item--flex" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--royal-blue kartra_icon--no-shrink kartra_icon--top-adjust kartra_icon--medium"
                                        id="1553894488527_formbutton"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                        <span style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-life-saver"></span>
                                    </div>
                                    <div
                                        class="kartra_item_info kartra_item_info--padding-left-special-small kartra_item_info--flex-1">
                                        <div class="kartra_item_info__headline kartra_item_info__headline--white kartra_item_info__headline--montserrat-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--h4 kartra_item_info__headline--margin-bottom-extra-small"
                                            style="position: relative;">
                                            <p>Lower Cholesterol</p>
                                        </div>
                                        <div class="kartra_item_info__text kartra_item_info__text--white-dim-grey kartra_item_info__text--margin-bottom-small"
                                            style="position: relative;">
                                            <p>Other great resources and bonuses --- An excellent solution for you to
                                                broaden your scope and take your health to the next
                                                level. <strong>(Value: $ 149)</strong></p>
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
                                <h2 style="text-align: center;"><strong><span><span
                                                style="font-family: verdana; color: rgb(0, 0, 0); font-size: 1.6rem;">But
                                                Don't Just Take My Word For It Only…</span></span></strong></h2>

                                <p style="font-size: 0.8rem;"><span style="color: rgb(0, 0, 0); font-size: 0.8rem;">I've
                                        been quietly letting a select group of people in on my little secret and one by
                                        one, they have been getting amazing results.<br>
                                        <br>
                                        The truth is, I wanted to be sure this would work time and time again.<br>
                                        <br>
                                        More importantly, I wanted to make sure that anyone- including you, could
                                        actually achieve the same results I see time and time again at my Wellness
                                        Center.<br>
                                        <br>
                                        And the results… Well… Frankly, they speak for themselves.</span></p>

                                <h4 style="text-align: center;"><strong><span><span
                                                style="font-size: 1rem; color: rgb(255, 0, 0);">Here’s what but a
                                                handful of people had to say about their
                                                experience:</span></span></strong></h4>
                            </div>
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
                                <iframe width="100%" src="https://app.kartra.com/external_video/youtube/Qj6QL71hB5I"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                    data-video="Qj6QL71hB5I"></iframe>

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
                                    <p><em>"I came to Dr. Cooper because I had diabetes. Now I'm off my medication and
                                            continue to do exercise and follow the diet. If you follow Dr. Cooper's
                                            directions, everything will be OK."</em></p>
                                </div>
                            </div>
                            <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                    class="background-item background_changer--blur0 js-bg-next-gen"
                                    data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3817341_1553897098324Anna-Garcia-1.jpg")'>
                                </div>
                            </div>
                            <div data-component="headline">
                                <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                    style="position: relative;">
                                    <p>Anna Garcia</p>
                                </div>
                            </div>
                            <div data-component="text">
                                <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                    style="position: relative;">
                                    <p><em>Satisfied Patient</em></p>
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
                                <iframe width="100%" src="https://app.kartra.com/external_video/youtube/tFiy_LONIYU"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                    data-video="tFiy_LONIYU"></iframe>

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
                                    <p><em>"Recently I've stopped taking diabetes medications and I've also eliminated
                                            my need for blood pressure medications. I feel great! Since I began the
                                            program, I've gone from 260 lbs to 234 lbs."</em></p>
                                </div>
                            </div>
                            <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                    class="background-item background_changer--blur0 js-bg-next-gen"
                                    data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3817348_1553897160919Carlos-Sanchez.jpg")'>
                                </div>
                            </div>
                            <div data-component="headline">
                                <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                    style="position: relative;">
                                    <p>Carlos Sanchez</p>
                                </div>
                            </div>
                            <div data-component="text">
                                <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                    style="position: relative;">
                                    <p><em>Satisfied Patient</em></p>
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
                                <iframe width="100%" src="https://app.kartra.com/external_video/youtube/PRdH7NxjAdU"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                    data-video="PRdH7NxjAdU"></iframe>

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
                                    <p><em>"This program is simple. Unlike some complicated health directives,
                                            Cooper-Dockery’s suggestions are pared down to what’s most important for
                                            someone to implement within fourteen days."</em></p>
                                </div>
                            </div>
                            <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                    class="background-item background_changer--blur0 js-bg-next-gen"
                                    data-bg='url("//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3817391_1553897389058Foreword_Reviews_logo1.png")'>
                                </div>
                            </div>
                            <div data-component="headline">
                                <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                    style="position: relative;">
                                    <p><strong>ForeWord Clarion </strong></p>
                                </div>
                            </div>
                            <div data-component="text">
                                <div class="kartra_text kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--letter-spacing-extra-tiny kartra_text--text-center"
                                    style="position: relative;">
                                    <p><em>Book Reviewer</em></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content" style="padding: 20px 0px; background-color: rgba(227, 227, 227, 0.19);" id="_qedjabpbf">
        <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 0.3;"></div>
        <div class="background_changer_overlay" style="background-image: none;"></div>
        <div class="container">
            <div class="row" data-component="grid">
                <div class="col-md-12">
                    <div class="js_kartra_component_holder">
                        <div data-component="text" id="o9WlcmIIMt">
                            <div class="kartra_text" style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                <p style="font-size: 2.33rem; text-align: center; line-height: 1.2em;"><b><span
                                            style="font-size: 2.33rem; line-height: 1.2em;"><span
                                                style="line-height: 1.2em; color: rgb(31, 47, 79); font-size: 2.33rem;">COURSE
                                                MODULES</span></span></b>
                                    <font color="#c2ae83"><span style="font-size: 2.33rem; line-height: 1.2em;"><b><span
                                                    style="line-height: 1.2em; font-size: 2.33rem;"><span
                                                        style="line-height: 1.2em; font-size: 2.33rem;"> &amp;
                                                        LESSONS</span></span></b></span></font>
                                </p>
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
                                        style="color: rgb(31, 47, 79); line-height: 1.2em; font-size: 2rem;">14 Days To
                                    </span><span
                                        style="color: rgb(194, 174, 131); line-height: 1.2em; font-size: 2rem;">Amazing
                                        Health </span></p>
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
                                <li class="kartra_list__item kartra_list__item--table" href="javascript: void(0);"
                                    id="8G8zVELUgM">
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
                                                            style="font-size: 1.8rem; line-height: 1.4em; color: rgb(31, 47, 79);">21
                                                            VIDEOS</span></span></b></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="kartra_list__item kartra_list__item--table" href="javascript: void(0);">
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
                                                            style="line-height: 1.4em; font-size: 1.6rem; color: rgb(31, 47, 79);">11
                                                            LESSONS</span></span></b></p>
                                        </div>
                                    </div>
                                </li>
                                <li class="kartra_list__item kartra_list__item--table" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--light-coral-two kartra_icon--negative-top-like-tiny kartra_icon--large"
                                        id="1544536093487_formbutton"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                        <span style="color: rgb(194, 174, 131);"
                                            class="kartra_icon__icon fa fa-download"></span>
                                    </div>
                                    <div class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                        <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-small"
                                            style="position: relative;">
                                            <p style="font-size: 1.6rem; line-height: 1.4em;"><b><span
                                                        style="color: rgb(31, 47, 79); font-size: 1.6rem; line-height: 1.4em;"><span
                                                            style="line-height: 1.4em; font-size: 1.6rem; color: rgb(31, 47, 79);">9
                                                            DOWNLOADS</span></span></b></p>
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
                                class="background-item background-item--shadow-2 background_changer--blur0"></div>
                            <div data-component="text" id="wjrBv5Dqoy">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                    <p style="text-align: center; font-size: 0.8rem;"><span
                                            style="color: rgb(194, 174, 131); font-size: 0.8rem;">SIMPLE PRACTICAL
                                            RELEVANT</span></p>
                                </div>
                            </div>
                            <div data-component="headline" id="jTocOfLEGZ">
                                <div class="kartra_headline kartra_headline--h4"
                                    style="position: relative; margin: 0px 0px 15px;">
                                    <p style="text-align: center;">
                                        <font color="#02173e" face="roboto condensed"><b>HEALTHY MEAL PLANS</b></font>
                                    </p>
                                </div>
                            </div>
                            <div data-component="text">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="text-align: center;">You're in good hands. Simply follow Dr. Cooper's
                                        expert advice that has been honed over 27 years of field experience. </p>
                                </div>
                            </div>
                            <div data-component="text" id="FcijNrb2TO">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                </div>
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
                                class="background-item background-item--shadow-2 background_changer--blur0"></div>
                            <div data-component="text" id="wjrBv5Dqoy">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                    <p style="text-align: center; font-size: 0.8rem;"><span
                                            style="color: rgb(194, 174, 131); font-size: 0.8rem;">LIVING AMAZING
                                            HEALTH</span></p>
                                </div>
                            </div>
                            <div data-component="headline" id="jTocOfLEGZ">
                                <div class="kartra_headline kartra_headline--h4"
                                    style="position: relative; margin: 0px 0px 15px;">
                                    <p style="text-align: center;"><strong><span
                                                style='color: rgb(2, 23, 62); font-family: "roboto condensed";'>SUPER
                                                FOODS</span></strong></p>
                                </div>
                            </div>
                            <div data-component="text" id="NCEWBfBDWc">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="text-align: center;">What should you be fueling your body with for peak
                                        performance, energy, and boosting your immune system? Find out.</p>
                                </div>
                            </div>
                            <div data-component="text" id="FcijNrb2TO">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                </div>
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
                                class="background-item background-item--shadow-2 background_changer--blur0"></div>
                            <div data-component="text" id="wjrBv5Dqoy">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                    <p style="text-align: center; font-size: 0.8rem;"><span
                                            style="color: rgb(194, 174, 131); font-size: 0.8rem;">CHANGING
                                            MINDSETS</span></p>
                                </div>
                            </div>
                            <div data-component="headline" id="jTocOfLEGZ">
                                <div class="kartra_headline kartra_headline--h4"
                                    style="position: relative; margin: 0px 0px 15px;">
                                    <p style="text-align: center;">
                                        <font color="#02173e" face="roboto condensed"><b>HABITS THAT STICK</b></font>
                                    </p>
                                </div>
                            </div>
                            <div data-component="text" id="QS4Yhnh2yK">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="text-align: center;">Though change is often hard, you'll find inspiration
                                        to live your best life with Dr. Cooper's proven lifestyle strategies.</p>
                                </div>
                            </div>
                            <div data-component="text" id="FcijNrb2TO">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                </div>
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
                                class="background-item background-item--shadow-2 background_changer--blur0"></div>
                            <div data-component="text" id="wjrBv5Dqoy">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                    <p style="text-align: center; font-size: 0.8rem;">
                                        <font color="#c2ae83">LATEST SCIENTIFIC RESEARCH</font>
                                    </p>
                                </div>
                            </div>
                            <div data-component="headline" id="jTocOfLEGZ">
                                <div class="kartra_headline kartra_headline--h4"
                                    style="position: relative; margin: 0px 0px 15px;">
                                    <p style="text-align: center;">
                                        <font color="#02173e" face="roboto condensed"><b>EXERCISE AS MEDICINE</b></font>
                                    </p>
                                </div>
                            </div>
                            <div data-component="text" id="cOc97mTrpF">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="text-align: center;">So much more is now known about exercise that Dr.
                                        Cooper wants to bring you up to speed to reap all the benefits.</p>
                                </div>
                            </div>
                            <div data-component="text" id="FcijNrb2TO">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                </div>
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
                                class="background-item background-item--shadow-2 background_changer--blur0"></div>
                            <div data-component="text" id="wjrBv5Dqoy">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                    <p style="text-align: center; font-size: 0.8rem;">
                                        <font color="#c2ae83">AND SO MUCH MORE!</font>
                                    </p>
                                </div>
                            </div>
                            <div data-component="headline" id="jTocOfLEGZ">
                                <div class="kartra_headline kartra_headline--h4"
                                    style="position: relative; margin: 0px 0px 15px;">
                                    <p style="text-align: center;"><strong><span
                                                style='color: rgb(2, 23, 62); font-family: "roboto condensed";'>LONGEVITY
                                                SECRETS</span></strong></p>
                                </div>
                            </div>
                            <div data-component="text" id="9l04e3AtyO">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="text-align: center;">Once you delve into this course you'll find these
                                        secrets spread throughout the 11 lessons and see video results testimonials.</p>
                                </div>
                            </div>
                            <div data-component="text" id="FcijNrb2TO">
                                <div class="kartra_text kartra_text--font-weight-regular" style="position: relative;">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row" data-component="grid">
                <div class="col-md-10 col-md-offset-1">
                    <div class="js_kartra_component_holder">
                        <div data-component="button" style="width: auto;" id="lQ3S7KDQZh">
                                    <button onclick="openPaymentSelection()"
                                        style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; padding: 10px 20px; font-family: 'Roboto Condensed'; text-align: center;"
                                        class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center"
                                        data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                        data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                        data-funnel-id="154973" data-product-id="154973"
                                        data-price-point="9d647b7641994f7cf4e2527587235214"
                                        rel="9d647b7641994f7cf4e2527587235214" data-asset-id="47"
                                        target="_parent">
                                        <span class="kartra_icon__icon fa fa-shopping-cart" style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 400;"></span>ENROLL IN THIS COURSE NOW!!
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
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; width: 50%;">Pay with Mobile Money</button>
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_product dg_popup"
                                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                                    data-frame-id="_qedjabpbf" data-kt-type="checkout" data-kt-owner="DpwDQa6g"
                                                    data-kt-value="9d647b7641994f7cf4e2527587235214" data-kt-layout="1"
                                                    data-funnel-id="154973" data-product-id="154973"
                                                    data-price-point="9d647b7641994f7cf4e2527587235214"
                                                    rel="9d647b7641994f7cf4e2527587235214" data-asset-id="81" target="_parent">
                                                    Pay with Credit/PayPal</a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('1')"
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
                        <div data-component="text" id="xzesLo6lOO">
                            <div class="kartra_text" style="position: relative; margin-top: 0px; margin-bottom: 50px;">
                                <p style="font-size: 0.65rem; text-align: center;"><strong><span
                                            style="font-size: 0.65rem;"><em><span style="font-size: 0.65rem;"><span
                                                        style="font-size: 0.65rem;">Starting as low as
                                                        $54.95!</span></span></em></span></strong></p>
                            </div>
                        </div>
                        <div data-component="image" id="G4zK80CFg8">
                            <img class="kartra_image pull-center kartra_image--full"
                                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                        </div>
                        <div data-component="image" href="javascript: void(0);" id="QrhT3Z9WH5">
                            <picture>
                                <!--<source type="image/webp"-->
                                <!--    data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3803391_155379716083514DAH-banner.webp">-->
                                <!--</source>-->
                                <!--<source type="image/jpeg"-->
                                <!--    data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3803391_155379716083514DAH-banner.jpg">-->
                                <!--</source>-->
                                <img
                                    class="kartra_image kartra_image--full pull-left background_changer--blur0"
                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                    id="1553899064175_formbutton"
                                    style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                    data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png">
                            </picture>
                        </div>
                        <div data-component="text" id="am04oykvVC">
                            <div class="kartra_text" style="position: relative;">
                                <p style="font-size: 28px; text-align: center;"><strong><span
                                            style="font-size: 28px;"><span
                                                style="color: rgb(128, 0, 0); font-size: 28px;">That’s over $797 Worth
                                                of Pure Value You’re Getting!</span></span></strong></p>

                                <h4><span><span style="font-size:20px;">But you may possibly be thinking that with all
                                            this value… What's the catch?</span></span></h4>

                                <p style="font-size: 20px;"><span style="font-size:20px;">And the catch is
                                        this… <strong><span style="font-size: 20px;"><span style="font-size: 20px;">You
                                                    just need to DECIDE to change.</span></span></strong></span></p>

                                <p style="font-size: 20px;"> </p>

                                <p style="font-size: 20px;"><span style="font-size:20px;">I'll be honest, you could skip
                                        over this offer today and continue to go from doctor to doctor, waste time,
                                        money, and energy on this plan, that program, and all the hype. </span></p>

                                <p style="font-size: 20px;"> </p>

                                <p style="font-size: 20px; text-align: center;"><strong><span
                                            style="font-size: 20px;"><span style="font-size:20px;">It's up to
                                                you.</span></span></strong></p>

                                <blockquote>
                                    <h5><span><span><em><span><span style="font-size: 20px;">But you'd have to shell out
                                                            at least $250 a month in a year of running
                                                            around!</span></span></em></span></span></h5>
                                </blockquote>

                                <p style="font-size: 20px;"><span style="font-size:20px;">And even then, you still
                                        probably won't get the results you’re looking for.</span></p>

                                <blockquote>
                                    <p style="font-size: 20px;"><span><span style="font-size:20px;">You could even hire
                                                a personal trainer to the tune of $99 a month for two gruesome sessions,
                                                and weeks of sweat and tears and with a lot of hard work and a few
                                                months of time before you even begin to see some kind of results.
                                            </span></span></p>

                                    <p style="font-size: 20px;"> </p>

                                    <p style="font-size: 20px;"><span><span style="font-size:20px;">Given enough time
                                                and money, you may, eventually, <em>(if you're lucky),</em> get to your
                                                goal. <strong>OR,</strong></span></span></p>
                                </blockquote>
                            </div>
                        </div>
                        <div data-component="button" style="width: auto;" id="MnHWk8QbeW">
                            <button onclick="openPaymentSelection()"
                                class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center"
                                style="font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: 'Roboto Condensed'; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">
                                <span class="fa fa-arrow-right" style="color: rgb(255, 255, 255); font-weight: 700;"></span> START THIS COURSE TODAY
                            </button>
                                    
                            <!-- Payment Selection Modal -->
                            <div id="paymentSelection" class="modal"
                                style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                                <div class="modal-content"
                                    style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                                    <button onclick="closePaymentSelection()"
                                        style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    
                                    <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">ENROLL NOW!</h2>
                            
                                    <!-- Tab Navigation -->
                                    <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                        <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Credit/PayPal</button>
                                        <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: #ddd; width: 50%;">Pay with Mobile Money</button>
                                    </div>
                            
                                    <!-- Credit Card Tab Content -->
                                    <div id="creditTab" class="tab-content" style="display: block; padding: 20px;">
                                        <p>Complete your payment with Credit Card or PayPal:</p>
                                        <a href="javascript:void(0);"
                                            class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center toggle_product dg_popup"
                                            style='font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";'
                                            data-frame-id="_qedjabpbf" data-kt-layout="1" data-kt-type="checkout"
                                            data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                            data-funnel-id="154973" data-product-id="154973"
                                            data-price-point="9d647b7641994f7cf4e2527587235214"
                                            rel="9d647b7641994f7cf4e2527587235214" data-asset-id="82" target="_parent"><span
                                                class="kartra_icon__icon fa fa-arrow-right"
                                                style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>Pay with Credit/PayPal
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
                                    <label for="billing-address" style="font-weight: bold; color: #555;">Billing Address:</label>
                                    <input type="text" id="billing-address" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

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
    <script type="text/javascript" async="" class="everWebinarScript"
        src="//events.genndi.com/register.evergreen.extra.js"></script>
    <div class="content content--padding-extra-large" style="padding: 30px 0px 10px; background-color: rgb(31, 47, 79);"
        id="_1etxtiyvq">
        <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
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
                                                style="font-size: 2rem; color: rgb(255, 255, 255); font-family: roboto;">ABOUT
                                                YOUR </span><span
                                                style="font-size: 2rem; font-family: roboto; color: rgb(255, 165, 0);">COURSE
                                                INSTRUCTOR</span></span></strong></p>
                            </div>
                        </div>
                        <div data-component="text" id="OdnPJ3VuRO">
                            <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                style="position: relative;">
                                <p>
                                    <font color="#ffffff"><span style="font-size: 20.425px;"><i>Physician, author,
                                                speaker, humanitarian, wife, mother, and TV producer.</i></span></font>
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
        <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
        <div class="background_changer_overlay" style="background-image: none;"></div>
        <div class="container">
            <div class="row row--equal" data-component="grid">
                <div class="col-md-4 column--vertical-center background_changer--blur0"
                    style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                    <div class="js_kartra_component_holder">
                        <div data-component="image" href="javascript: void(0);" id="o3unMi35Kf">
                            <picture>
                                <!--<source type="image/webp"-->
                                <!--    data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2548000_1542617857288DRCooper-image1.webp">-->
                                <!--</source>-->
                                <source type="image/png"
                                    data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/20434784_60cbe72dd7bdb_DRCooper-image.png">
                                </source><img
                                    class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                    style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 307px; max-width: 100%; height: auto;"
                                    id="1544923456889_formbutton"
                                    data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2548000_1542617857288DRCooper-image1.pnghttps://d11n7da8rpqbjy.cloudfront.net/cooperwellness/20434784_60cbe72dd7bdb_DRCooper-image.png">
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
                                <p style="font-size: 1.6rem;"><strong><span style="font-size: 1.6rem;"><span
                                                style="font-family: roboto; color: rgb(31, 47, 79); font-size: 1.6rem;">Dr.
                                                Dona </span></span></strong>
                                    <font face="roboto"><span style="font-size: 1.6rem;"><span
                                                style="color: rgb(194, 174, 131); font-size: 1.6rem;">Cooper-Dockery,
                                            </span></span></font><strong><span style="font-size: 1.6rem;"><span
                                                style="font-family: roboto; color: rgb(31, 47, 79); font-size: 1.6rem;">MD.</span></span></strong>
                                </p>
                            </div>
                        </div>
                        <div data-component="text" id="8Ao2Xi1axy">
                            <div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium"
                                style="position: relative; margin-top: 0px; margin-bottom: 10px;">
                                <p style="text-align: justify;"><span style="color:#696969;">Dona Cooper-Dockery, M.D.,
                                        is <i><span style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">an accomplished</span></span></i>
                                        board-certified physician, #1 bestselling author, <i><span
                                                style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">speaker, humanitarian, and
                                                    philanthropist </span></span></i>who has dedicated over 27 years to
                                        positively changing healthcare outcomes both nationally and internationally. She
                                        holds active memberships in the <em><span
                                                style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">American Academy of Lifestyle
                                                    Medicine</span></span></em> and the <em><span
                                                style="color: rgb(105, 105, 105);">American Medical
                                                Association</span></em>.</span></p>

                                <p style="text-align: justify;"> </p>

                                <p style="text-align: justify;"><span style="color:#696969;">She's the author of <em>Get
                                            Healthy for Life, Fourteen Days to Amazing Health, </em><em><span
                                                style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">My Health and The
                                                    Creator</span></span></em>, <em>Incredibly Delicious Vegan
                                            Recipes,</em> <i><span style="color: rgb(105, 105, 105);">and several
                                                manuals and courses and a quarterly magazine. </span></i>Dr.
                                        Cooper-Dockery is also the founder and director of <em><span
                                                style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">Cooper Internal Medicine and the
                                                    Cooper Wellness and Disease Prevention
                                                    Center</span></span></em> and host of the popular TV show, <em><span
                                                style="color: rgb(105, 105, 105);"><span
                                                    style="color: rgb(105, 105, 105);">Get Healthy with Dr.
                                                    Cooper</span></span></em>, which airs bi-weekly locally and on Fox,
                                        and <span style="color: rgb(105, 105, 105);">can be seen in 40 million homes
                                            across America.</span></span></p>
                            </div>
                        </div>
                        <div data-component="text" id="hXglQBuYna">
                            <div class="kartra_text" style="position: relative;"></div>
                        </div>
                        <div data-component="text" id="XFbu33eRrh">
                            <div class="kartra_text" style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                <p style="font-size: 1.4rem; text-align: center; line-height: 1.2em;">
                                    <font color="#1f2f4f"><span style="font-size: 1.4rem; line-height: 1.2em;"><b><span
                                                    style="font-size: 1.4rem; line-height: 1.2em;">Lets Take This
                                                    Journey Together!</span></b></span></font>
                                </p>
                            </div>
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
                        <div data-component="carousel">
                            <div class="carousel-wrapper" style="margin: 0px auto 20px;">
                                <div class="carousel slide kartra_carousel" data-selector="kartra_carousel"
                                    data-ride="carousel" data-interval="0" style="margin-left:auto;margin-right:auto;"
                                    id="awDj0XdaXM">
                                    <ol class="carousel-indicators">
                                        <li data-target="#awDj0XdaXM" data-slide-to="0" class="active"
                                            style="margin-right:4px">
                                        </li>
                                        <li data-target="#awDj0XdaXM" data-slide-to="1" class=""
                                            style="margin-right:4px">
                                        </li>
                                        <li data-target="#awDj0XdaXM" data-slide-to="2" class=""
                                            style="margin-right:4px">
                                        </li>
                                    </ol>
                                    <div class="carousel-inner">
                                        <div class="item active js-bg-next-gen" style=""
                                            data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png')">
                                            <div class="carousel-caption">
                                                <h3>14 Days to Amazing Health is Yours!</h3>
                                                <p>Learn at home. Anytime. Anywhere. Enroll Now!</p>
                                            </div>
                                        </div>
                                        <div class="item js-bg-next-gen" style=""
                                            data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/14259522_5fa05aa03dd02_14dtah-banner1.JPG')">
                                            <div class="carousel-caption">
                                                <h3></h3>
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="item js-bg-next-gen" style=""
                                            data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996190_1674844805FWjmulticasting_sites.png')">
                                            <div class="carousel-caption">
                                                <h3></h3>
                                                <p>Multimedia approach to educating about health.</p>
                                            </div>
                                        </div>
                                    </div> <a class="left carousel-control" href="#awDj0XdaXM" role="button"
                                        data-slide="prev" data-frame-id="_sysp3czxc" target="_parent"> <span
                                            class="glyphicon-chevron-left fa fa-arrow-left" aria-hidden="true"></span>
                                        <span class="sr-only">Previous</span></a> <a class="right carousel-control"
                                        href="#awDj0XdaXM" role="button" data-slide="next" data-frame-id="_sysp3czxc"
                                        target="_parent"> <span class="glyphicon-chevron-right fa fa-arrow-right"
                                            aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
                                </div>
                            </div>
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
                                <iframe width="100%" src="https://app.kartra.com/external_video/youtube/nQ32QSmjrKc"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                    data-video="nQ32QSmjrKc"></iframe>

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
                                <iframe width="100%" src="https://app.kartra.com/external_video/vimeo/295420588"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="vimeo"
                                    data-video="295420588"></iframe>

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
                                <iframe width="100%" src="https://app.kartra.com/external_video/youtube/maN8Cs--Z1Y"
                                    frameborder="0" scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                    data-video="maN8Cs--Z1Y"></iframe>

                                <div class="kartra_video_player_shadow"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content content--padding-medium content--padding-bottom-none content--padding-top-none"
        style="background-color: #ffffff;" id="_vi0x7uznn">
        <div class="background_changer"></div>
        <div class="background_changer_overlay"></div>
        <div>
            <div>
                <div class="row row--margin-left-right-none" data-component="grid">
                    <div class="col-md-12 column--padding-none">
                        <div class="js_kartra_component_holder">
                            <div data-component="button" style="width: auto;" id="28oDoIYvu4">
                                <button onclick="openPaymentSelection()"
                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                    class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 js_kartra_trackable_object kartra_button1--solid kartra_button1--giant kartra_button1--squared kartra_button1--shadow-small pull-center pull-center"
                                    data-frame-id="_0m3wi8oyv" data-kt-layout="1" data-kt-type="checkout"
                                    data-kt-owner="DpwDQa6g" data-kt-value="9d647b7641994f7cf4e2527587235214"
                                    data-funnel-id="154973" data-product-id="154973"
                                    data-price-point="9d647b7641994f7cf4e2527587235214"
                                    rel="9d647b7641994f7cf4e2527587235214" data-asset-id="47"
                                    target="_parent">
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
                                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; width: 50%;">Pay with Mobile Money</button>
                                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
                                            </div>
                                    
                                            <!-- Credit Card Tab Content -->
                                            <div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
                                                <p>Complete your payment with Credit Card or PayPal:</p>
                                                <a href="javascript:void(0);"
                                                    class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font js_kartra_trackable_object kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_product dg_popup"
                                                    style='color: rgb(255, 255, 255); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed"; border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33);'
                                                    data-frame-id="_vi0x7uznn" data-effect="kartra_css_effect_6" data-kt-layout="1"
                                                    data-kt-type="checkout" data-kt-owner="DpwDQa6g"
                                                    data-kt-value="c7d4071f65bc70e18cfb89c667e6d154" data-funnel-id="154974"
                                                    data-product-id="154974" data-price-point="c7d4071f65bc70e18cfb89c667e6d154"
                                                    rel="c7d4071f65bc70e18cfb89c667e6d154" data-asset-id="70" target="_parent">Pay with Credit/PayPal</a>
                                            </div>
                                            
                                            <!-- Mobile Money Tab Content -->
                                            <div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
                                                <p>Complete your payment with Mobile Money:</p>
                                                <button onclick="openPaymentPopup('1')"
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
                            <div data-component="text" id="orDxtrhmW5">
                                <div class="kartra_text" style="position: relative;">
                                    <p style="font-size: 0.65rem; text-align: center;"><strong><span
                                                style="font-size: 0.65rem;"><span
                                                    style="color: rgb(178, 34, 34); font-size: 0.65rem;">Only</span>
                                                <s><span style="font-size: 0.65rem;"><span
                                                            style="font-size: 0.65rem;">$197</span></span></s> <span
                                                    style="color: rgb(178, 34, 34); font-size: 0.65rem;">$97!</span></span></strong>
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
                                                style="font-size: 0.5rem;"><span style="font-size: 0.5rem;">Every
                                                    individual is different so no one can predict your result nor can we
                                                    guarantee any results whatsoever. The testimonials herein presented
                                                    should not be construed as "typical." As with all information
                                                    regarding health, be sure to research and review carefully. You
                                                    should consult your professional health care provider before
                                                    implementing any exercise or dietary program. The info presented on
                                                    this page is for educational purposes only and not intended to treat
                                                    or diagnose any disease, nor should it substitute medical advice
                                                    offered by your physician or other licensed healthcare provider. Use
                                                    at your own risk.</span></span></em></p>
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
                                    data-component="bundle" id="Zek7peHwwQ_J7FTJoomB0" style="margin: 0px 0px 15px;">
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                        href="javascript: void(0);" data-frame-id="_vi0x7uznn"
                                        style='color: rgb(251, 250, 248); font-weight: 400; font-family: "Open Sans";'
                                        target="_parent">DISCLAIMERS</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                        href="javascript: void(0);" data-frame-id="_vi0x7uznn"
                                        style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                        target="_parent">TERMS</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                        href="javascript: void(0);" data-frame-id="_vi0x7uznn"
                                        style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                        target="_parent">DCMA NOTICE</a><a
                                        class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny"
                                        href="javascript: void(0);" data-frame-id="_vi0x7uznn"
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
    <script src="//app.kartra.com/resources/js/page_check?page_id=YDxmyuNWgBZa" async defer></script>
    <script>
    if ( typeof window['jQuery'] !== 'undefined') {
        window.jsVars = { "page_title": "14 Days to Amazing Health Course", "page_description": "Let Dr. Dona Cooper help you lose weight, lower cholesterol, and reduce blood pressure and meds in ONLY 14 Days!", "page_keywords": "get healthy for life, 14 days to amazing health, Dr. dona cooper, Dona Cooper-Dockery, get healthy with Dr. Cooper, Cooper wellness center, cooper internal medicine", "page_robots": "index, follow", "secure_base_url": "\/\/app.kartra.com\/", "global_id": "YDxmyuNWgBZa" };
    window.global_id = 'YDxmyuNWgBZa';
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
                                    data-type-id="196" data-type-owner="DpwDQa6g">
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
    <!--// GDPR cookie BANNER -->
</body>

</html>