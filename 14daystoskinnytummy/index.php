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
	<title>14-Day Skinny Tummy Challenge</title>
	<meta name="description"
		content="Transform your health and vitality and get the end-of-year body you truly deserve and long for. Finish the year strong and fabulous!">
	<meta name="keywords"
		content="14-day fitness challenge, weight-loss challenge, tummy challenge, shed fat fast, 14 days to amazing health, skinny challenge, skinny tummy challenge, burn fat fast, shed pounds, holiday weight-loss, holiday health challenge, holiday fitness challenge">
	<meta name="robots" content="index, follow">
	<link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="author" content="">
	<meta property="og:title" content="">
	<meta property="og:description" content="">
	<meta property="og:image"
		content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/376791092554Purple_and_Yellow_Geometric_Business_Consulting_Services_Instagram_Post__Medium_Banner__US___Landscape____1_.png">

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
		href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
		as="style" onload="this.onload=null;this.rel='stylesheet'">
	<link rel="stylesheet" href="css/new_bootstrap.css">

	<link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

	<noscript>
		<link rel="stylesheet" href="css/kartra_components.css">
		<link rel="stylesheet" href="css/font-awesome.css">
		<link type="text/css" rel="stylesheet"
			href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
	</noscript>

	<script>
		/*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
		(function (w) { "use strict"; if (!w.loadCSS) { w.loadCSS = function () { } } var rp = loadCSS.relpreload = {}; rp.support = function () { var ret; try { ret = w.document.createElement("link").relList.supports("preload") } catch (e) { ret = false } return function () { return ret } }(); rp.bindMediaToggle = function (link) { var finalMedia = link.media || "all"; function enableStylesheet() { link.media = finalMedia } if (link.addEventListener) { link.addEventListener("load", enableStylesheet) } else if (link.attachEvent) { link.attachEvent("onload", enableStylesheet) } setTimeout(function () { link.rel = "stylesheet"; link.media = "only x" }); setTimeout(enableStylesheet, 3e3) }; rp.poly = function () { if (rp.support()) { return } var links = w.document.getElementsByTagName("link"); for (var i = 0; i < links.length; i++) { var link = links[i]; if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) { link.setAttribute("data-loadcss", true); rp.bindMediaToggle(link) } } }; if (!rp.support()) { rp.poly(); var run = w.setInterval(rp.poly, 500); if (w.addEventListener) { w.addEventListener("load", function () { rp.poly(); w.clearInterval(run) }) } else if (w.attachEvent) { w.attachEvent("onload", function () { rp.poly(); w.clearInterval(run) }) } } if (typeof exports !== "undefined") { exports.loadCSS = loadCSS } else { w.loadCSS = loadCSS } })(typeof global !== "undefined" ? global : this);

		window.global_id = 'DFXHG1pE9bZa';
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
	<link rel="preconnect" href="//vip.timezonedb.com">
	<link rel="dns-prefetch" href="//vip.timezonedb.com">
</head>

<body>
	<div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object"
		data-kt-type="kartra_page_tracking" data-kt-value="DFXHG1pE9bZa" data-kt-owner="DpwDQa6g">
	</div>
	<div id="page" class="page container-fluid">
		<div id="page_background_color" class="row">
			<div class="content content--lsp--1" style="padding: 0px 0px 40px; background-color: rgb(255, 255, 255);"
				id="_up1qmlae8">
				<style id="pagesInternalCSS">
					.kartra_headline--quicksand-font {
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_headline--pt-sans-font {
						font-family: 'PT Sans', sans-serif;
					}

					.kartra_headline--lsp-1-size-giant {
						font-size: 2.8rem;
						text-shadow: 0px 3px 15px rgba(0, 0, 0, .15);
					}

					.kartra_headline--lsp-1-size-extra-medium {
						font-size: 1.95rem
					}

					.kartra_headline--lsp-1-size-medium {
						font-size: 1.64rem
					}

					.kartra_headline--lsp-1-size-special-medium {
						font-size: 1.57rem;
						line-height: 120%;
					}

					.kartra_headline--lsp-1-size-big-small {
						font-size: 1.1rem
					}

					.kartra_headline--lsp-1-size-small {
						font-size: 1.02rem
					}

					.kartra_headline--lsp-1-size-extra-small {
						font-size: 0.86rem
					}

					.kartra_headline--lsp-1-size-big-tiny {
						font-size: 0.82rem
					}

					.kartra_headline--lsp-1-size-tiny {
						font-size: 0.7rem
					}

					.kartra_text--lsp-1--size-large {
						font-size: 0.86rem;
					}

					.kartra_text--lsp-1--size-extra-medium {
						font-size: 0.785rem;
					}

					.kartra_text--lsp-1--size-small {
						font-size: 0.7rem;
					}

					.kartra_item_info__text--lsp-1--size-medium {
						font-size: 0.7rem;
					}

					.kartra_divider--lsp-1-small.kartra_divider--small {
						width: 170px;
					}

					.kartra_item_info--lsp-1--size-medium {
						width: 50px;
					}

					.content--lsp--1 .kartra_video--player_1 {
						box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
					}

					.kartra_button11--squared.kartra_button11--lsp-1-two-line-btn {
						background-image: none;
						border-radius: 10px;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_first_line {
						font-size: 40px;
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_second_line {
						font-size: 25px;
					}

					.content--lsp--1.kartra_video--player_1 {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .3);
					}

					.kartra_icon--lsp-title-icn.kartra_icon--large {
						width: 75px;
						height: 75px;
						font-size: 45px;
					}

					.kartra_element_bg--lsp-box-style-one {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .07);
						border-radius: 6px;
					}

					.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
						width: 150px;
						height: 150px;
						font-size: 72px;
					}

					.js_kartra_component_holder--height-auto {
						min-height: auto;
					}

					.background-item__arrow--left-arrow-top {
						position: absolute;
						top: -7px;
						left: 35px;
						margin-left: -7px;
						width: 14px;
						height: 14px;
						background: inherit;
						transform: rotate(45deg);
					}

					.kartra_image--lsp-1-testimonoial {
						width: 70px;
					}

					.kartra_element_bg--testimonial-box {
						box-shadow: 0px 5px 20px rgba(0, 0, 0, .1);
						border-radius: 12px;
					}

					.kartra_element_bg--dot-sign {
						width: 8px;
						height: 8px;
						padding: 0px !important;
						position: relative;
					}

					.kartra_element_bg--who-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_element_bg--who-box .background-item {
						background-size: inherit !important;
						background-repeat: no-repeat;
						background-position: right bottom;
					}

					.kartra_icon--who-area.kartra_icon--giant {
						width: 56px;
						height: 56px;
					}

					.kartra_icon--who-area.kartra_icon--giant.kartra_icon--circled {
						width: 92px;
						height: 92px;
					}

					.kartra_element_bg--who-call-top-action {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_headline_block__info--padding-left-small {
						padding-left: 25px;
					}

					.kartra_icon--lsp-giant.kartra_icon--giant {
						font-size: 65px;
						width: 105px;
						height: 105px;
					}

					.kartra_element_bg--feature-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_icon--pricing-med-icn.kartra_icon--medium {
						font-size: 30px;
					}

					.kartra_button1--price-btn.kartra_button1 {
						font-size: 20px;
						font-family: 'Quicksand', sans-serif;
						border-radius: 8px;
						padding: 16px;
						box-shadow: inset 0 -3px 0px rgba(0, 0, 0, 0.2), 0px 5px 30px rgba(0, 0, 0, .1);
					}

					.kartra_element_bg--lsp-pricing {
						box-shadow: 0px 5px 30px rgba(0, 0, 0, .05);
						border-radius: 10px;
					}

					.kartra_divider--border-tiny.kartra_divider--small {
						width: 170px;
					}

					/*Accordion*/
					.lsp-1--accordion.accordion-3 .panel {
						border: 2px solid #f5f5f5;
						border-radius: 10px;
						box-shadow: 0px 3px 10px rgba(0, 0, 0, .03)
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						color: #424242;
						font-size: 20px;
						font-weight: 500;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-title {
						border: 15px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon {
						border-top: 17px solid transparent;
						border-bottom: 17px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element {
						border: 0px;
						color: rgb(255, 76, 0);
						font-size: 22px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element i:before {
						content: "\f063";
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading:hover {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading+.panel-collapse>.panel-body,
					.accordion-3 .panel .panel-heading+.panel-collapse>.list-group {
						border-top: 1px solid #eee;
						margin: 0px 30px;
						padding: 24px 0px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-body p {
						color: #37474e;
						font-size: 18px;
						line-height: 140%;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						padding: 10px 15px;
					}

					.lsp-1--accordion.accordion-3 .panel+.panel {
						margin-top: 25px;
					}

					.kartra_element_bg--thumb-size-extra-medium {
						width: 70px;
						height: 70px;
					}

					.kartra_testimonial_author_block--padding-bottom-extra-small {
						padding-bottom: 20px;
					}

					@media(max-width: 991px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.2rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 0.85rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 0.9rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 0.85rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 0.85rem;
						}

						.column--sm-padding-bottom-none,
						.column--sm-padding-bottom-none .background-item {
							padding-bottom: 0px !important;
						}

						.column--sm-padding-top-extra-medium {
							padding-top: 40px !important;
						}

						.row--sm-margin-top-none {
							margin-top: 0px !important;
						}

						.column--sm-height-auto {
							min-height: auto !important;
						}

						.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
							width: 100px;
							height: 100px;
							font-size: 50px;
						}

						.kartra_text--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.row--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.sm-pull-left {
							float: left !important;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.1rem;
						}

						.content--sm-padding-bottom-none,
						.content--sm-padding-bottom-none .background_changer {
							padding-bottom: 0px !important;
						}

						.content--sm-padding-top-none,
						.content--sm-padding-top-none .background_changer {
							padding-top: 0px !important;
						}
					}

					@media(max-width: 767px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.15rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.05rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.2rem;
						}

						.kartra_item_info--lsp-1--size-medium {
							width: 30px;
						}

						.kartra_item_info__headline--xs-margin-top-negative-extra-tiny {
							margin-top: -5px !important;
						}

						.kartra_headline_block--flex {
							display: block !important;
						}

						.kartra_element_bg--who-call-top-action,
						.kartra_element_bg--who-call-top-action .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_element_bg--feature-box,
						.kartra_element_bg--feature-box .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_headline_block__info--padding-left-small {
							padding-left: 0px !important;
						}

						.kartra_text--xs-text-center {
							text-align: center !important;
						}

						.kartra_headline--xs-text-center {
							text-align: center !important;
						}
					}

					@media(max-width: 480px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1.15rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.3rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.2rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1.25rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1.25rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.3rem;
						}

						.kartra_testimonial__author_details--padding-left-extra-small {
							padding-left: 20px !important;
						}

						.kartra_item_info__headline--xxs-negative-margin-top-big-tiny {
							margin-top: -8px !important;
						}
					}

					@media (max-width: 767px) {
						.kartra_text--xs-text-center {
							text-align: inherit;
						}

						.kartra_headline--xs-text-center {
							text-align: inherit;
						}

						.kartra_element_bg--xs-padding-left-right-special-medium {
							padding-left: 30px !important;
							padding-right: 30px !important;
						}

						.kartra_text--xs-text-center-important {
							text-align: center;
						}

						.content--xs-padding-bottom-none-important {
							padding-bottom: 0px !important;
						}
					}
				</style>
				<div class="background_changer background_changer--blur0" style="opacity: 1; background-image: none;">
				</div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div>
					<div class="row row--margin-left-right-none row--equal" data-component="grid"
						id="accordion-41WAgukyRP">
						<div class="col-md-12 column--sm-padding-bottom-none"
							style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 40px 15px 480px; background-image: none; margin: 0px;">
							<div style="background-color: rgb(19, 170, 36); border-radius: 0px; padding: 40px 15px 480px;  opacity: 1; box-shadow: 0px 0px 0px 0px;"
								class="background-item background_changer--blur0 js-bg-next-gen" alt=""
								data-bg='url("//d11n7da8rpqbjy.cloudfront.net/Kartra/8574689_1584709149250bg-32_transparent_2.png")'>
							</div>
							<div>
								<div class="container">
									<div class="row" data-component="grid" id="accordion-41WAgukyRP">
										<div class="col-md-12">
											<div class="js_kartra_component_holder">
												<div data-component="countdown">
													<div class="countdown-section countdown-section--text-center countdown-section--margin-bottom-extra-small"
														data-countdown-id="OdEroXeH2c"
														style="margin-top: 0px; margin-bottom: 20px;"
														data-countdown="fixed" data-date="12/08/2024" data-time="23:59"
														data-asset-id="0" data-timestamp="1733723940">
														<div
															class="countdown countdown--flex countdown--justify-content-center">
															<div
																class="countdown__item countdown__item--element-box-01 countdown__item--day countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
																<div>
																	<span
																		class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">DAYS</span>
																</div>
																<div
																	class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
																	<div class="digit-list__item">0</div>
																	<div class="digit-list__item">0</div>
																</div>
															</div>
															<div
																class="countdown__item countdown__item--element-box-01 countdown__item--hours countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
																<div>
																	<span
																		class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">HOURS</span>
																</div>
																<div
																	class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
																	<div class="digit-list__item">0</div>
																	<div class="digit-list__item">0</div>
																</div>
															</div>
															<div
																class="countdown__item countdown__item--element-box-01 countdown__item--minutes countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
																<div>
																	<span
																		class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">MINUTES</span>
																</div>
																<div
																	class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
																	<div class="digit-list__item">0</div>
																	<div class="digit-list__item">0</div>
																</div>
															</div>
															<div
																class="countdown__item countdown__item--element-box-01 countdown__item--seconds countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
																<div>
																	<span
																		class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">SECONDS</span>
																</div>
																<div
																	class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
																	<div class="digit-list__item">0</div>
																	<div class="digit-list__item">0</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div data-component="image" id="jdgcaANPPt">
													<img class="kartra_image kartra_image--margin-bottom-none kartra_image--max-width-full pull-center background_changer--blur0"
														src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
														style="opacity: 1; margin: 0px auto 20px; border-radius: 0px;"
														alt=""
														data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/376791092554Purple_and_Yellow_Geometric_Business_Consulting_Services_Instagram_Post__Medium_Banner__US___Landscape____1_.png">
												</div>
												<div data-component="headline" id="N0XePuX0wL">
													<div class="kartra_headline kartra_headline--quicksand-font kartra_headline--lsp-1-size-giant kartra_headline--text-center kartra_headline--white"
														style="position: relative;" aria-controls="cke_56"
														aria-activedescendant="" aria-autocomplete="list"
														aria-expanded="false">
														<p style="line-height: 1.2em;"><b><span
																	style="font-family: Roboto; line-height: 1.2em;">Join
																	the 14-Day Skinny Tummy Challenge!</span></b></p>
													</div>
												</div>
												<div data-component="text" id="accordion-u4Z6VMxH2R">
													<div class="kartra_text kartra_text--white kartra_text--text-center kartra_text--lsp-1--size-large kartra_text--font-weight-regular"
														style="margin: 0px 0px 50px; position: relative;"
														aria-controls="cke_2207" aria-activedescendant=""
														aria-autocomplete="list" aria-expanded="false">
														<p>Elevate your wellness with our exclusive 14-Day Skinny Tummy
															Challenge, designed to help you achieve a rejuvenated
															you<strong> in just two weeks</strong>. Embark on an
															incredible journey towards transformative health and
															well-being<b> (and stun family and friends for the holidays
															</b>😉<b>)!</b></p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div>
						<div class="container">
							<div class="row row--sm-margin-top-none" data-component="grid" id="accordion-41WAgukyRP"
								style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: -480px; margin-bottom: 0px; background-image: none;">
								<div class="col-md-12 column--sm-padding-top-extra-medium">
									<div class="js_kartra_component_holder">
										<div data-component="video" id="accordion-07fMfJYPCr"
											data-thumbnail="https://d2uolguxr56s4e.cloudfront.net/img/shared/kartra_logo_color.svg"
											data-screenshot="false">
											<div class="kartra_video kartra_video--player_1 kartra_video_containerCoa1Ss9f83a4 js_kartra_trackable_object"
												style="margin: 0px 0px 50px; border-radius: 0px; padding-bottom: 56.25%;"
												data-kt-type="video" data-kt-value="Coa1Ss9f83a4"
												data-kt-owner="DpwDQa6g"
												id="Coa1Ss9f83a4/gfebb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=false"
												data-random_str="gfebb">
												<script
													src="https://app.kartra.com/video/Coa1Ss9f83a4/gfebb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=false"></script>
											</div>
										</div>
										<div data-component="button" id="i3TLzbjdmv"><a href="javascript:void(0);" onclick="openPaymentSelection()"
												class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
												style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: Lato; border-radius: 10px;"
												target="_parent"><span class="kartra_button11_text"
													style="font-weight: 700;"><span
														class="kartra_button11_text_first_line"
														style="font-weight: 700;">SIGN UP NOW!</span><span
														class="kartra_button11_text_second_line"
														style="font-weight: 700;">for only $49.99</span></span></a>
										</div>
										<!-- Payment Selection Modal -->
										<div id="paymentSelection" class="modal"
                                    		style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
											<div class="modal-content"
												style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
												<button onclick="closePaymentSelection()"
													style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;
												</button>
												<h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
										
												<!-- Tab Navigation -->
												<div style="display: flex; justify-content: space-around; margin-top: 20px;">
													<button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; color: white; width: 50%;">Pay with Mobile Money</button>
													<button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
												</div>
												
												<!-- Credit Card Tab Content -->
												<div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
													<p>Complete your payment with Credit Card or PayPal:</p>
													<div data-component="button" id="i3TLzbjdmv"><a href="javascript:void(0);"
														class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object dg_popup"
														style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: Lato; border-radius: 10px;"
														data-frame-id="_up1qmlae8" data-kt-layout="1" data-kt-type="checkout"
														data-kt-owner="DpwDQa6g"
														data-kt-value="12a0269ef7610cbde49b29632c6d5ac7" data-funnel-id="511307"
														data-product-id="511307"
														data-price-point="12a0269ef7610cbde49b29632c6d5ac7"
														rel="12a0269ef7610cbde49b29632c6d5ac7" data-asset-id="4"
														target="_parent"><span class="kartra_button11_text"
															style="font-weight: 700;"><span
																class="kartra_button11_text_first_line"
																style="font-weight: 700;">SIGN UP NOW!</span><span
																class="kartra_button11_text_second_line"
																style="font-weight: 700;">for only $49.99</span></span></a>
													</div>
												</div>

												<!-- Mobile Money Tab Content -->
												<div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
													<p>Complete your payment with Mobile Money:</p>
													<button onclick="openPaymentPopup('5')"
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
													<input type="text" id="name" value="<?php echo htmlspecialchars($_SESSION['customer_name'])?>" required
														style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

													<label for="mail" style="font-weight: bold; color: #555;">Email:</label>
													<input type="email" id="mail" value="<?php echo htmlspecialchars($_SESSION['customer_email'])?>" required
														style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

													<label for="phone" style="font-weight: bold; color: #555;">Phone
														Number:</label>
													<input type="text" id="phone" value="<?php echo htmlspecialchars($_SESSION['customer_phone'])?>" required
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


										<div data-component="countdown">
											<div class="countdown-section countdown-section--text-center countdown-section--margin-bottom-extra-small"
												data-countdown-id="BFGBHMgGBb"
												style="margin-top: 0px; margin-bottom: 20px;" data-countdown="fixed"
												data-date="12/08/2024" data-time="23:58" data-asset-id="1"
												data-timestamp="1733723880">
												<div
													class="countdown countdown--flex countdown--justify-content-center">
													<div
														class="countdown__item countdown__item--element-box-01 countdown__item--day countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
														<div>
															<span
																class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">DAYS</span>
														</div>
														<div
															class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
															<div class="digit-list__item">0</div>
															<div class="digit-list__item">0</div>
														</div>
													</div>
													<div
														class="countdown__item countdown__item--element-box-01 countdown__item--hours countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
														<div>
															<span
																class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">HOURS</span>
														</div>
														<div
															class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
															<div class="digit-list__item">0</div>
															<div class="digit-list__item">0</div>
														</div>
													</div>
													<div
														class="countdown__item countdown__item--element-box-01 countdown__item--minutes countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
														<div>
															<span
																class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">MINUTES</span>
														</div>
														<div
															class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
															<div class="digit-list__item">0</div>
															<div class="digit-list__item">0</div>
														</div>
													</div>
													<div
														class="countdown__item countdown__item--element-box-01 countdown__item--seconds countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
														<div>
															<span
																class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">SECONDS</span>
														</div>
														<div
															class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
															<div class="digit-list__item">0</div>
															<div class="digit-list__item">0</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div data-component="video" id="accordion-07fMfJYPCr"
											data-thumbnail="https://d2uolguxr56s4e.cloudfront.net/img/shared/kartra_logo_color.svg"
											data-screenshot="false">
											<div class="kartra_video kartra_video--player_1 kartra_video_container3LhEQX0sMCdT js_kartra_trackable_object"
												style="margin: 0px 0px 50px; border-radius: 0px; padding-bottom: 56.25%;"
												data-kt-type="video" data-kt-value="3LhEQX0sMCdT"
												data-kt-owner="DpwDQa6g"
												id="3LhEQX0sMCdT/qfebb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=false"
												data-random_str="qfebb">
												<script
													src="https://app.kartra.com/video/3LhEQX0sMCdT/qfebb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=false"></script>
											</div>
										</div>
										
										<div data-component="button" id="WnGLeULqG0"><a href="javascript:void(0);" onclick="openPaymentSelection()"
												class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
												style="background-color: rgb(61, 170, 19); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 400; font-family: Lato; border-radius: 10px;"
												target="_parent"><span class="kartra_button11_text"
													style="font-weight: 400;"><span
														class="kartra_button11_text_first_line"
														style="font-weight: 400;">REGISTRATE AHORA!</span><span
														class="kartra_button11_text_second_line"
														style="font-weight: 400;">(solamente $49.99)</span></span></a>
										</div>
										<div data-component="image" id="St3BDP2TAj">
											<img class="kartra_image pull-center kartra_image--full"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
										</div>
										<div class="social_share_wrapper pull-center" data-component="bundle"
											id="gp81rswDQC">
											<div data-component="social_share">
												<span
													class="facebook_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-facebook kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
														class="kartra_icon__icon fa fa-facebook-square"></span><span
														class="social_share__text">Share</span></span>
											</div>
											<div data-component="social_share">
												<span
													class="twitter_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-twitter kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
														class="kartra_icon__icon fa fa-twitter"></span><span
														class="social_share__text">Post</span></span>
											</div>


											<div data-component="social_share">
												<span
													class="whatsapp_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-whatsapp kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
														class="kartra_icon__icon fa fa-whatsapp"></span><span
														class="social_share__text">Share</span></span>
											</div>
										</div>
										<div data-component="image">
											<img class="kartra_image pull-center kartra_image--full"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
										</div>
										<div data-component="headline" id="qiftaePlFT">
											<div class="kartra_headline kartra_headline--lsp-1-size-medium kartra_headline--quicksand-font kartra_headline--text-center"
												style="position: relative; margin: 0px 0px 40px;"
												aria-controls="cke_3199" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em;"><strong><span
															style="font-family: Roboto; line-height: 1.2em;">Are You
															Tired of Feling Bloated, Heavy, and Just Need a Reset?
														</span></strong><span
														style="font-family: Roboto; line-height: 1.2em;">Do You Really
														Want to Shed Some Unwanted Fat in Time for the Holidays?</span>
												</p>
											</div>
										</div>
										<div data-component="image" id="pb8mr5yqte">
											<img class="kartra_image kartra_image--margin-bottom-none kartra_image--max-width-full pull-center background_changer--blur0"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												style="opacity: 1; margin: 0px auto 20px; border-radius: 0px;" alt=""
												data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/1201337905085Dr-Cooper-Before-After-pics.png">
										</div>
										<div data-component="icon" href="javascript: void(0);" id="oX00Vk0qsn">
											<div class="kartra_icon kartra_icon--lsp-title-icn kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
												style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgba(0, 0, 0, 0.6);">
												<span style="color: rgb(255, 76, 0);"
													class="kartra_icon__icon fa fa-bullhorn"></span>
											</div>
										</div>
										<div data-component="headline" id="DJuveAnsvh">
											<div class="kartra_headline kartra_headline--lsp-1-size-medium kartra_headline--quicksand-font kartra_headline--text-center"
												style="position: relative; margin: 0px 0px 20px;"
												aria-controls="cke_17326" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em;"><span
														style="line-height: 1.2em; color: rgb(153, 0, 0);">Then It's
														Time to Take the <strong><span
																style="line-height: 1.2em; color: rgb(153, 0, 0);"><span
																	style="line-height: 1.2em; color: rgb(153, 0, 0);">14-Day
																	Skinny Tummy Challenge</span></span></strong> and
														Transform your health and vitality! This is your gateway to
														lifelong well-being and the end-of-year body you deserve!</span>
												</p>
											</div>
										</div>
										<div data-component="image" id="n5OfH1hYXg">
											<img class="kartra_image pull-center kartra_image--full"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
										</div>
										<div data-component="list" id="58vW1LlTNV">
											<ul class="kartra_list">
												<li class="kartra_list__item kartra_list__item--table">
													<div class="kartra_item_info kartra_item_info--lsp-1--size-medium">

													</div>
													<div
														class="kartra_item_info kartra_item_info--padding-left-extra-tiny">
														<div class="kartra_item_info__headline kartra_item_info__headline--h4 kartra_item_info__headline--black kartra_headline--pt-sans-font"
															style="position: relative;" aria-controls="cke_13259"
															aria-activedescendant="" aria-autocomplete="list"
															aria-expanded="false">
															<p style="line-height: 1.2em;"><strong><span
																		style="line-height: 1.2em;"><span
																			style="color: rgb(55, 71, 78); line-height: 1.2em;">What
																			are you waiting for?
																		</span></span></strong><span
																	style="color: rgb(55, 71, 78); line-height: 1.2em;">Revitalize
																	your lifestyle in just 2 weeks and feel healthier
																	than you've been all year. In fact, what better way
																	to finish the year strong than with a skinny tummy
																	and feeling fabulous</span><strong><span
																		style="line-height: 1.2em;"><span
																			style="color: rgb(55, 71, 78); line-height: 1.2em;">
																			(just in time to impress friends and family
																			over the holidays)?</span></span></strong>
															</p>

															<p style="line-height: 1.2em;"> </p>

															<p style="line-height: 1.2em;"><span
																	style="color: rgb(102, 102, 102); line-height: 1.2em;">Join
																	this </span><span
																	style="color: rgb(55, 71, 78); line-height: 1.2em;">specialized
																	<em><span
																			style="color: rgb(55, 71, 78); line-height: 1.2em;"><span
																				style="color: rgb(55, 71, 78); line-height: 1.2em;">14-Day
																				Skinny Tummy
																				Challenge</span></span></em>
																	and</span><strong><span
																		style="line-height: 1.2em;"><span
																			style="color: rgb(55, 71, 78); line-height: 1.2em;">
																			get daily eating plans, fat-melting
																			exercises, empowering meditation,
																		</span></span></strong><span
																	style="color: rgb(55, 71, 78); line-height: 1.2em;">and
																	a support group led by a board-certified doctor of
																	Internal Medicine.</span><span
																	style="color: rgb(102, 102, 102); line-height: 1.2em;"> </span>
															</p>

															<p style="line-height: 1.2em;"> </p>

															<p style="line-height: 1.2em;"><span
																	style="color: rgb(102, 102, 102); line-height: 1.2em;">My
																	friend, you're about to witness the incredible
																	impact of lifestyle changes on your health and
																	vitality with a program designed </span><span
																	style="color: rgb(55, 71, 78); line-height: 1.2em;">to
																	inspire and help you reach your year-end fitness
																	goals. Enroll now!</span></p>


														</div>
														<div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--size-extra-medium"
															style="position: relative; margin: 0px 0px 40px;"
															aria-controls="cke_12414" aria-activedescendant=""
															aria-autocomplete="list" aria-expanded="false"></div>
													</div>
												</li>
												<li class="kartra_list__item kartra_list__item--table">
													<div class="kartra_item_info kartra_item_info--lsp-1--size-medium">

													</div>
													<div
														class="kartra_item_info kartra_item_info--padding-left-extra-tiny">


													</div>
												</li>


											</ul>
										</div>
										<div data-component="image" id="uhC3hIcK2R"><img
												class="kartra_image kartra_image--full pull-center background_changer--blur0"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
												style="margin: 0px auto 20px; border-radius: 0px; opacity: 1;" alt=""
												data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/33708319616514-Day_Skinny_Tummy__1_.png">
										</div>
										<div data-component="button" id="Q22HiIbtZF"><a href="javascript:void(0);" onclick="openPaymentSelection()"
												class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
												style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: Lato; border-radius: 10px;"
												target="_parent"><span class="kartra_button11_text"
													style="font-weight: 700;"><span
														class="kartra_button11_text_first_line"
														style="font-weight: 700;">SIGN UP NOW!</span><span
														class="kartra_button11_text_second_line"
														style="font-weight: 700;">for only $49.99</span></span></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content light" style="padding: 50px 0px; background-color: rgb(19, 170, 76);" id="_5scsdltud">
				<style id="pagesInternalCSS">
					.kartra_headline--quicksand-font {
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_headline--pt-sans-font {
						font-family: 'PT Sans', sans-serif;
					}

					.kartra_headline--lsp-1-size-giant {
						font-size: 2.8rem;
						text-shadow: 0px 3px 15px rgba(0, 0, 0, .15);
					}

					.kartra_headline--lsp-1-size-extra-medium {
						font-size: 1.95rem
					}

					.kartra_headline--lsp-1-size-medium {
						font-size: 1.64rem
					}

					.kartra_headline--lsp-1-size-special-medium {
						font-size: 1.57rem;
						line-height: 120%;
					}

					.kartra_headline--lsp-1-size-big-small {
						font-size: 1.1rem
					}

					.kartra_headline--lsp-1-size-small {
						font-size: 1.02rem
					}

					.kartra_headline--lsp-1-size-extra-small {
						font-size: 0.86rem
					}

					.kartra_headline--lsp-1-size-big-tiny {
						font-size: 0.82rem
					}

					.kartra_headline--lsp-1-size-tiny {
						font-size: 0.7rem
					}

					.kartra_text--lsp-1--size-large {
						font-size: 0.86rem;
					}

					.kartra_text--lsp-1--size-extra-medium {
						font-size: 0.785rem;
					}

					.kartra_text--lsp-1--size-small {
						font-size: 0.7rem;
					}

					.kartra_item_info__text--lsp-1--size-medium {
						font-size: 0.7rem;
					}

					.kartra_divider--lsp-1-small.kartra_divider--small {
						width: 170px;
					}

					.kartra_item_info--lsp-1--size-medium {
						width: 50px;
					}

					.content--lsp--1 .kartra_video--player_1 {
						box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
					}

					.kartra_button11--squared.kartra_button11--lsp-1-two-line-btn {
						background-image: none;
						border-radius: 10px;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_first_line {
						font-size: 40px;
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_second_line {
						font-size: 25px;
					}

					.content--lsp--1.kartra_video--player_1 {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .3);
					}

					.kartra_icon--lsp-title-icn.kartra_icon--large {
						width: 75px;
						height: 75px;
						font-size: 45px;
					}

					.kartra_element_bg--lsp-box-style-one {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .07);
						border-radius: 6px;
					}

					.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
						width: 150px;
						height: 150px;
						font-size: 72px;
					}

					.js_kartra_component_holder--height-auto {
						min-height: auto;
					}

					.background-item__arrow--left-arrow-top {
						position: absolute;
						top: -7px;
						left: 35px;
						margin-left: -7px;
						width: 14px;
						height: 14px;
						background: inherit;
						transform: rotate(45deg);
					}

					.kartra_image--lsp-1-testimonoial {
						width: 70px;
					}

					.kartra_element_bg--testimonial-box {
						box-shadow: 0px 5px 20px rgba(0, 0, 0, .1);
						border-radius: 12px;
					}

					.kartra_element_bg--dot-sign {
						width: 8px;
						height: 8px;
						padding: 0px !important;
						position: relative;
					}

					.kartra_element_bg--who-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_element_bg--who-box .background-item {
						background-size: inherit !important;
						background-repeat: no-repeat;
						background-position: right bottom;
					}

					.kartra_icon--who-area.kartra_icon--giant {
						width: 56px;
						height: 56px;
					}

					.kartra_icon--who-area.kartra_icon--giant.kartra_icon--circled {
						width: 92px;
						height: 92px;
					}

					.kartra_element_bg--who-call-top-action {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_headline_block__info--padding-left-small {
						padding-left: 25px;
					}

					.kartra_icon--lsp-giant.kartra_icon--giant {
						font-size: 65px;
						width: 105px;
						height: 105px;
					}

					.kartra_element_bg--feature-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_icon--pricing-med-icn.kartra_icon--medium {
						font-size: 30px;
					}

					.kartra_button1--price-btn.kartra_button1 {
						font-size: 20px;
						font-family: 'Quicksand', sans-serif;
						border-radius: 8px;
						padding: 16px;
						box-shadow: inset 0 -3px 0px rgba(0, 0, 0, 0.2), 0px 5px 30px rgba(0, 0, 0, .1);
					}

					.kartra_element_bg--lsp-pricing {
						box-shadow: 0px 5px 30px rgba(0, 0, 0, .05);
						border-radius: 10px;
					}

					.kartra_divider--border-tiny.kartra_divider--small {
						width: 170px;
					}

					/*Accordion*/
					.lsp-1--accordion.accordion-3 .panel {
						border: 2px solid #f5f5f5;
						border-radius: 10px;
						box-shadow: 0px 3px 10px rgba(0, 0, 0, .03)
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						color: #424242;
						font-size: 20px;
						font-weight: 500;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-title {
						border: 15px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon {
						border-top: 17px solid transparent;
						border-bottom: 17px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element {
						border: 0px;
						color: rgb(255, 76, 0);
						font-size: 22px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element i:before {
						content: "\f063";
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading:hover {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading+.panel-collapse>.panel-body,
					.accordion-3 .panel .panel-heading+.panel-collapse>.list-group {
						border-top: 1px solid #eee;
						margin: 0px 30px;
						padding: 24px 0px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-body p {
						color: #37474e;
						font-size: 18px;
						line-height: 140%;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						padding: 10px 15px;
					}

					.lsp-1--accordion.accordion-3 .panel+.panel {
						margin-top: 25px;
					}

					.kartra_element_bg--thumb-size-extra-medium {
						width: 70px;
						height: 70px;
					}

					.kartra_testimonial_author_block--padding-bottom-extra-small {
						padding-bottom: 20px;
					}

					@media(max-width: 991px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.2rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 0.85rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 0.9rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 0.85rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 0.85rem;
						}

						.column--sm-padding-bottom-none,
						.column--sm-padding-bottom-none .background-item {
							padding-bottom: 0px !important;
						}

						.column--sm-padding-top-extra-medium {
							padding-top: 40px !important;
						}

						.row--sm-margin-top-none {
							margin-top: 0px !important;
						}

						.column--sm-height-auto {
							min-height: auto !important;
						}

						.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
							width: 100px;
							height: 100px;
							font-size: 50px;
						}

						.kartra_text--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.row--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.sm-pull-left {
							float: left !important;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.1rem;
						}

						.content--sm-padding-bottom-none,
						.content--sm-padding-bottom-none .background_changer {
							padding-bottom: 0px !important;
						}

						.content--sm-padding-top-none,
						.content--sm-padding-top-none .background_changer {
							padding-top: 0px !important;
						}
					}

					@media(max-width: 767px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.15rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.05rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.2rem;
						}

						.kartra_item_info--lsp-1--size-medium {
							width: 30px;
						}

						.kartra_item_info__headline--xs-margin-top-negative-extra-tiny {
							margin-top: -5px !important;
						}

						.kartra_headline_block--flex {
							display: block !important;
						}

						.kartra_element_bg--who-call-top-action,
						.kartra_element_bg--who-call-top-action .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_element_bg--feature-box,
						.kartra_element_bg--feature-box .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_headline_block__info--padding-left-small {
							padding-left: 0px !important;
						}

						.kartra_text--xs-text-center {
							text-align: center !important;
						}

						.kartra_headline--xs-text-center {
							text-align: center !important;
						}
					}

					@media(max-width: 480px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1.15rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.3rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.2rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1.25rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1.25rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.3rem;
						}

						.kartra_testimonial__author_details--padding-left-extra-small {
							padding-left: 20px !important;
						}

						.kartra_item_info__headline--xxs-negative-margin-top-big-tiny {
							margin-top: -8px !important;
						}
					}

					@media (max-width: 767px) {
						.kartra_text--xs-text-center {
							text-align: inherit;
						}

						.kartra_headline--xs-text-center {
							text-align: inherit;
						}

						.kartra_element_bg--xs-padding-left-right-special-medium {
							padding-left: 30px !important;
							padding-right: 30px !important;
						}

						.kartra_text--xs-text-center-important {
							text-align: center;
						}

						.content--xs-padding-bottom-none-important {
							padding-bottom: 0px !important;
						}
					}
				</style>

				<div class="background_changer background_changer--blur0 js-bg-next-gen" style="opacity: 0.1;" alt=""
					data-bg='url("//d11n7da8rpqbjy.cloudfront.net/Kartradev/10989_1498116245317Pattern.png")'></div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container">
					<div class="row" data-component="grid" id="accordion-5wBCPvQVrN">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="icon" id="accordion-5los2Zjyv1">
									<div class="kartra_icon kartra_icon--lsp-title-icn kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
										style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgba(0, 0, 0, 0.6);">
										<span style="color: rgb(255, 255, 255);"
											class="kartra_icon__icon fa fa-briefcase"></span>
									</div>
								</div>
								<div data-component="headline" id="accordion-sxCOdJ0Qvh">
									<div class="kartra_headline kartra_headline--text-center kartra_headline--lsp-1-size-medium kartra_headline--quicksand-font kartra_headline--white"
										style="position: relative; margin: 0px 0px 15px;" aria-controls="cke_55"
										aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
										<p style="line-height: 1.2em;"><strong><span
													style="font-family: Roboto; line-height: 1.2em;"><span
														style="line-height: 1.2em; font-family: Roboto;">Pave the way to
														an amazingly healthier you with our revolutionary 14-Day Skinny
														Tummy Challenge. Your journey to wellness starts
														here!</span></span></strong></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="accordion-4e6KIf00ZD">
						<div class="col-md-10 col-md-offset-1">
							<div class="row" data-component="grid" id="kawO3">
								<div class="col-md-4">
									<div class="js_kartra_component_holder">
										<div data-component="icon" href="javascript: void(0);">
											<div class="kartra_icon kartra_icon--lsp-icn-style-1 kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--circled kartra_icon--giant"
												style="background-color: rgb(255, 255, 255); margin: 0px auto 25px; border-color: rgb(19, 170, 159);">
												<span style="color: rgb(19, 170, 159);"
													class="kartra_icon__icon fa fa-child"></span>
											</div>
										</div>
										<div data-component="headline" id="sEssWJot3Z">
											<div class="kartra_headline kartra_headline--lsp-1-size-small kartra_headline--text-center kartra_headline--white"
												style="position: relative; margin: 0px 0px 5px;"
												aria-controls="cke_3682" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em;"><span style="line-height: 1.2em;">Unleash
														the potential for a healthier you with meals and movement
														designed for health, lengevity and weightloss</span></p>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="js_kartra_component_holder">
										<div data-component="icon" href="javascript: void(0);">
											<div class="kartra_icon kartra_icon--lsp-icn-style-1 kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--circled kartra_icon--giant"
												style="background-color: rgb(255, 255, 255); margin: 0px auto 25px; border-color: rgb(19, 170, 159);">
												<span style="color: rgb(19, 170, 159);"
													class="kartra_icon__icon fa fa-comments-o"></span>
											</div>
										</div>
										<div data-component="headline" id="accordion-CBjcpoAksO">
											<div class="kartra_headline kartra_headline--lsp-1-size-small kartra_headline--text-center kartra_headline--white"
												style="position: relative; margin: 0px 0px 5px;"
												aria-controls="cke_3154" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em;">Join a group of like-minded individuals
													to keep you motivated and get your questions answered by Dr. Cooper!
												</p>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="js_kartra_component_holder">
										<div data-component="icon" href="javascript: void(0);">
											<div class="kartra_icon kartra_icon--lsp-icn-style-1 kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--circled kartra_icon--giant"
												style="background-color: rgb(255, 255, 255); margin: 0px auto 25px; border-color: rgb(19, 170, 159);">
												<span style="color: rgb(19, 170, 159);"
													class="kartra_icon__icon fa fa-calendar-o"></span>
											</div>
										</div>
										<div data-component="headline" id="accordion-JsAoocmDxM">
											<div class="kartra_headline kartra_headline--h4 kartra_headline--text-center kartra_headline--white"
												style="position: relative; margin: 0px 0px 5px;"
												aria-controls="cke_4080" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em;"><span style="line-height: 1.2em;">Unlock
														the power of holistic health and wellness with our exclusive
														14-Day Skinny Tummy Challenge. The journey to a healthier you
														starts today!</span></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content" style="padding-top: 50px; padding-bottom: 30px; background-color: rgb(245, 245, 245);"
				id="_zmmaa0ygl">
				<style id="pagesInternalCSS">
					.kartra_headline--quicksand-font {
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_headline--pt-sans-font {
						font-family: 'PT Sans', sans-serif;
					}

					.kartra_headline--lsp-1-size-giant {
						font-size: 2.8rem;
						text-shadow: 0px 3px 15px rgba(0, 0, 0, .15);
					}

					.kartra_headline--lsp-1-size-extra-medium {
						font-size: 1.95rem
					}

					.kartra_headline--lsp-1-size-medium {
						font-size: 1.64rem
					}

					.kartra_headline--lsp-1-size-special-medium {
						font-size: 1.57rem;
						line-height: 120%;
					}

					.kartra_headline--lsp-1-size-big-small {
						font-size: 1.1rem
					}

					.kartra_headline--lsp-1-size-small {
						font-size: 1.02rem
					}

					.kartra_headline--lsp-1-size-extra-small {
						font-size: 0.86rem
					}

					.kartra_headline--lsp-1-size-big-tiny {
						font-size: 0.82rem
					}

					.kartra_headline--lsp-1-size-tiny {
						font-size: 0.7rem
					}

					.kartra_text--lsp-1--size-large {
						font-size: 0.86rem;
					}

					.kartra_text--lsp-1--size-extra-medium {
						font-size: 0.785rem;
					}

					.kartra_text--lsp-1--size-small {
						font-size: 0.7rem;
					}

					.kartra_item_info__text--lsp-1--size-medium {
						font-size: 0.7rem;
					}

					.kartra_divider--lsp-1-small.kartra_divider--small {
						width: 170px;
					}

					.kartra_item_info--lsp-1--size-medium {
						width: 50px;
					}

					.content--lsp--1 .kartra_video--player_1 {
						box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
					}

					.kartra_button11--squared.kartra_button11--lsp-1-two-line-btn {
						background-image: none;
						border-radius: 10px;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_first_line {
						font-size: 40px;
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_second_line {
						font-size: 25px;
					}

					.content--lsp--1.kartra_video--player_1 {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .3);
					}

					.kartra_icon--lsp-title-icn.kartra_icon--large {
						width: 75px;
						height: 75px;
						font-size: 45px;
					}

					.kartra_element_bg--lsp-box-style-one {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .07);
						border-radius: 6px;
					}

					.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
						width: 150px;
						height: 150px;
						font-size: 72px;
					}

					.js_kartra_component_holder--height-auto {
						min-height: auto;
					}

					.background-item__arrow--left-arrow-top {
						position: absolute;
						top: -7px;
						left: 35px;
						margin-left: -7px;
						width: 14px;
						height: 14px;
						background: inherit;
						transform: rotate(45deg);
					}

					.kartra_image--lsp-1-testimonoial {
						width: 70px;
					}

					.kartra_element_bg--testimonial-box {
						box-shadow: 0px 5px 20px rgba(0, 0, 0, .1);
						border-radius: 12px;
					}

					.kartra_element_bg--dot-sign {
						width: 8px;
						height: 8px;
						padding: 0px !important;
						position: relative;
					}

					.kartra_element_bg--who-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_element_bg--who-box .background-item {
						background-size: inherit !important;
						background-repeat: no-repeat;
						background-position: right bottom;
					}

					.kartra_icon--who-area.kartra_icon--giant {
						width: 56px;
						height: 56px;
					}

					.kartra_icon--who-area.kartra_icon--giant.kartra_icon--circled {
						width: 92px;
						height: 92px;
					}

					.kartra_element_bg--who-call-top-action {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_headline_block__info--padding-left-small {
						padding-left: 25px;
					}

					.kartra_icon--lsp-giant.kartra_icon--giant {
						font-size: 65px;
						width: 105px;
						height: 105px;
					}

					.kartra_element_bg--feature-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_icon--pricing-med-icn.kartra_icon--medium {
						font-size: 30px;
					}

					.kartra_button1--price-btn.kartra_button1 {
						font-size: 20px;
						font-family: 'Quicksand', sans-serif;
						border-radius: 8px;
						padding: 16px;
						box-shadow: inset 0 -3px 0px rgba(0, 0, 0, 0.2), 0px 5px 30px rgba(0, 0, 0, .1);
					}

					.kartra_element_bg--lsp-pricing {
						box-shadow: 0px 5px 30px rgba(0, 0, 0, .05);
						border-radius: 10px;
					}

					.kartra_divider--border-tiny.kartra_divider--small {
						width: 170px;
					}

					/*Accordion*/
					.lsp-1--accordion.accordion-3 .panel {
						border: 2px solid #f5f5f5;
						border-radius: 10px;
						box-shadow: 0px 3px 10px rgba(0, 0, 0, .03)
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						color: #424242;
						font-size: 20px;
						font-weight: 500;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-title {
						border: 15px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon {
						border-top: 17px solid transparent;
						border-bottom: 17px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element {
						border: 0px;
						color: rgb(255, 76, 0);
						font-size: 22px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element i:before {
						content: "\f063";
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading:hover {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading+.panel-collapse>.panel-body,
					.accordion-3 .panel .panel-heading+.panel-collapse>.list-group {
						border-top: 1px solid #eee;
						margin: 0px 30px;
						padding: 24px 0px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-body p {
						color: #37474e;
						font-size: 18px;
						line-height: 140%;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						padding: 10px 15px;
					}

					.lsp-1--accordion.accordion-3 .panel+.panel {
						margin-top: 25px;
					}

					.kartra_element_bg--thumb-size-extra-medium {
						width: 70px;
						height: 70px;
					}

					.kartra_testimonial_author_block--padding-bottom-extra-small {
						padding-bottom: 20px;
					}

					@media(max-width: 991px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.2rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 0.85rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 0.9rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 0.85rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 0.85rem;
						}

						.column--sm-padding-bottom-none,
						.column--sm-padding-bottom-none .background-item {
							padding-bottom: 0px !important;
						}

						.column--sm-padding-top-extra-medium {
							padding-top: 40px !important;
						}

						.row--sm-margin-top-none {
							margin-top: 0px !important;
						}

						.column--sm-height-auto {
							min-height: auto !important;
						}

						.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
							width: 100px;
							height: 100px;
							font-size: 50px;
						}

						.kartra_text--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.row--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.sm-pull-left {
							float: left !important;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.1rem;
						}

						.content--sm-padding-bottom-none,
						.content--sm-padding-bottom-none .background_changer {
							padding-bottom: 0px !important;
						}

						.content--sm-padding-top-none,
						.content--sm-padding-top-none .background_changer {
							padding-top: 0px !important;
						}
					}

					@media(max-width: 767px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.15rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.05rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.2rem;
						}

						.kartra_item_info--lsp-1--size-medium {
							width: 30px;
						}

						.kartra_item_info__headline--xs-margin-top-negative-extra-tiny {
							margin-top: -5px !important;
						}

						.kartra_headline_block--flex {
							display: block !important;
						}

						.kartra_element_bg--who-call-top-action,
						.kartra_element_bg--who-call-top-action .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_element_bg--feature-box,
						.kartra_element_bg--feature-box .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_headline_block__info--padding-left-small {
							padding-left: 0px !important;
						}

						.kartra_text--xs-text-center {
							text-align: center !important;
						}

						.kartra_headline--xs-text-center {
							text-align: center !important;
						}
					}

					@media(max-width: 480px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1.15rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.3rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.2rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1.25rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1.25rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.3rem;
						}

						.kartra_testimonial__author_details--padding-left-extra-small {
							padding-left: 20px !important;
						}

						.kartra_item_info__headline--xxs-negative-margin-top-big-tiny {
							margin-top: -8px !important;
						}
					}

					@media (max-width: 767px) {
						.kartra_text--xs-text-center {
							text-align: inherit;
						}

						.kartra_headline--xs-text-center {
							text-align: inherit;
						}

						.kartra_element_bg--xs-padding-left-right-special-medium {
							padding-left: 30px !important;
							padding-right: 30px !important;
						}

						.kartra_text--xs-text-center-important {
							text-align: center;
						}

						.content--xs-padding-bottom-none-important {
							padding-bottom: 0px !important;
						}
					}
				</style>

				<div class="background_changer background_changer--blur0" style="opacity: 1;"
					data-bg="url(https://unsplash.com/photos/DZ5qYLvWsHw)"></div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container">
					<div class="row" data-component="grid" id="accordion-3ssXjEvRTj">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="icon" href="javascript: void(0);">
									<div class="kartra_icon kartra_icon--lsp-title-icn kartra_icon--light-grey kartra_icon--center kartra_icon--margin-bottom-small kartra_icon--large"
										style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgba(0, 0, 0, 0.6);">
										<span style="color: rgb(255, 76, 0);"
											class="kartra_icon__icon fa fa-cube"></span>
									</div>
								</div>

								<div data-component="headline" id="accordion-Tu9ykWfn6t">
									<div class="kartra_headline kartra_headline--lsp-1-size-medium kartra_headline--quicksand-font kartra_headline--text-center"
										style="position: relative; margin: 0px 0px 40px;" aria-controls="cke_56"
										aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
										<p style="line-height: 1.2em;">This is just the beginning...</p>
									</div>
								</div>
								<div data-component="button" id="ubMGmeeBd5"><a href="javascript:void(0);" onclick="openPaymentSelection()"
										class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
										style="background-color: rgb(61, 170, 19); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: Roboto; border-radius: 10px;"
										target="_parent"><span
											class="kartra_button11_text" style="font-weight: 700;"><span
												class="kartra_button11_text_first_line" style="font-weight: 700;">SIGN
												UP NOW!</span><span class="kartra_button11_text_second_line"
												style="font-weight: 700;">for only $49.99</span></span></a></div>
								<div data-component="countdown">
									<div class="countdown-section countdown-section--text-center countdown-section--margin-bottom-extra-small"
										data-countdown-id="fD04LQrQ2b" style="margin-top: 0px; margin-bottom: 20px;"
										data-countdown="fixed" data-date="12/08/2024" data-time="23:58"
										data-asset-id="2" data-timestamp="1733723880">
										<div class="countdown countdown--flex countdown--justify-content-center">
											<div
												class="countdown__item countdown__item--element-box-01 countdown__item--day countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
												<div>
													<span
														class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">DAYS</span>
												</div>
												<div
													class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--element-box-01 countdown__item--hours countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
												<div>
													<span
														class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">HOURS</span>
												</div>
												<div
													class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--element-box-01 countdown__item--minutes countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
												<div>
													<span
														class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">MINUTES</span>
												</div>
												<div
													class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--element-box-01 countdown__item--seconds countdown__item--bg-black-opaque-40 countdown__item--rounded-tiny countdown__item--white countdown__item--margin-left-right-extra-tiny countdown__item--xxs-margin-left-right-semi-special-tiny">
												<div>
													<span
														class="countdown_title countdown_title--oswald-font countdown_title--element-title-01 countdown_title--margin-top-big-tiny">SECONDS</span>
												</div>
												<div
													class="digit-list digit-list--oswald-font digit-list--font-weight-regular digit-list--element-size-01">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="accordion-ebVMgpvrmX"></div>
					<div class="row" data-component="grid" id="accordion-ebVMgpvrmX"></div>
				</div>
			</div>
			<div class="content dark" style="padding: 0px; background-color: rgba(0, 0, 0, 0);" id="_2wdcd7qhq">
				<style id="pagesInternalCSS">
					.kartra_headline--quicksand-font {
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_headline--pt-sans-font {
						font-family: 'PT Sans', sans-serif;
					}

					.kartra_headline--lsp-1-size-giant {
						font-size: 2.8rem;
						text-shadow: 0px 3px 15px rgba(0, 0, 0, .15);
					}

					.kartra_headline--lsp-1-size-extra-medium {
						font-size: 1.95rem
					}

					.kartra_headline--lsp-1-size-medium {
						font-size: 1.64rem
					}

					.kartra_headline--lsp-1-size-special-medium {
						font-size: 1.57rem;
						line-height: 120%;
					}

					.kartra_headline--lsp-1-size-big-small {
						font-size: 1.1rem
					}

					.kartra_headline--lsp-1-size-small {
						font-size: 1.02rem
					}

					.kartra_headline--lsp-1-size-extra-small {
						font-size: 0.86rem
					}

					.kartra_headline--lsp-1-size-big-tiny {
						font-size: 0.82rem
					}

					.kartra_headline--lsp-1-size-tiny {
						font-size: 0.7rem
					}

					.kartra_text--lsp-1--size-large {
						font-size: 0.86rem;
					}

					.kartra_text--lsp-1--size-extra-medium {
						font-size: 0.785rem;
					}

					.kartra_text--lsp-1--size-small {
						font-size: 0.7rem;
					}

					.kartra_item_info__text--lsp-1--size-medium {
						font-size: 0.7rem;
					}

					.kartra_divider--lsp-1-small.kartra_divider--small {
						width: 170px;
					}

					.kartra_item_info--lsp-1--size-medium {
						width: 50px;
					}

					.content--lsp--1 .kartra_video--player_1 {
						box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
					}

					.kartra_button11--squared.kartra_button11--lsp-1-two-line-btn {
						background-image: none;
						border-radius: 10px;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_first_line {
						font-size: 40px;
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_second_line {
						font-size: 25px;
					}

					.content--lsp--1.kartra_video--player_1 {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .3);
					}

					.kartra_icon--lsp-title-icn.kartra_icon--large {
						width: 75px;
						height: 75px;
						font-size: 45px;
					}

					.kartra_element_bg--lsp-box-style-one {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .07);
						border-radius: 6px;
					}

					.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
						width: 150px;
						height: 150px;
						font-size: 72px;
					}

					.js_kartra_component_holder--height-auto {
						min-height: auto;
					}

					.background-item__arrow--left-arrow-top {
						position: absolute;
						top: -7px;
						left: 35px;
						margin-left: -7px;
						width: 14px;
						height: 14px;
						background: inherit;
						transform: rotate(45deg);
					}

					.kartra_image--lsp-1-testimonoial {
						width: 70px;
					}

					.kartra_element_bg--testimonial-box {
						box-shadow: 0px 5px 20px rgba(0, 0, 0, .1);
						border-radius: 12px;
					}

					.kartra_element_bg--dot-sign {
						width: 8px;
						height: 8px;
						padding: 0px !important;
						position: relative;
					}

					.kartra_element_bg--who-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_element_bg--who-box .background-item {
						background-size: inherit !important;
						background-repeat: no-repeat;
						background-position: right bottom;
					}

					.kartra_icon--who-area.kartra_icon--giant {
						width: 56px;
						height: 56px;
					}

					.kartra_icon--who-area.kartra_icon--giant.kartra_icon--circled {
						width: 92px;
						height: 92px;
					}

					.kartra_element_bg--who-call-top-action {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_headline_block__info--padding-left-small {
						padding-left: 25px;
					}

					.kartra_icon--lsp-giant.kartra_icon--giant {
						font-size: 65px;
						width: 105px;
						height: 105px;
					}

					.kartra_element_bg--feature-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_icon--pricing-med-icn.kartra_icon--medium {
						font-size: 30px;
					}

					.kartra_button1--price-btn.kartra_button1 {
						font-size: 20px;
						font-family: 'Quicksand', sans-serif;
						border-radius: 8px;
						padding: 16px;
						box-shadow: inset 0 -3px 0px rgba(0, 0, 0, 0.2), 0px 5px 30px rgba(0, 0, 0, .1);
					}

					.kartra_element_bg--lsp-pricing {
						box-shadow: 0px 5px 30px rgba(0, 0, 0, .05);
						border-radius: 10px;
					}

					.kartra_divider--border-tiny.kartra_divider--small {
						width: 170px;
					}

					/*Accordion*/
					.lsp-1--accordion.accordion-3 .panel {
						border: 2px solid #f5f5f5;
						border-radius: 10px;
						box-shadow: 0px 3px 10px rgba(0, 0, 0, .03)
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						color: #424242;
						font-size: 20px;
						font-weight: 500;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-title {
						border: 15px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon {
						border-top: 17px solid transparent;
						border-bottom: 17px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element {
						border: 0px;
						color: rgb(255, 76, 0);
						font-size: 22px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element i:before {
						content: "\f063";
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading:hover {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading+.panel-collapse>.panel-body,
					.accordion-3 .panel .panel-heading+.panel-collapse>.list-group {
						border-top: 1px solid #eee;
						margin: 0px 30px;
						padding: 24px 0px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-body p {
						color: #37474e;
						font-size: 18px;
						line-height: 140%;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						padding: 10px 15px;
					}

					.lsp-1--accordion.accordion-3 .panel+.panel {
						margin-top: 25px;
					}

					.kartra_element_bg--thumb-size-extra-medium {
						width: 70px;
						height: 70px;
					}

					.kartra_testimonial_author_block--padding-bottom-extra-small {
						padding-bottom: 20px;
					}

					@media(max-width: 991px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.2rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 0.85rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 0.9rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 0.85rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 0.85rem;
						}

						.column--sm-padding-bottom-none,
						.column--sm-padding-bottom-none .background-item {
							padding-bottom: 0px !important;
						}

						.column--sm-padding-top-extra-medium {
							padding-top: 40px !important;
						}

						.row--sm-margin-top-none {
							margin-top: 0px !important;
						}

						.column--sm-height-auto {
							min-height: auto !important;
						}

						.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
							width: 100px;
							height: 100px;
							font-size: 50px;
						}

						.kartra_text--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.row--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.sm-pull-left {
							float: left !important;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.1rem;
						}

						.content--sm-padding-bottom-none,
						.content--sm-padding-bottom-none .background_changer {
							padding-bottom: 0px !important;
						}

						.content--sm-padding-top-none,
						.content--sm-padding-top-none .background_changer {
							padding-top: 0px !important;
						}
					}

					@media(max-width: 767px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.15rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.05rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.2rem;
						}

						.kartra_item_info--lsp-1--size-medium {
							width: 30px;
						}

						.kartra_item_info__headline--xs-margin-top-negative-extra-tiny {
							margin-top: -5px !important;
						}

						.kartra_headline_block--flex {
							display: block !important;
						}

						.kartra_element_bg--who-call-top-action,
						.kartra_element_bg--who-call-top-action .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_element_bg--feature-box,
						.kartra_element_bg--feature-box .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_headline_block__info--padding-left-small {
							padding-left: 0px !important;
						}

						.kartra_text--xs-text-center {
							text-align: center !important;
						}

						.kartra_headline--xs-text-center {
							text-align: center !important;
						}
					}

					@media(max-width: 480px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1.15rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.3rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.2rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1.25rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1.25rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.3rem;
						}

						.kartra_testimonial__author_details--padding-left-extra-small {
							padding-left: 20px !important;
						}

						.kartra_item_info__headline--xxs-negative-margin-top-big-tiny {
							margin-top: -8px !important;
						}
					}

					@media (max-width: 767px) {
						.kartra_text--xs-text-center {
							text-align: inherit;
						}

						.kartra_headline--xs-text-center {
							text-align: inherit;
						}

						.kartra_element_bg--xs-padding-left-right-special-medium {
							padding-left: 30px !important;
							padding-right: 30px !important;
						}

						.kartra_text--xs-text-center-important {
							text-align: center;
						}

						.content--xs-padding-bottom-none-important {
							padding-bottom: 0px !important;
						}
					}
				</style>

				<div class="background_changer background_changer--blur0"
					style="opacity: 1; background-color: rgba(0, 0, 0, 0); padding-top: 0px; padding-bottom: 0px;"
					alt=""
					data-bg='url("https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/50108428320714-Day_Skinny_Tummy__11.249_x_8_in_.png")'>
				</div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container-fluid">
					<div class="row row--equal" data-component="grid" id="accordion-mgkkpLeQFp">
						<div class="col-md-6"
							style="margin: 0px; padding: 30px 15px; background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; background-image: none;">
							<div style="background-color: rgb(76, 76, 76); border-radius: 0px; padding: 100px 15px; background-image: none; opacity: 1; box-shadow: rgb(51, 51, 51) 0px 0px 0px 0px;"
								class="background-item background_changer--blur0" alt=""></div>
							<div class="row" data-component="grid" id="accordion-7EDb1YcVlc">
								<div class="col-md-9 col-md-offset-3">
									<div class="js_kartra_component_holder">
										<div data-component="headline" id="accordion-eHkLskxrOF">
											<div class="kartra_headline kartra_headline--lsp-1-size-medium kartra_headline--quicksand-font kartra_headline--sm-text-center kartra_headline--white"
												style="position: relative; margin-top: 0px; margin-bottom: 10px;"
												aria-controls="cke_707" aria-activedescendant=""
												aria-autocomplete="list" aria-expanded="false">
												<p style="line-height: 1.2em; text-align: center;"><strong><span
															style="color: rgb(255, 255, 0); line-height: 1.2em;"><span
																style="font-family: Roboto; line-height: 1.2em; color: rgb(255, 255, 0);">DECEMBER
																Special!</span></span></strong></p>
											</div>
										</div>
										<div data-component="image" id="8vt1ZsZwRP"><img
												class="kartra_image kartra_image--full pull-center background_changer--blur0"
												src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
												onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
												style="margin: 0px auto; border-radius: 0px; opacity: 1;" alt=""
												data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/1201337905085Dr-Cooper-Before-After-pics.png">
										</div>
										<div data-component="text" id="Is8q2">
											<div class="kartra_text kartra_text--white kartra_text--sm-text-center kartra_text--extra-small"
												style="margin: 0px; position: relative;" aria-controls="cke_4226"
												aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
												<ul>
													<li>
														<h2>Families of 3 or more who join get the third person free!
														</h2>
													</li>
												</ul>
											</div>
										</div>
										<div data-component="text" id="Is8q2">
											<div class="kartra_text kartra_text--white kartra_text--sm-text-center kartra_text--extra-small"
												style="margin: 0px; position: relative;" aria-controls="cke_2834"
												aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
												<ul>
													<li>Lose 15 lbs or more and get your money back provided we can
														share your story to inspire others.</li>
												</ul>
											</div>
										</div>
										<div data-component="text" id="Is8q2">
											<div class="kartra_text kartra_text--white kartra_text--sm-text-center kartra_text--extra-small"
												style="margin: 0px; position: relative;" aria-controls="cke_1913"
												aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
												<ul>
													<li>Get to turn heads for the holidays (<em>not for vanity but
															because you would have worked hard and totally deserve
															it</em>).</li>
												</ul>
											</div>
										</div>
										<div data-component="button" id="LV1pIsvHDM"><a href="javascript:void(0);" onclick="openPaymentSelection()"
												class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
												style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255); margin: 0px auto; font-weight: 400; font-family: Lato; border-radius: 10px;"
												target="_parent"><span class="kartra_button11_text"
													style="font-weight: 400;"><span
														class="kartra_button11_text_first_line"
														style="font-weight: 400;">SIGN UP NOW!</span><span
														class="kartra_button11_text_second_line"
														style="font-weight: 400;">for only $49.99</span></span></a>
										</div>
										<div data-component="text" id="accordion-yOdOoYSUzS">
											<div class="kartra_text kartra_text--text-small kartra_text--white kartra_text--sm-text-center"
												style="position: relative;" aria-controls="cke_1705"
												aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 column--sm-height-auto">

						</div>
					</div>
				</div>
			</div>
			<div class="content" style="padding: 30px 0px 10px; background-color: rgb(76, 76, 76);" id="_hovnv06qr">
				<style id="pagesInternalCSS">
					.kartra_headline--quicksand-font {
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_headline--pt-sans-font {
						font-family: 'PT Sans', sans-serif;
					}

					.kartra_headline--lsp-1-size-giant {
						font-size: 2.8rem;
						text-shadow: 0px 3px 15px rgba(0, 0, 0, .15);
					}

					.kartra_headline--lsp-1-size-extra-medium {
						font-size: 1.95rem
					}

					.kartra_headline--lsp-1-size-medium {
						font-size: 1.64rem
					}

					.kartra_headline--lsp-1-size-special-medium {
						font-size: 1.57rem;
						line-height: 120%;
					}

					.kartra_headline--lsp-1-size-big-small {
						font-size: 1.1rem
					}

					.kartra_headline--lsp-1-size-small {
						font-size: 1.02rem
					}

					.kartra_headline--lsp-1-size-extra-small {
						font-size: 0.86rem
					}

					.kartra_headline--lsp-1-size-big-tiny {
						font-size: 0.82rem
					}

					.kartra_headline--lsp-1-size-tiny {
						font-size: 0.7rem
					}

					.kartra_text--lsp-1--size-large {
						font-size: 0.86rem;
					}

					.kartra_text--lsp-1--size-extra-medium {
						font-size: 0.785rem;
					}

					.kartra_text--lsp-1--size-small {
						font-size: 0.7rem;
					}

					.kartra_item_info__text--lsp-1--size-medium {
						font-size: 0.7rem;
					}

					.kartra_divider--lsp-1-small.kartra_divider--small {
						width: 170px;
					}

					.kartra_item_info--lsp-1--size-medium {
						width: 50px;
					}

					.content--lsp--1 .kartra_video--player_1 {
						box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
					}

					.kartra_button11--squared.kartra_button11--lsp-1-two-line-btn {
						background-image: none;
						border-radius: 10px;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_first_line {
						font-size: 40px;
						font-family: 'Quicksand', sans-serif;
					}

					.kartra_button11--lsp-1-two-line-btn.kartra_button11--full-width .kartra_button11_text .kartra_button11_text_second_line {
						font-size: 25px;
					}

					.content--lsp--1.kartra_video--player_1 {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .3);
					}

					.kartra_icon--lsp-title-icn.kartra_icon--large {
						width: 75px;
						height: 75px;
						font-size: 45px;
					}

					.kartra_element_bg--lsp-box-style-one {
						box-shadow: 0px 10px 30px rgba(0, 0, 0, .07);
						border-radius: 6px;
					}

					.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
						width: 150px;
						height: 150px;
						font-size: 72px;
					}

					.js_kartra_component_holder--height-auto {
						min-height: auto;
					}

					.background-item__arrow--left-arrow-top {
						position: absolute;
						top: -7px;
						left: 35px;
						margin-left: -7px;
						width: 14px;
						height: 14px;
						background: inherit;
						transform: rotate(45deg);
					}

					.kartra_image--lsp-1-testimonoial {
						width: 70px;
					}

					.kartra_element_bg--testimonial-box {
						box-shadow: 0px 5px 20px rgba(0, 0, 0, .1);
						border-radius: 12px;
					}

					.kartra_element_bg--dot-sign {
						width: 8px;
						height: 8px;
						padding: 0px !important;
						position: relative;
					}

					.kartra_element_bg--who-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_element_bg--who-box .background-item {
						background-size: inherit !important;
						background-repeat: no-repeat;
						background-position: right bottom;
					}

					.kartra_icon--who-area.kartra_icon--giant {
						width: 56px;
						height: 56px;
					}

					.kartra_icon--who-area.kartra_icon--giant.kartra_icon--circled {
						width: 92px;
						height: 92px;
					}

					.kartra_element_bg--who-call-top-action {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_headline_block__info--padding-left-small {
						padding-left: 25px;
					}

					.kartra_icon--lsp-giant.kartra_icon--giant {
						font-size: 65px;
						width: 105px;
						height: 105px;
					}

					.kartra_element_bg--feature-box {
						border-radius: 10px;
						box-shadow: 0px 8px 40px rgba(0, 0, 0, .05);
					}

					.kartra_icon--pricing-med-icn.kartra_icon--medium {
						font-size: 30px;
					}

					.kartra_button1--price-btn.kartra_button1 {
						font-size: 20px;
						font-family: 'Quicksand', sans-serif;
						border-radius: 8px;
						padding: 16px;
						box-shadow: inset 0 -3px 0px rgba(0, 0, 0, 0.2), 0px 5px 30px rgba(0, 0, 0, .1);
					}

					.kartra_element_bg--lsp-pricing {
						box-shadow: 0px 5px 30px rgba(0, 0, 0, .05);
						border-radius: 10px;
					}

					.kartra_divider--border-tiny.kartra_divider--small {
						width: 170px;
					}

					/*Accordion*/
					.lsp-1--accordion.accordion-3 .panel {
						border: 2px solid #f5f5f5;
						border-radius: 10px;
						box-shadow: 0px 3px 10px rgba(0, 0, 0, .03)
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						color: #424242;
						font-size: 20px;
						font-weight: 500;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-title {
						border: 15px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon {
						border-top: 17px solid transparent;
						border-bottom: 17px solid transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element {
						border: 0px;
						color: rgb(255, 76, 0);
						font-size: 22px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-indicator .accordion-indicator-icon .accordion-indicator-icon-element i:before {
						content: "\f063";
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading:hover {
						background-color: transparent;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading+.panel-collapse>.panel-body,
					.accordion-3 .panel .panel-heading+.panel-collapse>.list-group {
						border-top: 1px solid #eee;
						margin: 0px 30px;
						padding: 24px 0px;
					}

					.lsp-1--accordion.accordion-3 .panel.active-panel .panel-body p {
						color: #37474e;
						font-size: 18px;
						line-height: 140%;
					}

					.lsp-1--accordion.accordion-3 .panel .panel-heading.accordion-panel-heading .accordion-panel-heading-content .accordion-title .panel-title {
						padding: 10px 15px;
					}

					.lsp-1--accordion.accordion-3 .panel+.panel {
						margin-top: 25px;
					}

					.kartra_element_bg--thumb-size-extra-medium {
						width: 70px;
						height: 70px;
					}

					.kartra_testimonial_author_block--padding-bottom-extra-small {
						padding-bottom: 20px;
					}

					@media(max-width: 991px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.2rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 0.85rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 0.9rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 0.85rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 0.85rem;
						}

						.column--sm-padding-bottom-none,
						.column--sm-padding-bottom-none .background-item {
							padding-bottom: 0px !important;
						}

						.column--sm-padding-top-extra-medium {
							padding-top: 40px !important;
						}

						.row--sm-margin-top-none {
							margin-top: 0px !important;
						}

						.column--sm-height-auto {
							min-height: auto !important;
						}

						.kartra_icon--lsp-icn-style-1.kartra_icon--giant {
							width: 100px;
							height: 100px;
							font-size: 50px;
						}

						.kartra_text--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.row--sm-margin-bottom-extra-medium {
							margin-bottom: 40px !important;
						}

						.sm-pull-left {
							float: left !important;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.1rem;
						}

						.content--sm-padding-bottom-none,
						.content--sm-padding-bottom-none .background_changer {
							padding-bottom: 0px !important;
						}

						.content--sm-padding-top-none,
						.content--sm-padding-top-none .background_changer {
							padding-top: 0px !important;
						}
					}

					@media(max-width: 767px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.15rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.15rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.05rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.2rem;
						}

						.kartra_item_info--lsp-1--size-medium {
							width: 30px;
						}

						.kartra_item_info__headline--xs-margin-top-negative-extra-tiny {
							margin-top: -5px !important;
						}

						.kartra_headline_block--flex {
							display: block !important;
						}

						.kartra_element_bg--who-call-top-action,
						.kartra_element_bg--who-call-top-action .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_element_bg--feature-box,
						.kartra_element_bg--feature-box .background-item {
							padding: 25px 25px 5px 25px !important;
						}

						.kartra_headline_block__info--padding-left-small {
							padding-left: 0px !important;
						}

						.kartra_text--xs-text-center {
							text-align: center !important;
						}

						.kartra_headline--xs-text-center {
							text-align: center !important;
						}
					}

					@media(max-width: 480px) {
						.kartra_headline--lsp-1-size-big-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-small {
							font-size: 1.4rem
						}

						.kartra_headline--lsp-1-size-extra-small {
							font-size: 1.3rem
						}

						.kartra_headline--lsp-1-size-tiny {
							font-size: 1.15rem
						}

						.kartra_text--lsp-1--size-large {
							font-size: 1.3rem;
						}

						.kartra_text--lsp-1--size-extra-medium {
							font-size: 1.2rem;
						}

						.kartra_text--lsp-1--size-small {
							font-size: 1.25rem;
						}

						.kartra_item_info__text--lsp-1--size-medium {
							font-size: 1.25rem;
						}

						.kartra_item_info__headline--h4 {
							font-size: 1.3rem;
						}

						.kartra_testimonial__author_details--padding-left-extra-small {
							padding-left: 20px !important;
						}

						.kartra_item_info__headline--xxs-negative-margin-top-big-tiny {
							margin-top: -8px !important;
						}
					}

					@media (max-width: 767px) {
						.kartra_text--xs-text-center {
							text-align: inherit;
						}

						.kartra_headline--xs-text-center {
							text-align: inherit;
						}

						.kartra_element_bg--xs-padding-left-right-special-medium {
							padding-left: 30px !important;
							padding-right: 30px !important;
						}

						.kartra_text--xs-text-center-important {
							text-align: center;
						}

						.content--xs-padding-bottom-none-important {
							padding-bottom: 0px !important;
						}
					}
				</style>

				<div class="background_changer background_changer--blur0" style="opacity: 1; background-image: none;">
				</div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container">
					<div class="row" data-component="grid" id="accordion-SpBo0kHmtL">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div class="social_share_wrapper pull-center" data-component="bundle"
									id="0qd9dLLbJ3_AP6WD5frXN" style="margin: 0px auto;">
									<div data-component="social_share">
										<span
											class="facebook_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-facebook kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
												class="kartra_icon__icon fa fa-facebook-square"></span><span
												class="social_share__text">Share</span></span>
									</div>
									<div data-component="social_share">
										<span
											class="twitter_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-twitter kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
												class="kartra_icon__icon fa fa-twitter"></span><span
												class="social_share__text">Post</span></span>
									</div>


									<div data-component="social_share">
										<span
											class="whatsapp_share pull-center kartra_social_share1 kartra_social_share1--small kartra_social_share1--bg-whatsapp kartra_social_share1--white kartra_social_share1--font-weight-bold kartra_social_share1--icon-left-border-right"><span
												class="kartra_icon__icon fa fa-whatsapp"></span><span
												class="social_share__text">Share</span></span>
									</div>
								</div>
								<div data-component="image"><img
										class="kartra_image kartra_image--full pull-center background_changer--blur0"
										src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
										onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
										style="margin: 0px auto 20px; border-radius: 0px; opacity: 1;" alt=""
										data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/376791092554Purple_and_Yellow_Geometric_Business_Consulting_Services_Instagram_Post__Medium_Banner__US___Landscape____1_.png">
								</div>
								<div data-component="countdown" id="qii73jtydC">
									<div class="countdown-section countdown-section--text-center countdown-section--margin-bottom-extra-small"
										data-countdown-id="YDSvmKehOT_h4Gkq55n14"
										style="margin-top: 0px; margin-bottom: 20px;" data-countdown="fixed"
										data-date="12/08/2024" data-time="23:59" data-asset-id="3"
										data-timestamp="1733723940">
										<div class="countdown">
											<div
												class="countdown__item countdown__item--day countdown__item--white countdown__item--space-extra-tiny countdown__item--space-extra-tiny">
												<div
													class="digit-list digit-list--bg-black-opaque-20 digit-list--roboto-font digit-list--element-size-06">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
												<div><span
														class="countdown_title countdown_title--roboto-font countdown_title--letter-spacing-tiny countdown_title--cc-title-06 countdown_title--margin-top-tiny">DAYS</span>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--hours countdown__item--white countdown__item--space-extra-tiny countdown__item--space-extra-tiny">
												<div
													class="digit-list digit-list--bg-black-opaque-20 digit-list--roboto-font digit-list--element-size-06">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
												<div><span
														class="countdown_title countdown_title--roboto-font countdown_title--letter-spacing-tiny countdown_title--cc-title-06 countdown_title--margin-top-tiny">HOURS</span>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--minutes countdown__item--white countdown__item--space-extra-tiny countdown__item--space-extra-tiny">
												<div
													class="digit-list digit-list--bg-black-opaque-20 digit-list--roboto-font digit-list--element-size-06">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
												<div><span
														class="countdown_title countdown_title--roboto-font countdown_title--letter-spacing-tiny countdown_title--cc-title-06 countdown_title--margin-top-tiny">MINUTES</span>
												</div>
											</div>
											<div
												class="countdown__item countdown__item--seconds countdown__item--white countdown__item--space-extra-tiny countdown__item--space-extra-tiny">
												<div
													class="digit-list digit-list--bg-black-opaque-20 digit-list--roboto-font digit-list--element-size-06">
													<div class="digit-list__item">0</div>
													<div class="digit-list__item">0</div>
												</div>
												<div><span
														class="countdown_title countdown_title--roboto-font countdown_title--letter-spacing-tiny countdown_title--cc-title-06 countdown_title--margin-top-tiny">SECONDS</span>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div data-component="button" id="vVY5jEhqXc"><a href="javascript:void(0);" onclick="openPaymentSelection()"
										class="kartra_button11 kartra_button11--lsp-1-two-line-btn kartra_button11--default kartra_button11--gradient kartra_button11--solid kartra_button11--full-width kartra_button11--squared kartra_button11--shadow-small pull-center toggle_product js_kartra_trackable_object"
										style="background-color: rgb(61, 170, 19); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: Lato; border-radius: 10px;"
										target="_parent"><span
											class="kartra_button11_text" style="font-weight: 700;"><span
												class="kartra_button11_text_first_line"
												style="font-weight: 700;">REGISTRATE AHORA!</span><span
												class="kartra_button11_text_second_line"
												style="font-weight: 700;">(solamente $49.99)</span></span></a></div>
								<div data-component="text" id="accordion-KP7OJnts14">
									<div class="kartra_text kartra_text--text-center kartra_text--white kartra_text--font-weight-regular"
										style="position: relative;" aria-controls="cke_891" aria-activedescendant=""
										aria-autocomplete="list" aria-expanded="false">
										<p>Copyright © 2024. Cooper Wellness Center. All Rights Reserved.</p>
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
	<script src="//app.kartra.com/resources/js/page_check?page_id=DFXHG1pE9bZa" async defer></script>
	<script>
	if ( typeof window['jQuery'] !== 'undefined') {
		window.jsVars = { "page_title": "14-Day Skinny Tummy Challenge", "page_description": "Transform your health and vitality and get the end-of-year body you truly deserve and long for. Finish the year strong and fabulous!", "page_keywords": "14-day fitness challenge, weight-loss challenge, tummy challenge, shed fat fast, 14 days to amazing health, skinny challenge, skinny tummy challenge, burn fat fast, shed pounds, holiday weight-loss, holiday health challenge, holiday fitness challenge", "page_robots": "index, follow", "secure_base_url": "\/\/app.kartra.com\/", "global_id": "DFXHG1pE9bZa" };
	window.global_id = 'DFXHG1pE9bZa';
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
									data-type-id="1699" data-type-owner="DpwDQa6g">
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