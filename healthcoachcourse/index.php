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
    <title>Certified Health Coach Order</title>
    <meta name="description"
        content="Health coaches are in big demand! Start your career now without sacrificing 3-4 years of your life to get certified. Enroll now.">
    <meta name="keywords" content="">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587335975.jpg">

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
        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("paymentModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
                                    
        let courseNO;
        let ghs_price;
        let course_name;
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
                    course_name = data.course;
                    console.log("COurse name received", course_name);
                    courseNO = data.courseID;
                    ghs_price = data.price;
                    console.log('type of price received:', data.price, typeof data.price);
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
            const paymentprocessing = document.getElementById('paymentprocessing');
            paymentprocessing.disabled = true;
            paymentprocessing.style.backgroundColor = 'grey';
            paymentprocessing.innerHTML = 'Processing payment...';
            const email = document.getElementById("mail").value;
            const phone = document.getElementById("phone").value;
            course_no = courseNO;
            price_ghs = ghs_price;
            // console.log('ghs_price:', price_ghs, typeof price_ghs);
            const course_purchased = course_name
            // console.log("Course name sent to payment api", course_name);
            // console.log("Course id sent to payment api", course_no);
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
            console.log('Transaction ammount in pesewas:', amountInPesewas);
            
            const paystackPublicKey = "<?php echo $paystackPublicKey; ?>";
                
            // Initialize Paystack payment
            const handler = PaystackPop.setup({
                key: paystackPublicKey,
                email: email,
                amount: amountInPesewas,
                currency: "GHS",
                ref: "HCOACHCOURSE" + Math.floor((Math.random() * 1000000000) + 1),
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
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Annie+Use+Your+Telescope:300,300i,400,400i,600,600i,700,700i,900,900i|Asap:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Merriweather:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Annie+Use+Your+Telescope:300,300i,400,400i,600,600i,700,700i,900,900i|Asap:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Merriweather:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
    </noscript>

    <script>
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        (function (w) { "use strict"; if (!w.loadCSS) { w.loadCSS = function () { } } var rp = loadCSS.relpreload = {}; rp.support = function () { var ret; try { ret = w.document.createElement("link").relList.supports("preload") } catch (e) { ret = false } return function () { return ret } }(); rp.bindMediaToggle = function (link) { var finalMedia = link.media || "all"; function enableStylesheet() { link.media = finalMedia } if (link.addEventListener) { link.addEventListener("load", enableStylesheet) } else if (link.attachEvent) { link.attachEvent("onload", enableStylesheet) } setTimeout(function () { link.rel = "stylesheet"; link.media = "only x" }); setTimeout(enableStylesheet, 3e3) }; rp.poly = function () { if (rp.support()) { return } var links = w.document.getElementsByTagName("link"); for (var i = 0; i < links.length; i++) { var link = links[i]; if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) { link.setAttribute("data-loadcss", true); rp.bindMediaToggle(link) } } }; if (!rp.support()) { rp.poly(); var run = w.setInterval(rp.poly, 500); if (w.addEventListener) { w.addEventListener("load", function () { rp.poly(); w.clearInterval(run) }) } else if (w.attachEvent) { w.attachEvent("onload", function () { rp.poly(); w.clearInterval(run) }) } } if (typeof exports !== "undefined") { exports.loadCSS = loadCSS } else { w.loadCSS = loadCSS } })(typeof global !== "undefined" ? global : this);

        window.global_id = 'zp7QjqlNKPrK';
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
        .kartra_link_wrapper--margin-bottom-extra-tiny {
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object"
        data-kt-type="kartra_page_tracking" data-kt-value="zp7QjqlNKPrK" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content" style="padding-top: 40px; padding-bottom: 40px" id="_ms8ylon2x">
                <div class="background_changer"></div>
                <div class="background_changer_overlay"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--lato-font kartra_headline--size-sm-giant kartra_headline--text-center kartra_headline--mat-black"
                                        style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                        <h1><span><strong><span style="font-size: 1.8rem;">Start A Lucrative Career As a
                                                        Certified Health Coach</span></strong></span></h1>
                                    </div>
                                </div>
                                <div data-component="headline" id="TEmieJUeja">
                                    <div class="kartra_headline kartra_headline--lato-font kartra_headline--letter-spacing-small kartra_headline--font-weight-bold kartra_headline--h5 kartra_headline--text-center kartra_headline--orange-tomato"
                                        style="position: relative;">
                                        <h3><span><strong><span style="color: rgb(255, 0, 0);">...WITHOUT LEAVING YOUR
                                                        HOME OR SACRIFICING 3-4 YEARS OF YOUR
                                                        LIFE!</span></strong></span></h3>
                                    </div>
                                </div>
                                <div data-component="video">
                                    <div class="kartra_video"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%"
                                            src="https://app.kartra.com/external_video/vimeo/296224287?autoplay=true"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="vimeo" data-video="296224287?autoplay=true"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="K6yZTnTIOq">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto; opacity: 1;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                    </picture>
                                </div>
                                <div data-component="text" id="X8Tx7yQBrz">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 0px;">
                                        <h1 style="text-align: center;"><strong><span style="font-size: 1.6rem;"><span
                                                        style="color: rgb(178, 34, 34); font-size: 1.6rem;">Health
                                                        Coaches Are In Big Demand!</span></span></strong></h1>

                                        <h5 style="text-align: center;">
                                            <span style="font-size:0.80rem;">The 2012 <em><span
                                                        style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">ICF
                                                            Survey</span></span></em> says it's a $2 Billion Industry
                                                and coaches can command $214 per hour! </span><em><span><span
                                                        style="font-size: 0.65rem;">(Just imaging today's
                                                        numbers)</span></span></em>
                                        </h5>

                                        <p style="text-align: center; font-size: 0.8rem;"><span
                                                style="font-size:0.80rem;">As a certified health coach of the <em><span
                                                        style="font-size: 0.8rem;"><span
                                                            style="font-size: 0.8rem;">Cooper Life Coaching
                                                            Institute</span></span></em>, you can also be in demand and
                                                help change lives while you're at it. And it wouldn't take years to
                                                learn how to do it.</span></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-semi-large content--padding-bottom-none"
                style="background-color: rgb(255, 255, 255); padding: 40px 0px 0px;" id="_fjb4buary">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="divider" id="jqv67WefGi">
                                    <hr class="kartra_divider kartra_divider--border-small kartra_divider--border-gold kartra_divider--margin-bottom-medium pull-center kartra_divider--small"
                                        style="border-color: rgb(255, 202, 41); border-top-style: solid; border-top-width: 5px; margin: 0px auto 10px;">
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--size-extra-large kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-big-tiny"
                                        style="position: relative;">
                                        <p style="font-size: 1rem; line-height: 1.2em;"><strong><span
                                                    style="line-height: 1.2em; font-size: 1rem;"><span
                                                        style="font-size: 1rem; line-height: 1.2em;">THESE PROGRAMS WERE
                                                        CREATED BY MEDICAL DOCTORS AND EXPERTS WITH OVER 70 YEARS OF
                                                        COMBINED FIELD EXPERIENCE. THEY ARE SPECIFICALLY TAILORED TO
                                                        MEET THE 3 DIFFERENT INTEREST LEVELS
                                                        BELOW.</span></span></strong></p>
                                    </div>
                                </div>

                                <div data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-small kartra_divider--border-gold kartra_divider--margin-bottom-medium pull-center kartra_divider--small"
                                        style="border-color: rgb(255, 202, 41); border-top-style: solid; border-top-width: 5px; margin: 0px 416px 20px;">
                                </div>
                                <div data-component="text" id="WF0DZMSIpU">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 45px;">
                                        <p style="font-size: 1rem; text-align: center;"><strong><span
                                                    style="font-size: 1rem;"><span style="font-size: 1rem;">Health
                                                        coaches are needed now more than ever
                                                        before!</span></span></strong> </p>

                                        <p style="font-size: 0.8rem; text-align: center;"><span
                                                style="font-size:0.80rem;">Society is poised for a major health
                                                revolution. </span></p>

                                        <p style="font-size: 0.8rem; text-align: center;"><span
                                                style="font-size:0.80rem;">This paradigm shift will position you to be
                                                the go-to-person for personal wellness intervention.</span></p>

                                        <p style="font-size: 0.8rem; text-align: center;"><span
                                                style="font-size:0.80rem;">Learn our master secrets to health coaching
                                                and get in on this industry disruption now.</span></p>

                                        <p style="font-size: 0.8rem; text-align: center;"><span
                                                style="font-size: 0.8rem;">Our curriculum gives you the knowledge,
                                                confidence, and tools you need to succeed.</span> </p>

                                        <p style="font-size: 0.8rem; text-align: center;"> </p>

                                        <p style="font-size: 0.8rem; text-align: center;"><strong><span
                                                    style="font-size: 0.8rem;"><span
                                                        style="background-color: rgb(255, 255, 0); font-size: 0.8rem;">You
                                                        can choose to get started now in either the Basic, Intermediate,
                                                        or Advance levels.</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="headline" id="am4wl526GL">
                                    <div class="kartra_headline kartra_headline--size-extra-large kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-big-tiny"
                                        style="position: relative; margin-top: 0px; margin-bottom: 15px;">
                                        <p style="font-size: 2rem; line-height: 1.2em;"><strong><span
                                                    style="font-size: 2rem; line-height: 1.2em;"><span
                                                        style='line-height: 1.2em; font-family: "annie use your telescope"; color: rgb(255, 0, 0); font-size: 2rem;'>Your
                                                        Rewarding Career is Just a Click Away!</span></span></strong>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="divider" id="Y3jp9vZaCV">
                                    <hr class="kartra_divider kartra_divider--border-small kartra_divider--border-gold kartra_divider--margin-bottom-medium pull-center kartra_divider--small"
                                        style="border-color: rgb(255, 202, 41); border-top-style: solid; border-top-width: 5px; margin: 0px auto 30px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-6">
                            <div class="js_kartra_component_holder">
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--table">
                                            <div class="kartra_list_img_elem" href="javascript: void(0);">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587335975.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587335975.jpg">
                                                    </source><img
                                                        class="kartra_list_img_elem__image kartra_list_img_elem__image--semi-large kartra_list_img_elem__image--margin-bottom-extra-small pull-left background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                                        data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587335975.jpg">
                                                </picture>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-special-medium">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--negative-margin-top-tiny kartra_item_info__headline--h3 kartra_item_info__headline--asap-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--dim-black kartra_item_info__headline--margin-bottom-big-tiny"
                                                    style="position: relative;">
                                                    <p style="font-size: 1rem;"><span style="font-size:1.00rem;">Basic
                                                            Level Certification</span></p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-semi-large kartra_item_info__text--sm-margin-bottom-extra-medium"
                                                    style="position: relative;">
                                                    <p>This program is tailored for those who want to improve their own
                                                        health, the health of their families, and educate people on how
                                                        to lead a healthier lifestyle through a nutritional approach.
                                                        Students can use this course as credit toward the intermediate
                                                        and advanced certification.</p>

                                                    <p> </p>

                                                    <p>The course teaches you everything you need to know, and avoids
                                                        the stuff that you won’t use. This course is taught by actual
                                                        doctors with many years of experience and who understands what
                                                        the market needs today. Because of their expertise, there’s no
                                                        fluff, leaving highly actionable content.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--table">
                                            <div class="kartra_list_img_elem" href="javascript: void(0);">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364366.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364366.jpg">
                                                    </source><img
                                                        class="kartra_list_img_elem__image kartra_list_img_elem__image--semi-large kartra_list_img_elem__image--margin-bottom-extra-small pull-left background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                                        data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364366.jpg">
                                                </picture>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-special-medium">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--negative-margin-top-tiny kartra_item_info__headline--h3 kartra_item_info__headline--asap-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--dim-black kartra_item_info__headline--margin-bottom-big-tiny"
                                                    style="position: relative;">
                                                    <h4><span style="line-height: 1em;"><strong><span
                                                                    style="line-height: 1em; font-size: 0.8rem;"><span
                                                                        style="line-height: 1em; font-size: 0.8rem;">Intermediate
                                                                        Level
                                                                        Certification</span></span></strong></span></h4>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-semi-large kartra_item_info__text--sm-margin-bottom-extra-medium"
                                                    style="position: relative;">
                                                    <p>Besides acquiring the skills taught at the basic level students
                                                        will also learn how to effectively teach their clients the use
                                                        of home remedies to prevent and to reverse many diseases.
                                                        Students can use this course as credit toward the advanced
                                                        certification.</p>

                                                    <p> </p>

                                                    <p>In level course, you won’t learn things you’ll never use, but
                                                        instead, tutorials will be geared towards real-life examples.
                                                        This will enable you to use your new-found knowledge by
                                                        tomorrow…instead of never.</p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="js_kartra_component_holder">
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--table">
                                            <div class="kartra_list_img_elem" href="javascript: void(0);">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240658.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240658.jpg">
                                                    </source><img
                                                        class="kartra_list_img_elem__image kartra_list_img_elem__image--semi-large kartra_list_img_elem__image--margin-bottom-extra-small pull-left background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                                        data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240658.jpg">
                                                </picture>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-special-medium">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--negative-margin-top-tiny kartra_item_info__headline--h3 kartra_item_info__headline--asap-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--dim-black kartra_item_info__headline--margin-bottom-big-tiny"
                                                    style="position: relative;">
                                                    <h4><span><strong><span style="font-size: 1rem;">Advanced
                                                                    Level Certification</span></strong></span></h4>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-semi-large kartra_item_info__text--sm-margin-bottom-extra-medium"
                                                    style="position: relative;">
                                                    <p>Besides acquiring the skills taught at both basic and
                                                        intermediate levels, students will also learn how to establish a
                                                        successful health coaching practice and know the legal issues
                                                        regarding health coach scope of practice.</p>

                                                    <p> </p>

                                                    <p>Learning isn’t something that happens overnight but our approach
                                                        won't bog you down either. That’s why this course includes
                                                        multimedia learning modalities with each lesson --- helping you
                                                        learn and remember the important stuff in your unique learning
                                                        style.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--table">
                                            <div class="kartra_list_img_elem" href="javascript: void(0);">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240287.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240287.jpg">
                                                    </source><img
                                                        class="kartra_list_img_elem__image kartra_list_img_elem__image--semi-large kartra_list_img_elem__image--margin-bottom-extra-small pull-left background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;"
                                                        data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1589240287.jpg">
                                                </picture>
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-special-medium">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--negative-margin-top-tiny kartra_item_info__headline--h3 kartra_item_info__headline--asap-font kartra_item_info__headline--font-weight-bold kartra_item_info__headline--dim-black kartra_item_info__headline--margin-bottom-big-tiny"
                                                    style="position: relative;">
                                                    <h1><span><strong><span style="font-size: 0.8rem;">Change Your Life
                                                                    and Theirs Too!</span></strong></span></h1>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-black kartra_item_info__text--margin-bottom-semi-large kartra_item_info__text--sm-margin-bottom-extra-medium"
                                                    style="position: relative;">
                                                    <h2>Get started today by picking your level so you can begin this
                                                        rewarding journey today. It's time you turn your passion for
                                                        health into a rewarding career. All the obstacles are removed
                                                        with our training program.</h2>

                                                    <h2> </h2>

                                                    <h2>You can get started today <strong>100% on your time, totally
                                                            online,</strong> and you can move through the
                                                        course <strong>at your own pace.</strong> You're just one click
                                                        away from having a lifestyle only few dream of and
                                                        realize. <em><strong>Lets turn your dream into a reality
                                                                today.</strong></em>
                                                    </h2>
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
            <div class="content content--padding-large"
                style="background-color: rgb(255, 255, 255); padding: 30px 0px 10px;" id="_pb6zx4g4c">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-6" id="9THDZGBQ16">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline" id="i8NbDyv5Em">
                                    <div class="kartra_headline kartra_headline--font-weight-regular kartra_headline--size-giant kartra_headline--dim-black"
                                        style="position: relative;">
                                        <p style="font-size: 1.4rem;"><strong><span
                                                    style="color: rgb(255, 140, 0); font-size: 1.4rem;"><span
                                                        style="font-size: 1.4rem; color: rgb(255, 140, 0);">HEALTH COACH
                                                        CERTIFICATION</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="text" id="sAtAscg6Ad">
                                    <div class="kartra_text kartra_text--dim-grey kartra_text--extra-small kartra_text--margin-bottom-special-medium"
                                        style="position: relative;">
                                        <p>You are extremely important to us. By Submitting Payment below, you are
                                            agreeing to our Terms of Service.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block kartra_headline_block--flex" data-component="bundle"
                                    id="E4kEAQI39H_X4Fq1dCTkQ">
                                    <div class="kartra_headline_block__index">
                                        <div data-component="icon" href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--deep-sky-blue-six kartra_icon--negative-margin-top-like-tiny kartra_icon--large"
                                                style="background-color: rgba(0, 0, 0, 0); margin: -8px auto 0px;">
                                                <span style="color: rgb(243, 113, 33);"
                                                    class="kartra_icon__icon fa fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="kartra_headline_block__info kartra_headline_block__info--padding-left-tiny kartra_headline_block__info--adjust-width js_kartra_component_holder">
                                        <div data-component="headline">
                                            <div
                                                class="kartra_headline kartra_headline--semi-h5 kartra_headline--dim-black kartra_headline--font-weight-medium kartra_headline--margin-bottom-none">
                                                <p>Secure Order</p>
                                            </div>
                                        </div>
                                        <div data-component="text">
                                            <div
                                                class="kartra_text kartra_text--dim-grey kartra_text--text-small kartra_text--margin-bottom-special-medium">
                                                <p>256BIT – Encryption</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="divider" id="85Hx0iWqOD">
                                    <hr
                                        class="kartra_divider kartra_divider--border-tiny kartra_divider--border-dark-transparent kartra_divider--full">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block kartra_headline_block--flex" data-component="bundle"
                                    id="Ak5Iuvj61f_hKvQN1oJWJ">
                                    <div class="kartra_headline_block__index">
                                        <div data-component="icon" href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--deep-sky-blue-six kartra_icon--negative-margin-top-like-tiny kartra_icon--large"
                                                style="background-color: rgba(0, 0, 0, 0); margin: -8px auto 0px;">
                                                <span class="kartra_icon__icon fa fa-thumbs-up"
                                                    style="color: rgb(243, 113, 33);"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="kartra_headline_block__info kartra_headline_block__info--padding-left-tiny kartra_headline_block__info--adjust-width js_kartra_component_holder">
                                        <div data-component="headline">
                                            <div
                                                class="kartra_headline kartra_headline--semi-h5 kartra_headline--dim-black kartra_headline--font-weight-medium kartra_headline--margin-bottom-none">
                                                <p>30 Days</p>
                                            </div>
                                        </div>
                                        <div data-component="text">
                                            <div
                                                class="kartra_text kartra_text--dim-grey kartra_text--text-small kartra_text--margin-bottom-special-medium">
                                                <p>Money Back Guarantee</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="divider" id="6Kg9bfPrJG">
                                    <hr
                                        class="kartra_divider kartra_divider--border-tiny kartra_divider--border-dark-transparent kartra_divider--full">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid" id="1Es7vepXjK">
                        <div class="col-md-6">
                            <div class="js_kartra_component_holder">
                                <div class="checkout_tpl_8 js_checkout_template form_holder js_kartra_trackable_object"
                                    data-component="bundle" data-kt-type="checkout"
                                    data-kt-value="aa2f6d2f2557b789eb88c4d67c72b1eb" data-kt-owner="DpwDQa6g"
                                    data-base-url="https://app.kartra.com/" id="UyG8D05Nqi_prZ4pczWlM"
                                    data-funnel-id="153402" data-product-id="153402"
                                    data-price-point="aa2f6d2f2557b789eb88c4d67c72b1eb"
                                    style="margin-top: 0px; margin-bottom: 0px; position: relative;"
                                    data-color="rgb(255, 112, 67)" data-buttonsize="Small" data-asset-id="1">
                                </div>
                                <div>
                                    <button onclick="openPaymentPopup('3')"
                                        style="width: 100%; padding: 20px; background-color: green; color: white; border: none; border-radius: 4px; font-size: 20px; cursor: pointer;">I'll Pay with Mobile Money
                                    </button>
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
                                                
                                            <p>This course costs <span style="color: red; font-weight:bold;">$</span><span id="course_price" style=" font-weight:bold; color: red;"></span><br/>You'll be charged in your local currency.</p>    
                                            <button onclick="nextStep(2)"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Next</button>
                                        </div>

                                        <!-- Step 2: Billing Details -->
                                        <!-- <div class="form-step" id="step-2" style="display: none;">
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
                                        </div> -->

                                        <!-- Step 2: Payment Details -->
                                        <div class="form-step" id="step-2" style="display: none;">
                                            <h3 style="text-align: center; margin-bottom: 20px;">Payment Amount: $<span
                                                    id="price">0</span></h3>
                                            <button onclick="payWithPaystack(event)" id="paymentprocessing"
                                                style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay
                                                Now</button>
                                            <button onclick="nextStep(1)"
                                                style="width: 100%; padding: 10px; margin-top: 10px; background-color: #555; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Back</button>
                                        </div>

                                        <button onclick="document.getElementById('paymentModal').style.display='none';"
                                            style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                </div>
                                <div data-component="video">
                                    <div class="kartra_video"
                                        style="margin-top: 20px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%"
                                            src="https://app.kartra.com/external_video/youtube/frIzw0Zbv9o"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="youtube" data-video="frIzw0Zbv9o"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                                <div data-component="text" id="hpWNcQq0IG">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 45px;">
                                        <p style="text-align: center;"><span
                                                style="font-size: 25.67px;"><b>BONUSES</b></span></p>

                                        <p style="text-align: center;"><span style="font-size: 20.536px;">When you
                                                enroll today, you also get access to study guides, quizzes, lesson
                                                handouts, and extra videos on:</span></p>

                                        <ul>
                                            <li>
                                                <p><em>The Digestive System.</em></p>
                                            </li>
                                            <li>
                                                <p><em>Simplified Human Cyptology and Diabetes.</em></p>
                                            </li>
                                            <li>
                                                <p><em>The ins and outs of cell physiology and cancer.</em></p>
                                            </li>
                                            <li>
                                                <p><em>Complications of obesity and ways to maintain a healthy
                                                        weight.</em></p>
                                            </li>
                                        </ul>
                                        <p style="font-size: 0.8rem;"><strong><span style="font-size: 0.8rem;"><span
                                                        style="background-color: rgb(255, 255, 0); font-size: 0.8rem;">And
                                                        So Much More!</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="headline" id="q1bCgilPQ8">
                                    <div class="kartra_headline kartra_headline--font-weight-regular kartra_headline--size-giant kartra_headline--dim-black"
                                        style="position: relative; margin-top: 0px; margin-bottom: 0px;">
                                        <p style="line-height: 1.4em; font-size: 1rem;"><strong><span
                                                    style="font-size: 1rem; line-height: 1.4em;"><span
                                                        style="line-height: 1.4em; color: rgb(0, 100, 0); font-size: 1rem;">HELP
                                                        OTHERS ACHIEVE TRANSFORMATION LIKE THESE AS A CERTIFIED HEALTH
                                                        COACH</span></span></strong></p>

                                        <ul></ul>
                                    </div>
                                </div>
                                <div data-component="video" id="JeuZ5Kdmsf">
                                    <div class="kartra_video"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe width="100%"
                                            src="https://app.kartra.com/external_video/youtube/Qj6QL71hB5I"
                                            frameborder="0" scrolling="no" allowfullscreen="true"
                                            data-video-type="youtube" data-video="Qj6QL71hB5I"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="js_kartra_component_holder">




                                <div data-component="headline" id="Lcor5Q1MK9">
                                    <div class="kartra_headline kartra_headline--font-weight-regular kartra_headline--size-giant kartra_headline--dim-black"
                                        style="position: relative;">
                                        <p style="line-height: 1.4em; font-size: 1.2rem;"><strong><span
                                                    style="color: rgb(0, 100, 0); line-height: 1.4em; font-size: 1.2rem;"><span
                                                        style="font-size: 1.2rem; line-height: 1.4em; color: rgb(0, 100, 0);">HERE'S
                                                        A BREAKDOWN OF WHAT TO EXPECT WHEN YOU ENROLL
                                                        TODAY:</span></span></strong></p>

                                        <ul></ul>
                                    </div>
                                </div>
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--deep-sky-blue-six kartra_icon--negative-margin-top-semi-tiny kartra_icon--no-shrink kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: -12px auto 0px;">
                                                <span class="kartra_icon__icon fa fa-check"
                                                    style="color: rgb(243, 113, 33);"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-tiny kartra_item_info--flex-1">
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-grey kartra_item_info__text--size-extra-medium kartra_item_info__text--margin-bottom-special-medium"
                                                    style="position: relative;">
                                                    <p><strong><span style="color: #212121">Support Along The
                                                                Way</span></strong></p>

                                                    <p>We provide academic support making sure the student understands
                                                        the material taught. We also provide technical support with
                                                        problems related to the website login and navigation. You're
                                                        just an email or click away to get help.</p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--deep-sky-blue-six kartra_icon--negative-margin-top-semi-tiny kartra_icon--no-shrink kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: -12px auto 0px;">
                                                <span class="kartra_icon__icon fa fa-check"
                                                    style="color: rgb(243, 113, 33);"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-tiny kartra_item_info--flex-1">
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-grey kartra_item_info__text--size-extra-medium kartra_item_info__text--margin-bottom-special-medium"
                                                    style="position: relative;">
                                                    <p><strong><span style="color: #212121">Relevant Cutting-edge
                                                                Content</span></strong></p>

                                                    <p>Course materials are taught by credentialed professionals,
                                                        experts, and practitioners in their industry. The multimedia
                                                        presentations are engaging and filled with the latest best
                                                        practices. We also refer our students to additional material
                                                        that we believe may enrich their knowledge. </p>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="kartra_list__item kartra_list__item--flex"
                                            href="javascript: void(0);">
                                            <div class="kartra_icon kartra_icon--deep-sky-blue-six kartra_icon--negative-margin-top-semi-tiny kartra_icon--no-shrink kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: -12px auto 0px;">
                                                <span class="kartra_icon__icon fa fa-check"
                                                    style="color: rgb(243, 113, 33);"></span>
                                            </div>
                                            <div
                                                class="kartra_item_info kartra_item_info--padding-left-tiny kartra_item_info--flex-1">
                                                <div class="kartra_item_info__text kartra_item_info__text--dim-grey kartra_item_info__text--size-extra-medium kartra_item_info__text--margin-bottom-special-medium"
                                                    style="position: relative;">
                                                    <p>
                                                        <font color="#212121"><b>Tools to Help You Launch</b></font>
                                                    </p>

                                                    <p>Upon completion, you get access to our rich resource library of
                                                        programs, products, and courses to take your business to the
                                                        next level. Further help is also available to show you other
                                                        creative ways to monetize your new-found knowledge so you can
                                                        make a lasting impact. Enroll now and lets build a strong
                                                        relationship into the future. Purchase now to get started.</p>
                                                </div>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                                <div data-component="divider" id="sjTVeuDqey">
                                    <hr
                                        class="kartra_divider kartra_divider--border-tiny kartra_divider--border-dark-transparent kartra_divider--full kartra_divider--margin-bottom-extra-small">
                                </div>
                                <div data-component="icon" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--dark kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--circled kartra_icon--small"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto 25px; border-color: rgb(246, 113, 11);">
                                        <span style="color: rgb(246, 113, 11);"
                                            class="kartra_icon__icon fa fa-gift"></span></div>
                                </div>
                                <div data-component="headline" id="Zb9sUFfLI6">
                                    <div class="kartra_headline kartra_headline--font-weight-regular kartra_headline--size-giant kartra_headline--dim-black"
                                        style="position: relative;">
                                        <p style="line-height: 1.4em; font-size: 2.33rem; text-align: center;">
                                            <strong><span
                                                    style="color: rgb(0, 0, 0); line-height: 1.4em; font-size: 2.33rem;"><span
                                                        style="line-height: 1.4em; font-size: 2.33rem; color: rgb(0, 0, 0);">Course
                                                        Objectives</span></span></strong></p>

                                        <ul></ul>
                                    </div>
                                </div>
                                <div data-component="list">
                                    <ul class="kartra_list">
                                        <li
                                            class="kartra_list__item kartra_list__item--md-margin-bottom-extra-medium kartra_list__item--table">
                                            <div class="kartra_list_img_elem">
                                                <img class="kartra_list_img_elem__image kartra_list_img_elem__image--small"
                                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                    data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/content-img/technology-desktop.png">
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-small">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--h5 kartra_item_info__headline--black"
                                                    style="position: relative;">
                                                    <p>Basic Certificate Level</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey"
                                                    style="position: relative;">
                                                    <p> After completion of the course curriculum students will be able
                                                        to:</p>

                                                    <ol>
                                                        <li>Evaluate the health risks of individuals and families.</li>
                                                        <li>Identify the health risks of individuals and families.</li>
                                                        <li>Teach the nutritional principles of eating healthy to
                                                            individuals and families.</li>
                                                        <li>Be part of our team in promoting a healthy lifestyle in
                                                            his/her sphere of influence.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </li>
                                        <li
                                            class="kartra_list__item kartra_list__item--md-margin-bottom-extra-medium kartra_list__item--table">
                                            <div class="kartra_list_img_elem">
                                                <img class="kartra_list_img_elem__image kartra_list_img_elem__image--small"
                                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                    data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/content-img/Graphic-Design-Tools-14.png">
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-small">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--h5 kartra_item_info__headline--black"
                                                    style="position: relative;">
                                                    <p>Intermediate Certificate Level</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey"
                                                    style="position: relative;">
                                                    <p>After completion of the course curriculum students will be able
                                                        to:</p>

                                                    <ol>
                                                        <li>EVERYTHING IN BASIC (<em>plus</em>).</li>
                                                        <li>Apply health principles that prevent and cure many diseases.
                                                        </li>
                                                        <li>Teach the use of home remedies for disease prevention and
                                                            healing.</li>
                                                        <li>Teach the importance of micro-nutrients for disease
                                                            prevention and healing.</li>
                                                        <li>Teach the use of movement for disease prevention and
                                                            healing.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </li>
                                        <li
                                            class="kartra_list__item kartra_list__item--md-margin-bottom-extra-medium kartra_list__item--table">
                                            <div class="kartra_list_img_elem">
                                                <img class="kartra_list_img_elem__image kartra_list_img_elem__image--small"
                                                    src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                    data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/content-img/Documents_Bookmarks-17.png">
                                            </div>
                                            <div class="kartra_item_info kartra_item_info--padding-left-small">
                                                <div class="kartra_item_info__headline kartra_item_info__headline--h5 kartra_item_info__headline--black"
                                                    style="position: relative;">
                                                    <p>Advanced Certificate Level</p>
                                                </div>
                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey"
                                                    style="position: relative;">
                                                    <p>After completion of the course curriculum students will be able
                                                        to:</p>

                                                    <ol>
                                                        <li>EVERYTHING IN BASIC &amp; INTERMEDIATE (<em>plus</em>).</li>
                                                        <li>Understand the relationship between mind and body in health
                                                            and sickness.</li>
                                                        <li>Provide effective health coaching to individuals and
                                                            families.</li>
                                                        <li>Know the legal issues regarding coaching scope of practice.
                                                        </li>
                                                        <li>Conduct a successful health coaching practice.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div data-component="image" href="javascript: void(0);">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319547_1587509333263New_CWC_books.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319547_1587509333263New_CWC_books.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 259px; max-width: 100%; height: auto;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319547_1587509333263New_CWC_books.jpg">
                                    </picture>
                                </div>




                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-large"
                style="background-color: rgb(255, 255, 255); padding: 40px 0px 20px;" id="_f483x6cg0">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="js_kartra_component_holder">

                                <div data-component="image" href="javascript: void(0);" id="ozLlFw8aym">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto; opacity: 1;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
                                    </picture>
                                </div>
                                <div data-component="divider" id="ljsILNwHTQ">
                                    <hr class="kartra_divider kartra_divider--border-small kartra_divider--border-gold kartra_divider--margin-bottom-medium pull-center kartra_divider--small"
                                        style="border-color: rgb(255, 202, 41); border-top-style: solid; border-top-width: 5px; margin: 25px auto 30px;">
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--text-center kartra_headline--size-sm-giant kartra_headline--black kartra_headline--font-weight-bold kartra_headline--margin-bottom-tiny"
                                        style="position: relative; margin-top: 0px; margin-bottom: 20px;">
                                        <h2><span><strong><span style="font-size: 2rem;">Over 70 Years of Combined
                                                        Expertise and Experience!</span></strong></span></h2>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="icon" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--dark kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto 25px;"><span
                                            style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-quote-right"></span></div>
                                </div>
                                <div data-component="text" id="g0gOXZcK8V">
                                    <div class="kartra_text kartra_text--extra-small kartra_text--text-center kartra_text--dim-grey kartra_text--line-height-large kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>A physician, #1 bestselling author, and speaker who has dedicated over
                                            27 years to positively changing healthcare outcomes both nationally and
                                            internationally. She holds active memberships in the American Academy of
                                            Lifestyle Medicine and the American Medical Association.</p>
                                    </div>
                                </div>
                                <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        class="background-item background_changer--blur0 js-bg-next-gen"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195003_1587091962380DRCooper-image.png")'>
                                    </div>
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--h4 kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>Dr. Dona Cooper-Dockery</p>
                                    </div>
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-medium kartra_text--font-weight-medium kartra_text--royal-blue kartra_text--text-center"
                                        style="position: relative;">
                                        <p><span style="color:#FF8C00;">Founder, <em><span
                                                        style="color: rgb(255, 140, 0);">Cooper Internal
                                                        Medicine</span></em></span></p>
                                    </div>
                                </div>
                                <div data-component="divider">
                                    <hr
                                        class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-black-transparent-near-grey-extra-giant kartra_divider--small kartra_divider--margin-bottom-tiny pull-center">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="icon" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--dark kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto 25px;"><span
                                            style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-quote-right"></span></div>
                                </div>
                                <div data-component="text" id="2TsS4L2f8H">
                                    <div class="kartra_text kartra_text--extra-small kartra_text--text-center kartra_text--dim-grey kartra_text--line-height-large kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>He's a physician who loves and greatly cares for his patients and has
                                            lectured around the world. Dr. Bryce is also an adjunct assistant clinical
                                            professor of medicine who understands the need for a holistic approach to
                                            health. He's also a community leader and leads many local health
                                            initiatives.</p>
                                    </div>
                                </div>
                                <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        class="background-item background_changer--blur0 js-bg-next-gen"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195131_1587092361176Dr.-Errol-Bryce.jpeg")'>
                                    </div>
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--h4 kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>Dr. Errol Bryce, FACP</p>
                                    </div>
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-medium kartra_text--font-weight-medium kartra_text--royal-blue kartra_text--text-center"
                                        style="position: relative;">
                                        <p>
                                            <font color="#ff8c00">Physician, Adjunct Clinical Professor</font>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="divider">
                                    <hr
                                        class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-black-transparent-near-grey-extra-giant kartra_divider--small kartra_divider--margin-bottom-tiny pull-center">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-4 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div data-component="icon" href="javascript: void(0);">
                                    <div class="kartra_icon kartra_icon--dark kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto 25px;"><span
                                            style="color: rgb(243, 113, 33);"
                                            class="kartra_icon__icon fa fa-quote-right"></span></div>
                                </div>
                                <div data-component="text" id="pvqGBKMj8H">
                                    <div class="kartra_text kartra_text--extra-small kartra_text--text-center kartra_text--dim-grey kartra_text--line-height-large kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>A sought-after speaker on health and wellness. He holds a Ph.D. in Medical
                                            Genetics and Nutritional Biochemistry, and M.S. in Biology with emphasis in
                                            Cellular Physiology. He's the founder and CEO of
                                            Health Speaks Wellness Seminars, and SaLUNaT Consultancy. </p>
                                    </div>
                                </div>
                                <div class="kartra_element_bg kartra_element_bg--thumb-size-large kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        class="background-item background_changer--blur0 js-bg-next-gen"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9827097_1589242483459Tamayo1.jpg")'>
                                    </div>
                                </div>
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--montserrat-font kartra_headline--h4 kartra_headline--font-weight-bold kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>E. Jerry Tamayo, Ph.D.</p>
                                    </div>
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-medium kartra_text--font-weight-medium kartra_text--royal-blue kartra_text--text-center"
                                        style="position: relative;">
                                        <p><span style="color:#FF8C00;">Univ. Professor and Speaker</span></p>
                                    </div>
                                </div>
                                <div data-component="divider">
                                    <hr
                                        class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-black-transparent-near-grey-extra-giant kartra_divider--small kartra_divider--margin-bottom-tiny pull-center">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-medium content--padding-bottom-none content--padding-top-none"
                style="background-color: #ffffff;" id="_ukryi6gp5">
                <div class="background_changer"></div>
                <div class="background_changer_overlay"></div>
                <div>
                    <div>
                        <div class="row row--margin-left-right-none" data-component="grid">
                            <div class="col-md-12 column--padding-none">
                                <div class="js_kartra_component_holder">
                                    <div data-component="image" href="javascript: void(0);">
                                        <picture>
                                            <source type="image/webp"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9197948_1587103031619Medium-CWC-logo.webp">
                                            </source>
                                            <source type="image/png"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9197948_1587103031619Medium-CWC-logo.png">
                                            </source><img
                                                class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                                style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; opacity: 1; width: 194px; max-width: 100%; height: auto;"
                                                data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9197948_1587103031619Medium-CWC-logo.png">
                                        </picture>
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
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                                href="https://app.kartra.com/redirect_to/?asset=page&amp;id=oL0qChRyJ3Yf"
                                                data-frame-id="_ukryi6gp5"
                                                style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                                data-project-id="3" data-page-id="107" target="_parent">ABOUT US</a>
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                                href="https://app.kartra.com/redirect_to/?asset=page&amp;id=5wFvhuon6coQ"
                                                data-frame-id="_ukryi6gp5"
                                                style='color: rgb(255, 255, 255); font-weight: 400; font-family: "Open Sans";'
                                                data-project-id="3" data-page-id="112" target="_parent">CONTACT US</a>
                                            <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                                href="https://app.kartra.com/redirect_to/?asset=page&amp;id=N9vYn5qZVgrK"
                                                data-frame-id="_ukryi6gp5"
                                                style='color: rgb(250, 250, 250); font-weight: 400; font-family: "Open Sans";'
                                                data-project-id="3" data-page-id="4" target="_parent">PRIVACY
                                                POLICY</a><a
                                                class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--light-black kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                                href="https://app.kartra.com/redirect_to/?asset=page&amp;id=lH30MsL8pv9c"
                                                data-frame-id="_ukryi6gp5"
                                                style='color: rgb(250, 250, 250); font-weight: 400; font-family: "Open Sans";'
                                                data-project-id="3" data-page-id="5" target="_parent">TERMS OF USE</a>

                                        </div>
                                    </div>
                                </div>
                                <div class="column--vertical-center col-md-6">
                                    <div class="js_kartra_component_holder js_kartra_component_holder--height-auto">

                                        <div data-component="text">
                                            <div class="kartra_text kartra_text--open-sans-font kartra_text--font-weight-regular kartra_text--dim-black kartra_text--text-right kartra_text--sm-text-center"
                                                style="position: relative;" aria-controls="cke_56"
                                                aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
                                                <p style="font-size: 16px; font-family: Georgia;"><span
                                                        style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;">Copyright
                                                        © 2024 by <strong>Cooper Wellness Center</strong><b><span
                                                                style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;"><span
                                                                    style="color: rgb(255, 255, 255); font-family: roboto; font-size: 16px;"> </span></span></b>| All
                                                        Rights Reserved.</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade popup_modal popup-modal js_trackable_wrapper" id="popup_exit"
                data-trigger="mouseaway" data-reocur="first" role="dialog" aria-hidden="true">
                <button type="button" class="closer close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="content  content--popup-large" id="_yp2zepbeq">
                                <div class="background_changer"></div>
                                <div class="background_changer_overlay"></div>
                                <div
                                    class="container-fluid page-popup-container--large page-popup-container--column-double">
                                    <div class="row row--equal" data-component="grid">
                                        <div class="col-md-6 column--vertical-center column--padding-special-medium">
                                            <div style="background-color: rgb(245, 245, 245);" class="background-item">
                                            </div>
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline">
                                                    <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h4 kartra_headline--font-weight-regular kartra_headline--margin-bottom-extra-tiny"
                                                        style="position: relative; margin-top: 0px; margin-bottom: 5px;">
                                                        <p><span style="color:#008000;">DON'T LEAVE JUST YET...</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div data-component="headline">
                                                    <div class="kartra_headline kartra_headline--black kartra_headline--merriweather-font kartra_headline--h1 kartra_headline--font-weight-bold kartra_headline--margin-bottom-tiny"
                                                        style="position: relative;">
                                                        <p style="font-size: 1.2rem;"><span
                                                                style="font-size:1.20rem;">You wouldn't want to miss out
                                                                on all this!</span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider">
                                                    <hr
                                                        class="kartra_divider kartra_divider--small kartra_divider--border-extra-tiny kartra_divider--border-full-transparent-black kartra_divider--align-left">
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--light-grey kartra_text--font-weight-regular kartra_text--margin-bottom-extra-medium kartra_text--open-sans-font"
                                                        style="position: relative;">
                                                        <p><em>Health coaches are needed now more than ever
                                                                before! </em>Society is poised for a major health
                                                            revolution. You can choose from 3 options:</p>
                                                    </div>
                                                </div>
                                                <div data-component="list">
                                                    <ul class="kartra_list kartra_list--margin-bottom-extra-medium">
                                                        <li
                                                            class="kartra_list__item kartra_list__item--margin-bottom-semi-tiny kartra_list__item--table">
                                                            <div
                                                                class="kartra_icon kartra_icon--top-none kartra_icon--tiny kartra_icon--light-grey">
                                                                <span style="color: #1d85b1"
                                                                    class="kartra_icon__icon fa fa-chevron-right"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                                                <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--light-grey kartra_item_info__text--open-sans-font kartra_item_info__text--font-weight-regular"
                                                                    style="position: relative;">
                                                                    <p><strong>BASIC LEVEL</strong></p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li
                                                            class="kartra_list__item kartra_list__item--margin-bottom-semi-tiny kartra_list__item--table">
                                                            <div
                                                                class="kartra_icon kartra_icon--top-none kartra_icon--tiny kartra_icon--light-grey">
                                                                <span style="color: #1d85b1"
                                                                    class="kartra_icon__icon fa fa-chevron-right"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                                                <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--light-grey kartra_item_info__text--open-sans-font kartra_item_info__text--font-weight-regular"
                                                                    style="position: relative;">
                                                                    <p><strong>INTERMEDIATE LEVEL</strong></p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li
                                                            class="kartra_list__item kartra_list__item--margin-bottom-semi-tiny kartra_list__item--table">
                                                            <div
                                                                class="kartra_icon kartra_icon--top-none kartra_icon--tiny kartra_icon--light-grey">
                                                                <span style="color: #1d85b1"
                                                                    class="kartra_icon__icon fa fa-chevron-right"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
                                                                <div class="kartra_item_info__text kartra_item_info__text--size-extra-medium kartra_item_info__text--light-grey kartra_item_info__text--open-sans-font kartra_item_info__text--font-weight-regular"
                                                                    style="position: relative;">
                                                                    <p><strong>ADVANCED LEVEL</strong></p>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                                <div data-component="button">
                                                    <a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=zp7QjqlNKPrK"
                                                        class="kartra_button1 kartra_button1--bg-steel-blue-02 kartra_button1--shadow-04 kartra_button1--merriweather-font kartra_button1--font-weight-bold kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-small pull-center toggle_pagelink "
                                                        data-frame-id="_yp2zepbeq"
                                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto; font-weight: 400; font-family: Roboto;"
                                                        data-project-id="3" data-page-id="36" target="_parent">Take Me
                                                        Back!</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 column--vertical-center column--padding-special-medium column--padding-top-medium"
                                            style="margin-top: 0px; margin-bottom: 0px; padding: 50px 30px 30px;">
                                            <div style="background-color: rgb(49, 85, 40); border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none rgb(51, 51, 51); background-image: none; opacity: 1;"
                                                class="background-item background_changer--blur0"></div>
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline">
                                                    <div class="kartra_headline kartra_headline--text-center kartra_headline--white kartra_headline--h3 kartra_headline--merriweather-font kartra_headline--font-weight-bold"
                                                        style="position: relative; margin-top: 0px; margin-bottom: 10px;">
                                                        <p>Plus</p>
                                                    </div>
                                                </div>
                                                <div data-component="divider">
                                                    <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-half-transparent-white pull-center kartra_divider--small"
                                                        style="border-color: rgb(243, 113, 33); border-top-style: solid; border-top-width: 2px; margin: 0px 145px;">
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--text-center kartra_text--white kartra_text--font-weight-regular kartra_text--margin-bottom-extra-medium kartra_text--open-sans-font"
                                                        style="position: relative; margin-top: 0px; margin-bottom: 30px;">
                                                        <p> </p>

                                                        <p style="font-size: 1rem;"><span
                                                                style="font-size:1.00rem;">Don't forget about these
                                                                extras:</span></p>

                                                        <p style="font-size: 1rem;"> </p>

                                                        <p style="font-size: 1rem;"><span style="font-size:1.00rem;">*
                                                                Study guides &amp; quizzes.</span></p>

                                                        <p style="font-size: 1rem;"><span style="font-size:1.00rem;">*
                                                                Lesson handouts.</span></p>

                                                        <p style="font-size: 1rem;"><span style="font-size:1.00rem;">*
                                                                In-depth multimedia.</span></p>

                                                        <p style="font-size: 1rem;"><span style="font-size:1.00rem;">*
                                                                And much, much, more!</span></p>
                                                    </div>
                                                </div>
                                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--laptop-mock-up"
                                                    data-component="bundle"
                                                    style="margin-top: 0px; margin-bottom: 0px; padding: 0px;">
                                                    <div style="background-color: rgba(0, 0, 0, 0); border-top-left-radius: 0px; border-top-right-radius: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px; border: 0px none rgb(51, 51, 51); opacity: 1;"
                                                        class="background-item background_changer--blur0 js-bg-next-gen"
                                                        data-bg='url("https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/10000412_1589854052547CWC-staff-team2.jpg")'>
                                                    </div>
                                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        class="laptop-mock-up--frame"
                                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-macbook-white.png">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="page-popup-footer-powered-by page-popup-footer-powered-by--text-right">
                                    <p>Powered by <strong>KARTRA</strong></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div rel="bvipDPMuGqZa" article="" product="" embedded="1" id="kartra_live_chat"
                class="kartra_helpdesk_sidebar js_kartra_trackable_object">
                <script type="text/javascript" src="https://app.kartra.com/resources/js/helpdesk_frame"></script>
                <link rel="stylesheet" type="text/css"
                    href="https://app.kartra.com/css/new/css/kartra_helpdesk_sidebar_out.css" />
                <div rel="bvipDPMuGqZa" id="display_kartra_helpdesk" class="kartra_helpdesk_sidebar_button open">
                </div>
            </div>
        </div>
        <script async src="https://app.kartra.com/resources/js/checkout_init"></script>
        <script async src="https://app.kartra.com/js/santitation_for_naked_checkout.js"></script>
        <link rel="stylesheet"
            href="https://app.kartra.com/css/new/css/v5/stylesheets_frontend/checkout/minimalistic/checkout_page.css"
            class="builder-conditional-asset" />
    </div>
    <!-- /#page -->
    <div style="height:0px;width:0px;opacity:0;position:fixed">
        <script>!function(){function e() { var e = ((new Date).getTime(), document.createElement("script")); e.type = "text/javascript", e.async = !0, e.setAttribute("embed-id", "e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf"), e.src = "https://embed.adabundle.com/embed-scripts/e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf"; var t = document.getElementsByTagName("script")[0]; t.parentNode.insertBefore(e, t) }var t=window;t.attachEvent?t.attachEvent("onload",e):t.addEventListener("load",e,!1)}();</script>

        <img src="https://app.kartra.com/button/tc/bee4d5df2508a5ea4ea185b89d24a016"
            style="border:0px; width:0px; height: 0px;" />
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
    <script src="//app.kartra.com/resources/js/page_check?page_id=zp7QjqlNKPrK" async defer></script>
    <script>
    if ( typeof window['jQuery'] !== 'undefined') {
        window.jsVars = { "page_title": "Certified Health Coach Order", "page_description": "Health coaches are in big demand! Start your career now without sacrificing 3-4 years of your life to get certified. Enroll now.", "page_keywords": "", "page_robots": "index, follow", "secure_base_url": "\/\/app.kartra.com\/", "global_id": "zp7QjqlNKPrK" };
    window.global_id = 'zp7QjqlNKPrK';
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
                                    data-type-id="36" data-type-owner="DpwDQa6g">
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