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
    <title>Virtual Health and Wellness Program McAllen Texas</title>
    <meta name="description"
        content="Adopt our highly competitive wellness program packages, include a weekly class of 60-70 minutes and change your lifestyle. Start from $250 only.">
    <meta name="keywords" content="Wellness Program Package USA, Spiritual healing classes Tx, Cooper Weight Loss">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image"
        content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9489409_1588095698774hypertension-check.jpg">

    <!-- Font icons preconnect -->
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="//d2uolguxr56s4e.cloudfront.net" crossorigin>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//d2uolguxr56s4e.cloudfront.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--scripts for payments processing-->
    <script>
        let p_price;
        let program;
        let pID;
        let instanceNumber;
        // Function to open the modal
        function openPaymentSelection(instanceNumber) {
            document.getElementById('paymentSelection').style.display = 'flex';
    
            const paypalButton = document.getElementById('paypalButton');
            const mobileMoneyButton = document.getElementById('mobileMoneyButton');
            
            // Define redirection links for each instance
            const links = {
                paypal: {
                    1: "https://cooperwellness.kartra.com/checkout/7578df5e5811f9376aa2052f34bacdeb",
                    2: "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=F5W46GG9FJWRC&kuid=cc723a83-e360-46ff-9138-5d60ffa7223d-1730997341&kref=pByILe3giEoQ"
                },
                mobileMoney: {
                    1: function() {
                        // console.log("Processing Mobile Money for Instance 1.");
                        openPaymentPopup(1); // Pass instanceNumber 1
                    },
                    2: function() {
                        // console.log("Processing Mobile Money for Instance 2.");
                        openPaymentPopup(2); // Pass instanceNumber 2
                    }
                }
            };
            
            // Set PayPal button link
            paypalButton.href = links.paypal[instanceNumber] || "#";
    
            // Set Mobile Money button functionality
            mobileMoneyButton.onclick = links.mobileMoney[instanceNumber] || function() { alert("An error occured with product. \n Try again"); };
    
            // console.log(`PayPal button set to: ${paypalButton.href}`);
        }

        function closePaymentSelection() {
            document.getElementById('paymentSelection').style.display = 'none';
        }

        // Function to show the selected tab
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(function (tab) {
                tab.style.display = 'none';
            });
            document.getElementById(tabId).style.display = 'block';
        }

        // Close the modal when clicking outside of it
        window.onclick = function(event) {
            const paymentSelectionModal = document.getElementById("paymentSelection");
            const paymentModal = document.getElementById("paymentModal");
            if (event.target == paymentSelectionModal) {
                paymentSelectionModal.style.display = "none";
            } else if (event.target == paymentModal) {
                paymentModal.style.display = "none";
            }
        };


        // new momo checkout
        function openPaymentPopup(instanceNumber) {
            // console.log("Fetching payment details for instance:", instanceNumber);

            const formData = new FormData();
            formData.append('instanceNumber', instanceNumber);
        
            fetch('programs.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // console.log("Response status:", response.status);
                return response.json(); // Parse JSON
            })
            .then(data => {
                console.log("Data received from server:", data);
                if (data.success) {
                    document.getElementById('price').innerText = data.price;
                    // document.getElementById('amount').innerText = data.price;
                    document.getElementById('prog_name').innerText = data.prog;
                    program = data.prog;
                    pID = data.id;
                    document.getElementById('prog_price').value = data.price;
                    p_price = data.price;

                    // console.log('checking type of price returned', p_price, typeof p_price);
                    // document.getElementById('rate').innerText = data.rate;
                    document.getElementById('usd_price').innerText = data.price_usd;
                    // console.log("Program:", program);
                    // console.log("Price in USD:", data.price_usd);
                    // console.log("Exchange rate:", data.rate);
                    // document.getElementById('p_ID').value = pID;
                    document.getElementById('paymentModal').style.display = 'flex';
                } else {
                    console.error("Error: Failed to fetch price details. Server responded with:", data);
                    alert(`Error, ${data.message}`);
                }
            })
            .catch(err => {
                // Log the error for debugging
                console.error('Error fetching price:', err);
                alert('An unexpected error occurred. Please try again later.');
            });
        }
        // Close the modal when clicking outside of it
		window.onclick = function(event) {
			var modal = document.getElementById("paymentModal");
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
        
        function payWithPaystackforPrograms(e) {
            e.preventDefault();
            const email1 = document.getElementById("email1").value;
            // console.log('Paystack email:', email1);
            const phone = document.getElementById("phone").value;
            const prog_name = program;
            console.log("PID:", pID);
            const price = p_price;
            // console.log('Paystack price:', price, typeof price);
            // const amountInPesewas = Math.round(price * 100);
            const amountInPesewas = price * 100;

            const paystackPublicKey = "pk_test_f5b5f05ffa20e04d5a54bedf16e0605ddab5281c";
                                    
            // Initialize Paystack payment
            const handler = PaystackPop.setup({
                key: paystackPublicKey,
                email: email1,
                amount: amountInPesewas,
                currency: "GHS",
                ref: "PROG" + Math.floor((Math.random() * 1000000000) + 1),
                metadata: {
                    custom_fields: [
                        {
                            display_name: "Phone",
                            variable_name: "phone",
                            value: phone
                        },
                        {
                            display_name: "Program",
                            variable_name: "prog_name",
                            value: prog_name
                        },
                        {
                            display_name: "Program ID",
                            variable_name: "pID",
                            value: pID
                        }
                    ]
                },
                callback: function(response) {
                    // console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Payment Successful!',
                        text: 'Reference: ' + response.reference,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = "pay/program_pay.php?reference=" + response.reference;
                    });
                },
                onClose: function() {
                    // Transaction was not completed, use SweetAlert for cancel message
                    Swal.fire({
                        icon: 'info',
                        title: 'Transaction Cancelled',
                        text: 'Transaction was not completed. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
                                    
            handler.openIframe(); // Open the inline modal
        }
    </script>

    <!--
        Google fonts are computed and loaded on page build via save.js
        Individual stylesheets required are listed in /css/pages/skeleton.css
    -->

    <!--<link href="cssskeleton.min.css" rel="stylesheet">-->
    <link type="text/css" rel="preload"
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Noto+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Zeyada:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Noto+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Source+Sans+Pro:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Zeyada:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
    </noscript>

    <script>
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        (function (w) {
            "use strict";
            if (!w.loadCSS) {
                w.loadCSS = function () { }
            }
            var rp = loadCSS.relpreload = {};
            rp.support = function () {
                var ret;
                try {
                    ret = w.document.createElement("link").relList.supports("preload")
                } catch (e) {
                    ret = false
                }
                return function () {
                    return ret
                }
            }();
            rp.bindMediaToggle = function (link) {
                var finalMedia = link.media || "all";

                function enableStylesheet() {
                    link.media = finalMedia
                }
                if (link.addEventListener) {
                    link.addEventListener("load", enableStylesheet)
                } else if (link.attachEvent) {
                    link.attachEvent("onload", enableStylesheet)
                }
                setTimeout(function () {
                    link.rel = "stylesheet";
                    link.media = "only x"
                });
                setTimeout(enableStylesheet, 3e3)
            };
            rp.poly = function () {
                if (rp.support()) {
                    return
                }
                var links = w.document.getElementsByTagName("link");
                for (var i = 0; i < links.length; i++) {
                    var link = links[i];
                    if (link.rel === "preload" && link.getAttribute("as") === "style" && !link.getAttribute("data-loadcss")) {
                        link.setAttribute("data-loadcss", true);
                        rp.bindMediaToggle(link)
                    }
                }
            };
            if (!rp.support()) {
                rp.poly();
                var run = w.setInterval(rp.poly, 500);
                if (w.addEventListener) {
                    w.addEventListener("load", function () {
                        rp.poly();
                        w.clearInterval(run)
                    })
                } else if (w.attachEvent) {
                    w.attachEvent("onload", function () {
                        rp.poly();
                        w.clearInterval(run)
                    })
                }
            }
            if (typeof exports !== "undefined") {
                exports.loadCSS = loadCSS
            } else {
                w.loadCSS = loadCSS
            }
        })(typeof global !== "undefined" ? global : this);

        window.global_id = 'pByILe3giEoQ';
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
            background-color: #eeeeee;

        }


        [data-effect] {
            visibility: hidden;
        }
    </style>
    <script>
        var google_analytics = null; <
            !--Global site tag(gtag.js) - Google Analytics-- >
                <
			script async src="https://www.googletagmanager.com/gtag/js?id=UA-175519445-2" >
    </script>
    <script>
                    window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
		}
                    gtag('js', new Date());

                    gtag('config', 'UA-175519445-2');
    </script>

    <script type="text/javascript">
                    (function(c, l, a, r, i, t, y) {
                        c[a] = c[a] || function () {
                            (c[a].q = c[a].q || []).push(arguments)
                        };
                    t = l.createElement(r);
                    t.async = 1;
                    t.src = "https://www.clarity.ms/tag/" + i;
                    y = l.getElementsByTagName(r)[0];
                    y.parentNode.insertBefore(t, y);
		})(window, document, "clarity", "script", "aqpjjtqipr");
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-217187331-1"></script>
    <script>
                    window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
		}
                    gtag('js', new Date());

                    gtag('config', 'UA-217187331-1');
    </script>


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-217187331-1"></script>
    <script>
                    window.dataLayer = window.dataLayer || [];

                    function gtag() {
                        dataLayer.push(arguments);
		}
                    gtag('js', new Date());

                    gtag('config', 'UA-217187331-1');
    </script>


    <script type="text/javascript">
                    (function(c, l, a, r, i, t, y) {
                        c[a] = c[a] || function () {
                            (c[a].q = c[a].q || []).push(arguments)
                        };
                    t = l.createElement(r);
                    t.async = 1;
                    t.src = "https://www.clarity.ms/tag/" + i;
                    y = l.getElementsByTagName(r)[0];
                    y.parentNode.insertBefore(t, y);
		})(window, document, "clarity", "script", "aqpjjtqipr");
    </script>
    <link rel=“canonical” href=“https://www.cooperwellnesscenter.com/” />


    </script>

    <script src="js/build/front/pages/skeleton-above.js"></script>
    <link rel="preconnect" href="//vip.timezonedb.com">
    <link rel="dns-prefetch" href="//vip.timezonedb.com">
    <style id="pagesCustomCSS">
        .kartra_icon-negative-margin-top-special-extra-tiny-important {
            margin-top: -6px !important;
        }

        .kartra_element_bg--winner-header {
            display: table;
            margin-left: 20px !important;
        }

        @media(max-width: 991px) {
            .kartra_headline--sm-margin-top-extra-small-important {
                margin-top: 20px !important;
            }

            .row--sm-margin-bottom-extra-small-important {
                margin-bottom: 20px !important;
            }
        }

        @media(max-width: 767px) {
            .kartra_element_bg--xs-padding-left-right-extra-small-important {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
        }

        @media(min-width: 992px) {
            .column--md-padding-right-extra-medium-important {
                padding-right: 40px !important;
            }
        }

        .background-item--rounded-top-small-tiny-important {
            border-radius: 0px 0px 4px 4px !important;
        }
    </style>
</head>

<body>
    <div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object"
        data-kt-type="kartra_page_tracking" data-kt-value="pByILe3giEoQ" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content content--popup-overflow-visible"
                style="background-color: rgb(255, 255, 255); padding: 0px;" id="_66bbb30d32b13">
                <div class="overflow_background_wrapper">
                    <div class="background_changer background_changer--blur0"
                        style="background-image: none; opacity: 1;"></div>
                    <div class="background_changer_overlay" style="background-image: none;"></div>
                </div>
                <nav class="navbar navbar-inverse navbar-light navbar-light--border-bottom-light">
                    <div class="kartra_element_bg kartra_element_bg--padding-top-bottom-tiny"
                        style="margin-top: 0px; margin-bottom: 0px; padding: 10px 0px;">
                        <div style="background-color: rgb(243, 113, 33); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1;"
                            class="background-item background_changer--blur0"></div>
                        <div class="container">
                            <div class="row row--equal row--xs-equal" data-component="grid">
                                <div class="column--vertical-center col-xs-6">
                                    <div class="js_kartra_component_holder js_kartra_component_holder--min-height-auto">
                                        <div class="kartra_headline_block kartra_headline_block--flex kartra_headline_block--vertical-center kartra_headline_block--justify-content-start"
                                            data-component="bundle">
                                            <div class="kartra_headline_block__index">
                                                <div data-component="icon" href="javascript: void(0);">
                                                    <div class="kartra_icon kartra_icon--margin-left-negative-like-tiny kartra_icon--white kartra_icon--top-adjust kartra_icon--royal-blue kartra_icon--small"
                                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                        <span class="kartra_icon__icon fa fa-phone-square"
                                                            style="color: rgb(49, 85, 40);"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="kartra_headline_block__info">
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--dim-black kartra_text--extra-small kartra_text--font-weight-medium kartra_text--margin-bottom-none"
                                                        style="position: relative;">
                                                        <p>(956) 627-3106</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column--vertical-center col-xs-6">
                                    <div class="js_kartra_component_holder js_kartra_component_holder--min-height-auto">
                                        <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--sm-align-center social_icons_wrapper--margin-bottom-extra-small social_icons_wrapper--negative-margin-left-right-extra-tiny pull-right"
                                            data-component="bundle" id="FaDTPsEGB8_xcO7anT4eY"
                                            style="margin: 0px -5px 20px;">
                                            <div data-component="icon">
                                                <a href="https://www.facebook.com/cooperwellness/"
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d32b13"
                                                    target="_blank">
                                                    <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                        <span style="color: rgb(255, 255, 255);"
                                                            class="kartra_icon__icon fa fa-facebook"></span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div data-component="icon" href="javascript: void(0);">
                                                <a href="https://www.instagram.com/cooperwellnesscenter/"
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d32b13"
                                                    target="_blank">
                                                    <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                        <span style="color: rgb(255, 255, 255);"
                                                            class="kartra_icon__icon fa fa-instagram"></span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div data-component="icon" href="javascript: void(0);">
                                                <a href="https://www.youtube.com/channel/UCihzseMaylCivEhN5lN9Peg/?sub_confirmation=1"
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d32b13"
                                                    target="_blank">
                                                    <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                        <span style="color: rgb(255, 255, 255);"
                                                            class="kartra_icon__icon fa fa-youtube-square"></span>
                                                    </div>
                                                </a>
                                            </div>
                                            <div data-component="icon" href="javascript: void(0);">
                                                <a href="https://twitter.com/DrCooperDockery" class="toggle_pagelink"
                                                    data-frame-id="_66bbb30d32b13" target="_blank">
                                                    <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                        style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                        <span style="color: rgb(255, 255, 255);"
                                                            class="kartra_icon__icon fa fa-twitter"></span>
                                                    </div>
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="container nav-elem-wrapper nav-elem-wrapper--md-sm-flex nav-elem-wrapper--md-sm-vertical-center nav-elem-wrapper--md-sm-justify-content-space-between">
                        <div class="navbar-header nav-elem-col">
                            <div data-component="image">
                                <a href="../"
                                    data-frame-id="_66bbb30d32b13" class="toggle_pagelink" data-project-id="3"
                                    data-page-id="111" target="_parent">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                                        </source><img
                                            class="kartra_image kartra_image--logo kartra_image--margin-bottom-none pull-left background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            style="border-color: rgb(49, 85, 40); border-style: none; border-width: 0px; margin: 0px; opacity: 1;"
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                                    </picture>
                                </a>
                            </div>
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                data-target="#navbar_9vxxjb5a2X" aria-expanded="false" aria-controls="navbar">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        <div id="navbar_HjFs14RpbR"
                            class="navbar-collapse collapse nav-elem-col js_kartra_component_holder navbar-collapse--md-sm-padding-right-none"
                            style="">
                            <div
                                class="navbar-collapse__inner navbar-collapse__inner--sm-padding-top-big-tiny navbar-collapse__inner--sm-padding-bottom-tiny navbar-collapse__inner--md-sm-vertical-center navbar-collapse__inner--md-sm-justify-content-end js_kartra_component_holder">
                                <ul
                                    class="nav navbar-nav navbar-right navbar-nav--md-padding-top-bottom-special-medium navbar-nav--sm-padding-top-bottom-big-tiny">
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="../" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: Roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="111" target="_parent">HOME</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="../about" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="107" target="_parent">ABOUT</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="../books" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="181" target="_parent">BOOKS</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="../courses" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="191" target="_parent">COURSES</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="../programs" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="201" target="_parent">PROGRAMS</a>
                                    </li>
                                </ul>
                                <div class="inline_elements_wrapper pull-right xs-pull-center inline_elements_wrapper--last-child-margin-bottom-none inline_elements_wrapper--md-sm-margin-left-small inline_elements_wrapper--xs-margin-top-big-tiny"
                                    style="justify-content: center;">
                                    <!--	<div data-component="button" style="width: auto;">-->
                                    <!--		<a href="supplements" class="kartra_button1 kartra_button1--icon-right kartra_button1--md-sm-margin-top-extra-small kartra_button1--hollow kartra_button1--small kartra_button1--rounded pull-center toggle_pagelink" onmouseover="this.style.color='#fff';this.style.backgroundColor='rgb(49, 85, 40)';this.style.borderColor='rgb(49, 85, 40)';if (this.querySelector('.fa')) this.querySelector('.fa').style.color='#fff'" onmouseout="this.style.color='rgb(49, 85, 40)';this.style.borderColor='rgb(49, 85, 40)';this.style.backgroundColor='transparent';if (this.querySelector('.fa')) this.querySelector('.fa').style.color='rgb(49, 85, 40)'" style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); background-color: transparent; font-weight: 700; font-family: Lato; margin: 20px auto;" data-frame-id="_5feca5d6e913f" data-project-id="3" data-page-id="25" target="_parent">SUPPLEMENTS<span class="kartra_icon__icon fa fa-chevron-right" data-color="rgb(255, 255, 255)" style="font-weight: 700; color: rgb(49, 85, 40);"></span></a>-->
                                    <!--	</div>-->
                                    <div data-component="button" style="width: auto;">
                                        <a href="../contact"
                                            class="kartra_button1 kartra_button1--royal-blue kartra_button1--icon-right kartra_button1--md-sm-margin-top-extra-small kartra_button1--solid kartra_button1--small kartra_button1--rounded pull-center toggle_pagelink"
                                            style="font-weight: 700; background-color: rgb(49, 85, 40); color: rgb(255, 255, 255); margin: 20px auto; font-family: lato;"
                                            data-frame-id="_5feca5d6e913f" data-project-id="3" data-page-id="112"
                                            target="_parent">CONTACT<span class="kartra_icon__icon fa fa-chevron-right"
                                                style="color: rgba(255, 255, 255, 0.4); border-color: rgba(255, 255, 255, 0.4); font-weight: 700;"></span></a>
                                    </div>
                                    <div data-component="button" style="width: auto;">
                                        <a onclick="openDoctorMeetingModal(event)"
                                            class="kartra_button1 kartra_button1--icon-right kartra_button1--md-sm-margin-top-extra-small kartra_button1--hollow kartra_button1--small kartra_button1--rounded pull-center toggle_pagelink"
                                            onmouseover="this.style.color='#fff';this.style.backgroundColor='rgb(49, 85, 40)';this.style.borderColor='rgb(49, 85, 40)';if (this.querySelector('.fa')) this.querySelector('.fa').style.color='#fff'"
                                            onmouseout="this.style.color='rgb(49, 85, 40)';this.style.borderColor='rgb(49, 85, 40)';this.style.backgroundColor='transparent';if (this.querySelector('.fa')) this.querySelector('.fa').style.color='rgb(49, 85, 40)'"
                                            style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); background-color: transparent; font-weight: 700; font-family: Lato; margin: 20px auto;">Meet
                                            A Doctor
                                            <i class="fas fa-calendar-check"
                                                style="font-size: 24px; color: #4CAF50;"></i>
                                        </a>
                                    </div>
                                    <div data-component="button" style="width: auto;">
                                        <!--<a class="kartra_button1 kartra_button1--royal-blue kartra_button1--icon-right kartra_button1--md-sm-margin-top-extra-small kartra_button1--solid kartra_button1--small kartra_button1--rounded pull-center toggle_pagelink"-->
                                        <!--    style="font-weight: 700; background-color: rgb(49, 85, 40); color: rgb(255, 255, 255); margin: 20px auto; font-family: lato;"-->
                                        <!--    data-frame-id="_66bbb30d1e28e" data-project-id="3" data-page-id="112" onclick="openDoctorMeetingModal(event)">-->
                                        <style>
                                            .tooltip-text {
                                                visibility: hidden;
                                                width: 200px;
                                                background-color: #333;
                                                color: #fff;
                                                text-align: center;
                                                padding: 8px;
                                                border-radius: 5px;

                                                /* Position the tooltip above the icon */
                                                position: absolute;
                                                bottom: 125%;
                                                /* Move it above */
                                                left: 50%;
                                                transform: translateX(-50%);

                                                /* Tooltip arrow */
                                                opacity: 0;
                                                transition: opacity 0.3s;
                                            }

                                            /* Arrow below the tooltip */
                                            .icon-container .tooltip-text::after {
                                                content: "";
                                                position: absolute;
                                                top: 100%;
                                                /* Position at the bottom of tooltip */
                                                left: 50%;
                                                transform: translateX(-50%);
                                                border-width: 5px;
                                                border-style: solid;
                                                border-color: #333 transparent transparent transparent;
                                            }

                                            /* Show the tooltip text on hover */
                                            .icon-container:hover .tooltip-text {
                                                visibility: visible;
                                                opacity: 1;
                                            }
                                        </style>
                                        <!--    <i class="fas fa-calendar-check" style="font-size: 24px; color: #4CAF50;"></i>-->
                                        <!--    <span class="tooltip-text">Book an appointment with a doctor</span>-->
                                        <!--</a>-->
                                    </div>
                                </div>

                                <style>
                                    /* General Modal Background Styling */
                                    .modal-background {
                                        display: none;
                                        /* Hidden by default */
                                        position: fixed;
                                        top: 0;
                                        left: 0;
                                        width: 100%;
                                        height: 100%;
                                        background-color: rgba(0, 0, 0, 0.6);
                                        z-index: 1000;
                                        justify-content: center;
                                        align-items: center;
                                    }

                                    /* Modal Content Styling */
                                    .modal-content {
                                        background-color: #fff;
                                        padding: 10px 20px 10px 20px;
                                        border-radius: 8px;
                                        width: 90%;
                                        max-width: 500px;
                                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                                        text-align: left;
                                        position: relative;
                                        overflow-y: scroll;
                                    }

                                    /* Close Button */
                                    .close {
                                        position: absolute;
                                        top: 10px;
                                        right: 15px;
                                        font-size: 24px;
                                        font-weight: bold;
                                        color: #333;
                                        cursor: pointer;
                                    }

                                    /* Form Header */
                                    h2 {
                                        margin-top: 0;
                                        color: #333;
                                        font-family: Arial, sans-serif;
                                        font-size: 24px;
                                        text-align: center;
                                    }

                                    /* Input and Textarea Styling */
                                    label {
                                        display: block;
                                        margin-top: 10px;
                                        color: #555;
                                        font-family: Arial, sans-serif;
                                        font-size: 14px;
                                    }

                                    input[type="text"],
                                    input[type="email"],
                                    input[type="number"],
                                    input[type="datetime-local"],
                                    textarea {
                                        width: 100%;
                                        padding: 10px;
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        font-size: 14px;
                                        box-sizing: border-box;
                                        font-family: Arial, sans-serif;
                                        resize: none;
                                    }

                                    textarea {
                                        height: 80px;
                                    }

                                    /* Submit Button Styling */
                                    button[type="submit"] {
                                        width: 100%;
                                        padding: 12px;
                                        background-color: #007bff;
                                        border: none;
                                        border-radius: 5px;
                                        color: white;
                                        font-size: 16px;
                                        font-weight: bold;
                                        cursor: pointer;
                                        margin-top: 10px;
                                        transition: background-color 0.3s ease;
                                        font-family: Arial, sans-serif;
                                    }

                                    button[type="submit"]:hover {
                                        background-color: #0056b3;
                                    }

                                    /* Loader Styling */
                                    #loader {
                                        display: none;
                                        margin-top: 10px;
                                        font-size: 16px;
                                        color: #007bff;
                                        font-weight: bold;
                                        text-align: center;
                                    }

                                    /* Response Message Styling */
                                    #responseMessage {
                                        margin-top: 15px;
                                        font-size: 14px;
                                        color: #28a745;
                                        text-align: center;
                                    }
                                </style>

                                <!-- Schedule Tour Modal -->
                                <div id="meetModal" class="modal-background">
                                    <div class="modal-content">
                                        <span class="close" onclick="closeDoctorMeetingModal()">&times;</span>
                                        <h2>Book a Virtual Meeting</h2>
                                        <form id="visitForm" onsubmit="payWithPaystack(event)">
                                            <label for="name">Name:</label>
                                            <input type="text" id="name" name="name" required>

                                            <label for="email">Email:</label>
                                            <input type="email" id="email" name="email" required>

                                            <label for="number">Phone Number:</label>
                                            <input type="number" id="number" name="number" required>

                                            <label for="visitDateTime">Preferred Date and Time:</label>
                                            <input type="datetime-local" id="visitDateTime" name="visitDateTime"
                                                required>

                                            <label for="reason">Reason:</label>
                                            <textarea id="reason" name="reason" placeholder="Reason"
                                                required></textarea>

                                            <!-- Duration Field with Increment and Decrement Buttons -->
                                            <label for="duration">Consultation Duration:</label>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <button type="button" onclick="updateDuration(-30)"
                                                    style="background-color:#ff1a1a; color: white; font-weight:500; width:30px; height:30px;">-</button>
                                                <input type="number" id="duration" name="duration" value="30" min="30"
                                                    step="30" readonly style="width: 60px; text-align: center;">
                                                <button type="button" onclick="updateDuration(30)"
                                                    style="background-color:#4CAF50; color: white; font-weight: 500; width:30px; height:30px;">+</button>
                                            </div>
                                            <p style="color: #ff1a1a; font-size: 14px; font-family: Arial, sans-serif;">
                                                Each 30 minutes costs $75. You will be charged in the equivalent of your
                                                local cuurency <br /> Amount Payable: <span id="totalCost">$75</span>
                                            </p>

                                            <button type="submit">Submit Request</button>

                                            <!-- Loader -->
                                            <div id="loader">Submitting...</div>

                                            <!-- Response Message -->
                                            <div id="responseMessage"></div>
                                        </form>
                                    </div>
                                </div>

                                <script>
                // Open modal
                function openDoctorMeetingModal(event) {
                    event.preventDefault();
                document.getElementById("meetModal").style.display = "flex";
                                        }

                // Close modal
                function closeDoctorMeetingModal() {
                    document.getElementById("meetModal").style.display = "none";
                                        }

                // Close modal on outside click
                window.onclick = function(event) {
                                            var modal = document.getElementById("meetModal");
                if (event.target == modal) {
                    modal.style.display = "none";
                                            }
                                        }

                // Set minimum datetime to prevent past selection
                const visitDateTime = document.getElementById("visitDateTime");
                const now = new Date();
                visitDateTime.min = now.toISOString().slice(0, 16);

                // Declare cost as a global variable
                let cost = 0;

                // Update duration to cost
                function updateDuration(change) {
                                            const durationInput = document.getElementById("duration");
                const totalCostElement = document.getElementById("totalCost");
                let duration = parseInt(durationInput.value) + change;

                                            // Minimum duration is 30 minutes
                                            if (duration >= 30) {
                    durationInput.value = duration;
                cost = 75 * (duration / 30); // Update the global cost variable
                totalCostElement.innerText = `$${cost}`;
                                            }
                                        }

                // Conversion to cedis API with Open Exchange Rates
                const openExchangeAppID = '0d6f5687149b407fb1c561d00ecdb908';
                const openExchangeUrl = `https://openexchangerates.org/api/latest.json?app_id=${openExchangeAppID}&symbols=GHS&base=USD`;


                // Conversion to cedis
                async function fetchOpenExchangeData(cost) {
                                            try {
                                                const response = await fetch(openExchangeUrl);
                const exchangeData = await response.json();
                // console.log("Exchange Data:", exchangeData);
                // console.log("exchange rate object:", exchangeData.rates);

                // Verify if API returned expected data
                if (exchangeData && exchangeData.rates && exchangeData.timestamp) {
                    // console.log("exchange rate from api:", exchangeData.rates.GHS);
                // console.log("Cost object:", cost);
                const usdToGhsRate = exchangeData.rates.GHS;
                const priceInGhs = (cost * usdToGhsRate).toFixed(2);
                return priceInGhs;
                                                } else {
                                                    throw new Error("Failed to retrieve exchange rate data");
                                                }
                                            } catch (error) {
                    console.error("Error fetching currency data:", error);
                return null;
                                            }
                                        }

                // Payment script
                async function payWithPaystack(e) {
                    e.preventDefault();
                const email = document.getElementById("email").value;
                const phone = document.getElementById("number").value;
                const name = document.getElementById("name").value;
                const datetime = document.getElementById("visitDateTime").value;
                const reason = document.getElementById("reason").value;
                const duration = parseInt(document.getElementById("duration").value);

                // Calculate cost in USD
                const cost = (75 * (duration / 30)).toFixed(2);

                // Convert USD cost to GHS
                const priceInGhs = await fetchOpenExchangeData(cost);

                // Check if the price conversion succeeded
                if (!priceInGhs) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Conversion Error',
                        text: 'Unable to retrieve conversion rate. Please try again later.'
                    });
                return;
                                            }

                // Calculate amount in pesewas for Paystack (GHS to pesewas)
                const amountInPesewas = Math.round(priceInGhs * 100);

                const paystackPublicKey = "pk_test_f5b5f05ffa20e04d5a54bedf16e0605ddab5281c";

                // Initialize Paystack payment
                const handler = PaystackPop.setup({
                    key: paystackPublicKey,
                email: email,
                amount: amountInPesewas,
                currency: "GHS",
                ref: "VMeet" + Math.floor((Math.random() * 1000000000) + 1),
                metadata: {
                    custom_fields: [
                {display_name: "Phone", variable_name: "phone", value: phone },
                {display_name: "Duration", variable_name: "duration", value: duration },
                {display_name: "Datetime", variable_name: "datetime", value: datetime },
                {display_name: "Name", variable_name: "name", value: name },
                {display_name: "Reason", variable_name: "reason", value: reason },
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
                        window.location.href = "../pay/meeting_pay.php?reference=" + response.reference;
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

                handler.openIframe(); // Open Paystack inline modal
                                        }
                                </script>
                            </div>
                        </div>
                        <!--/.nav-collapse -->
                    </div>
                </nav>
            </div>
            <!--<div class="content" style="background-color:rgb(48,63,159);" id="_b45zlp5y0">-->
            <!--    <div class="background_changer"></div>-->
            <!--    <div class="background_changer_overlay"></div>-->
            <!--    <div class="container-fluid">-->
            <!--        <div class="row row--equal" data-component="grid">-->
            <!--            <div class="col-md-7 column--vertical-center column--padding-top-bottom-large column--padding-left-extra-large column--padding-right-doubble-extra-large column--sm-padding-extra-medium"-->
            <!--                style="margin-top: 0px; margin-bottom: 0px; padding: 100px 140px 100px 70px;">-->
            <!--                <div style="background-color: rgb(243, 113, 33); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"-->
            <!--                    class="background-item background_changer--blur0 js-bg-next-gen" alt=""-->
            <!--                    data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/17589195_1614761233k9osecond-section.png")'>-->
            <!--                </div>-->
            <!--                <div class="js_kartra_component_holder">-->
            <!--                    <div data-component="video">-->
            <!--                        <div class="kartra_video kartra_video--player_1"-->
            <!--                            style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">-->
            <!--                            <iframe-->
            <!--                                src="https://app.kartra.com/external_video/vimeo/398438819?autoplay=true"-->
            <!--                                scrolling="no" allowfullscreen="true" data-video-type="vimeo"-->
            <!--                                data-video="398438819?autoplay=true" width="100%" frameborder="0"></iframe>-->

            <!--                            <div class="kartra_video_player_shadow"></div>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--            <div class="col-md-5 column--vertical-center column--padding-top-bottom-large column--padding-right-extra-large column--padding-left-doubble-extra-large column--sm-padding-extra-medium"-->
            <!--                style="margin-top: 0px; margin-bottom: 0px; padding: 15px 70px 15px 140px;">-->
            <!--                <div style="background-color: rgb(49, 85, 40); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1;"-->
            <!--                    class="background-item background_changer--blur0"></div>-->
            <!--                <div class="js_kartra_component_holder">-->

            <!--                    <div data-component="headline">-->
            <!--                        <div class="kartra_headline kartra_headline--text-center kartra_headline--font-weight-regular kartra_headline--dim-black kartra_headline--font-weight-normal kartra_headline--source-sans-pro-font kartra_headline--size-giant kartra_headline--margin-bottom-special-medium"-->
            <!--                            style="position: relative; margin-top: 0px; margin-bottom: 10px;">-->
            <!--                            <p style="font-size: 1.4rem; line-height: 1.2em;"><b><span-->
            <!--                                        style="color: rgb(255, 255, 255); font-size: 1.4rem; line-height: 1.2em;"><span-->
            <!--                                            style="font-family: roboto; line-height: 1.2em; font-size: 1.4rem; color: rgb(255, 255, 255);">Get-->
            <!--                                            a FREE Account Now!</span></span></b></p>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                    <div data-component="divider">-->
            <!--                        <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-full-light-grey kartra_divider--margin-bottom-tiny pull-center kartra_divider--medium"-->
            <!--                            style="border-color: rgb(243, 113, 33); border-top-style: solid; border-top-width: 2px; margin: 0px auto 5px;">-->
            <!--                    </div>-->
            <!--                    <div data-component="text">-->
            <!--                        <div class="kartra_text kartra_text--text-center kartra_text--light-grey kartra_text--size-big-special-small kartra_text--margin-bottom-special-large"-->
            <!--                            style="position: relative; margin-top: 0px; margin-bottom: 30px;">-->
            <!--                            <p style="text-align: left;"><span><span style="color:#FFFFFF;">It take's less-->
            <!--                                        than 2 minutes to do! Click the button below and you're all-->
            <!--                                        set!</span></span></p>-->

            <!--                            <ul>-->
            <!--                                <li style="text-align: left;"><span><span style="color:#FFFFFF;">Access our-->
            <!--                                            10 favorite health and longevity videos</span></span></li>-->
            <!--                                <li style="text-align: left;"><span><span style="color:#FFFFFF;">Download-->
            <!--                                            "Foods Fight Cancer report. </span></span></li>-->
            <!--                                <li style="text-align: left;"><span><span style="color:#FFFFFF;">VIP-->
            <!--                                            discounts and coupons.</span></span></li>-->
            <!--                            </ul>-->
            <!--                        </div>-->
            <!--                    </div>-->
            <!--                    <div data-component="button">-->
            <!--                        <a href="javascript: void(0);"-->
            <!--                            class="kartra_button1 kartra_button1--green kartra_button1--source-sans-pro-font kartra_button1--box-shadow-inset-bottom kartra_button1--margin-bottom-none kartra_button1--solid kartra_button1--full-width kartra_button1--rounded pull-center toggle_optin"-->
            <!--                            style='font-weight: 700; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto; font-family: "source sans pro";'-->
            <!--                            data-frame-id="_b45zlp5y0" id="1587406455559_formbutton"-->
            <!--                            data-popup-src="https://app.kartra.com/elements/popup_optin_form_double_col_4.html"-->
            <!--                            target="_parent">SUBMIT NOW</a>-->
            <!--                    </div>-->
            <!--                </div>-->
            <!--            </div>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            <div class="content" style="padding-top: 60px; padding-bottom: 40px; background-color: rgb(244, 244, 244);"
                id="_hkvs5qwnk">
                <div class="background_changer background_changer--blur0" style="background-image: none;"></div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid" id="XgrjVe8rve">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline" id="accordion-rLJqY0Jiu3">
                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--text-center kartra_headline--dim-black"
                                        style="position: relative; margin: 0px 0px 30px;">
                                        <p style="font-size: 2.33rem; line-height: 1.2em;"><strong><span
                                                    style="font-size: 2.33rem; line-height: 1.2em;"><span
                                                        style="font-family: oswald; font-size: 2.33rem; line-height: 1.2em;">Best
                                                        Rated <span
                                                            style="font-family: oswald; font-size: 2.33rem; line-height: 1.2em; color: rgb(255, 165, 0);">Healthy
                                                            Lifestyle Programs</span> In The Market By Satisfied
                                                        Clients</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 kartra_divider--padding-bottom-tiny pull-center kartra_divider--full"
                                        style="border-color: rgba(33, 33, 33, 0.2); border-top-style: solid; border-top-width: 1px; margin: 0px;">
                                </div>
                                <div class="row row--equal" data-component="grid" id="accordion-xGwPTvGVyp">
                                    <div class="col-md-6"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px; background-image: none;">
                                        <div class="js_kartra_component_holder">
                                            <div data-component="text" id="accordion-Gnsk6Xl1UA">
                                                <div class="kartra_text kartra_text--sm-text-center"
                                                    style="position: relative;margin: 20px 0px;">
                                                    <p style="font-size: 0.8rem;"><strong><span
                                                                style="font-size: 0.8rem;">BY COOPER WELLNESS &amp;
                                                                DISEASE PREVENTION CENTER</span></strong></p>

                                                    <p style="font-size: 0.8rem;"><span
                                                            style="font-size:0.80rem;">Helping you to</span>
                                                        <strong><span style="font-size: 1rem;"><span
                                                                    style="font-family: zeyada; color: rgb(0, 128, 0); font-size: 1rem;">Get
                                                                    Healthy for Life!</span></span></strong>
                                                    </p>


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px; background-image: none;">
                                        <div class="js_kartra_component_holder">
                                            <div class="social_share_wrapper pull-right" data-component="bundle"
                                                id="AZRw5rNwiC" style="margin: 0px;">
                                                <div data-component="social_share">
                                                    <span
                                                        class="facebook_share pull-center kartra_social_share1 kartra_social_share1--medium kartra_social_share1--bg-white kartra_social_share1--dim-black kartra_social_share1--bordered kartra_social_share1--font-weight-bold kartra_social_share1--icon-top"><span
                                                            class="kartra_icon__icon kartra_icon__icon--facebook-color fa fa-facebook-square"></span>Share</span>
                                                </div>
                                                <div data-component="social_share">
                                                    <span
                                                        class="twitter_share pull-center kartra_social_share1 kartra_social_share1--medium kartra_social_share1--bg-white kartra_social_share1--dim-black kartra_social_share1--bordered kartra_social_share1--font-weight-bold kartra_social_share1--icon-top"><span
                                                            class="kartra_icon__icon kartra_icon__icon--twitter-color fa fa-twitter"></span>Tweet</span>
                                                </div>
                                                <div data-component="social_share">
                                                    <span
                                                        class="pinterest_share pull-center kartra_social_share1 kartra_social_share1--medium kartra_social_share1--bg-white kartra_social_share1--dim-black kartra_social_share1--bordered kartra_social_share1--font-weight-bold kartra_social_share1--icon-top"><span
                                                            class="kartra_icon__icon kartra_icon__icon--pinterest-color fa fa-pinterest"></span>Pin</span>
                                                </div>
                                                <div data-component="social_share">
                                                    <span
                                                        class="linkedin_share pull-center kartra_social_share1 kartra_social_share1--medium kartra_social_share1--bg-white kartra_social_share1--dim-black kartra_social_share1--bordered kartra_social_share1--font-weight-bold kartra_social_share1--icon-top"><span
                                                            class="kartra_icon__icon kartra_icon__icon--linkedin-color fa fa-linkedin-square"></span>Share</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="divider" id="9vTlKWJsDM">
                                    <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 pull-center kartra_divider--full"
                                        style="border-color: rgba(33, 33, 33, 0.2); border-top-style: solid; border-top-width: 1px; margin: 0px auto 20px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small kartra_element_bg--xs-padding-left-right-extra-small-important js_kartra_component_holder"
                                    data-component="bundle" id="nUz9Au8xUT_digVbKXlHZ"
                                    style="margin: 0px; padding: 50px 40px 20px; border-radius: 4px 4px 0px 0px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 0px; border-style: none; border-color: rgb(255, 255, 255); padding: 40px 40px 20px; background-image: none; opacity: 1;"
                                        class="background-item background_changer--blur0"></div>
                                    <div data-component="video"
                                        data-thumbnail="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/generated-kartra-video-thumb-16198217_1610651310102TESTIMONY_COOPER_WELLNESS_CENTER_DENYS.mp4.jpg"
                                        data-screenshot="false">
                                        <div class="kartra_video kartra_video_containerkXD4fThsiEdT js_kartra_trackable_object"
                                            style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;"
                                            data-kt-type="video" data-kt-value="kXD4fThsiEdT" data-kt-owner="DpwDQa6g"
                                            id="kXD4fThsiEdT/rddbb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true"
                                            data-random_str="rddbb">
                                            <script
                                                src="https://app.kartra.com/video/kXD4fThsiEdT/rddbb/?autoplay=false&amp;mute_on_start=false&amp;show_controls=true"></script>
                                        </div>
                                    </div>
                                    <div class="row row--equal row--sm-margin-bottom-extra-small-important"
                                        data-component="grid" id="accordion-kw63qIXFpj"
                                        style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;margin-top: 0px;margin-bottom: 40px;background-image: none;">
                                        <div class="col-md-6 column--md-padding-right-extra-medium-important background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; background-image: none; padding: 0px 40px 0px 15px; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-ZSD77Xq1ND">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center"
                                                        style="position: relative;">
                                                        <p style="font-size: 1rem; line-height: 1.2em;">
                                                            <font face="oswald"><span
                                                                    style="color: rgb(0, 100, 0); font-size: 1rem; line-height: 1.2em;"><b><span
                                                                            style="font-size: 1rem; line-height: 1.2em; color: rgb(0, 100, 0);"><span
                                                                                style="font-size: 1rem; line-height: 1.2em; color: rgb(0, 100, 0);">VIRTUAL
                                                                                8 WEEKS TO
                                                                                WELLNESS</span></span></b></span></font>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-e4a4WpBMMJ">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="text" id="accordion-oxjEoof7sL">
                                                    <div class="kartra_text kartra_text--text-medium kartra_text--light-grey kartra_text--text-left kartra_text--sm-text-center"
                                                        style="margin: 0px 0px 20px; position: relative;">
                                                        <p><span style="color:#000000;">Our very successful in-house 8
                                                                Weeks to Wellness Program is now Virtual! Our Wellness
                                                                Program has been designed as a result of years of
                                                                research into chronic diseases. Lifestyle medicine is
                                                                the emerging, cutting edge approach that is achieving
                                                                phenomenal results. You will discover that not only will
                                                                your physical health improve, but your mental and
                                                                spiritual outlook will also improve.</span></p>

                                                        <p> </p>

                                                        <p><span style="color:#000000;">Join the many others who have
                                                                enjoyed the success of reducing or reversing chronic
                                                                diseases. In this program, you will be taught
                                                                life-changing methods through one-on-one and group
                                                                lifestyle coaching, exercise sessions and diet
                                                                modification strategies combined with hydrotherapy and
                                                                other therapy treatment</span>.<span
                                                                style="color:#000000;"> We create plans tailored to your
                                                                needs.</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px; background-image: none; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="image" href="javascript: void(0);">
                                                    <picture>
                                                        <source type="image/webp"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/18035285_1616089928knaCopy_of_Black_and_Green_Gyms_Back_to_Business_Landscape_Banner_1.webp">
                                                        </source>
                                                        <source type="image/jpeg"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/18035285_1616089928knaCopy_of_Black_and_Green_Gyms_Back_to_Business_Landscape_Banner_1.jpg">
                                                        </source><img
                                                            class="kartra_image kartra_image--max-width-full sm-pull-center pull-right background_changer--blur0"
                                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                            style="border-width: 0px; border-style: none; border-color: rgb(51, 51, 51); margin: 0px 0px 20px; max-width: 100%; height: auto; opacity: 1; width: 500px;"
                                                            id="1522415303504_formbutton" alt=""
                                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/18035285_1616089928knaCopy_of_Black_and_Green_Gyms_Back_to_Business_Landscape_Banner_1.jpg">
                                                    </picture>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text" style="position: relative;">
                                                        <p style="text-align: center;"><em>It's virtual so you can learn
                                                                anytime, anywhere, at your own convenience!</em></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" data-component="grid" id="accordion-zNvEKGnHyK"
                                        style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;margin-top: 0px;background-image: none;">
                                        <div class="col-md-6">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-rrfieZUtq8">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center"
                                                        style="position: relative; margin: 0px 0px 20px;">
                                                        <p style="font-size: 1rem; line-height: 1.2em;"><span
                                                                style="font-size: 1rem; line-height: 1.2em;"><span
                                                                    style="font-family: oswald; font-size: 1rem; line-height: 1.2em;">What
                                                                    You Can Expect</span></span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-qQ1vVynOgn">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="list" id="accordion-6N1JjXG7e4">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Medical Provider Personalized
                                                                            Treatment</strong></p>

                                                                    <p>You'll start the program knowing you're in
                                                                        capable hands! It begins with an initial
                                                                        physician medical consultation, labs, body fat
                                                                        analysis, health risk assessment. Plus
                                                                        weekly follow-ups.</p>

                                                                    <p> </p>

                                                                    <p><strong>Your own personal wellness
                                                                            coach!</strong></p>

                                                                    <p>Personalized goal and plan development,
                                                                        including  monitoring and progress.</p>

                                                                    <p> </p>

                                                                    <p><b>Rich Multimedia Educational
                                                                            Presentations</b>· </p>

                                                                    <p>Discover how to reverse diabetes, improve blood
                                                                        pressure, lose weight, reduce cholesterol and
                                                                        medications, and how to become physically fit.
                                                                    </p>

                                                                    <p> </p>

                                                                    <p><strong>Access to our virtual course including
                                                                            lectures, cooking videos, exercise videos
                                                                            and more!</strong></p>

                                                                    <p> </p>

                                                                    <p> </p>

                                                                    <p> </p>

                                                                    <p> </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74); display: none;"
                                                                    class="kartra_icon__icon fa fa-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">

                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-tm4gaPQeSA">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center kartra_headline--sm-margin-top-extra-small-important"
                                                        style="position: relative; margin: 0px 0px 20px;">
                                                        <p style="font-size: 1rem;line-height: 1.2em;"><span
                                                                style="font-family: oswald; line-height: 1.2em; font-size: 1rem;">Vibrant
                                                                Health is Yours &amp; More!</span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-zK0vZsGGkT">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="list" id="accordion-BjBzDAYUnX">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101); display: none;"
                                                                    class="kartra_icon__icon fa fa-heartbeat"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p> </p>

                                                                    <ul>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </li>

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="accordion-nncilpkFWu"
                                    style="padding: 25px; margin-top: 0px; margin-bottom: 70px;">
                                    <div style="background-color: rgb(49, 85, 40); border-radius: 0px; border-width: 0px; border-style: none; border-color: rgb(51, 51, 51); padding: 25px; background-image: none; opacity: 1;"
                                        class="background-item background-item--rounded-top-small-tiny-important background_changer--blur0">
                                    </div>
                                    <div class="row row--equal" data-component="grid" id="accordion-zkCc21Lgbp">
                                        <div class="col-md-2 column--vertical-center background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;background-image: none;opacity: 1;">
                                            <div
                                                class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                                <div data-component="headline" id="mXeZe">
                                                    <div class="kartra_headline kartra_headline--sm-text-center kartra_headline--h3 kartra_headline--text-center kartra_headline--black kartra_headline--margin-bottom-none"
                                                        style="position: relative;">
                                                        <p style="font-size: 1rem;line-height: 1.2em;"><strong><span
                                                                    style="font-size: 1rem; line-height: 1.2em;"><span
                                                                        style="font-family: oswald; color: rgba(255, 255, 255, 0.7); font-size: 1rem; line-height: 1.2em;">Only
                                                                        $399!</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;">
                                            <div
                                                class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                                <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--align-center social_icons_wrapper--margin-bottom-tiny social_icons_wrapper--negative-margin-left-right-extra-tiny"
                                                    data-component="bundle">
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-2 column--vertical-center background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <button onclick="openPaymentSelection(1)" class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom sm-pull-center kartra_button1--solid kartra_button1--medium kartra_button1--squared pull-right toggle_pagelink"
                                                    style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; font-family: lato;">Order Now
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                   <!-- Payment Selection Modal -->
                    <div id="paymentSelection" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; justify-content: center; align-items: center;">
                        <div class="modal-content"  style="background-color: #fff; width: 90%; max-width: 500px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); position: relative;">
                            <button onclick="closePaymentSelection()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                            
                            <h2 style="text-align: center; font-size: 24px; font-weight: bold; color: #333;">Select Payment Method</h2>
                    
                            <!-- Tab Navigation -->
                            <div style="display: flex; justify-content: space-around; margin-top: 20px;">
                                <button onclick="showTab('mobileMoneyTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: green; color:white; width: 50%;">Pay with Mobile Money</button>
                                <button onclick="showTab('creditTab')" style="padding: 10px; font-weight: bold; cursor: pointer; border: none; background-color: orange; width: 50%;">Pay with Credit/PayPal</button>
                            </div>
                    
                            <!-- Credit Card Tab Content -->
                            <div id="creditTab" class="tab-content" style="display: none; padding: 20px;">
                                <p>Complete your payment with Credit Card or PayPal:</p>
                                <a id="paypalButton" class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom sm-pull-center kartra_button1--solid kartra_button1--medium kartra_button1--squared toggle_pagelink pull-center"
                                    style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; font-family: lato;"
                                    target="_blank">Pay with Credit/PayPal
                                </a>
                            </div>
                            <!-- Mobile Money Tab Content -->
                            <div id="mobileMoneyTab" class="tab-content" style="display: block; padding: 20px;">
                                <p>Complete your payment with Mobile Money:</p>
                                <button id="mobileMoneyButton"
                                    style="width: 100%; padding: 10px; background-color: #008cdd; color: white; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;">Pay with Mobile Money
                                </button>
                            </div>
                        </div>
                    </div>

                   <!-- Modal -->
                    <div id="paymentModal" class="modal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; display: none; justify-content: center; align-items: center;">
                                    <div class="modal-content" style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: center; position: relative;">
                                        <h4 style="font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">Confirm Your Payment for <span id="prog_name"></span></h4>
                                        <p style="color: #555; margin-bottom: 20px;">Price: USD <span id="usd_price" style="font-weight: bold;"></span> = GHS <span id="price" style="font-weight: bold;"></span></p>
                                        <!--<p>Conversion rate: <span id="rate" style="font-weight: bold;"></span></p>-->
                                        <!--<p style="font-size: 15px; color: #555; margin-bottom: 20px;">Amount Payable: GHS <span id="amount" style="font-weight: bold;"></span></p>-->

                                        
                                        <form id="paymentForm" onsubmit="payWithPaystackforPrograms(event)" id="paymentprocessing" style="display: flex; flex-direction: column; align-items: center;">
                                            <!--<input type="hidden" name="p_ID" id="p_ID">-->
                                            <input type="hidden" name="price" id="prog_price">
                                
                                            <label for="email1" style="align-self: flex-start; font-size: 14px; margin-bottom: 5px; color: #333;">Email:</label>
                                            <input type="email" id="email1" name="email1" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                
                                            <label for="phone" style="align-self: flex-start; font-size: 14px; margin-bottom: 5px; color: #333;">Phone:</label>
                                            <input type="text" id="phone" name="phone" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                
                                            <button type="submit" style="padding: 10px 20px; background-color: #f37121; color: #fff; border: none; border-radius: 4px; font-size: 18px; cursor: pointer; font-weight: bold;">Proceed to Payment</button>
                                            <script>
                                                document.querySelector('form').addEventListener('submit', function() {
                                                const paymentprocessing = document.getElementById('paymentprocessing');
                                                paymentprocessing.disabled = true;
                                                paymentprocessing.style.backgroundColor = 'grey';
                                                paymentprocessing.innerHTML = 'Processing payment...';
                                            </script>
                                        </form>
                                        <button onclick="document.getElementById('paymentModal').style.display='none';" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                </div>

                    <div class="row" data-component="grid"></div>
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small kartra_element_bg--xs-padding-left-right-extra-small-important js_kartra_component_holder"
                                    data-component="bundle" id="nUz9Au8xUT_digVbKXlHZ"
                                    style="margin: 0px; padding: 50px 40px 20px; border-radius: 4px 4px 0px 0px;">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 0px; border-style: none; border-color: rgb(255, 255, 255); padding: 40px 40px 20px; background-image: none; opacity: 1;"
                                        class="background-item background_changer--blur0"></div>
                                    <div class="row row--equal row--sm-margin-bottom-extra-small-important"
                                        data-component="grid" id="accordion-kw63qIXFpj"
                                        style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;margin-top: 0px;margin-bottom: 40px;background-image: none;">
                                        <div class="col-md-6 column--md-padding-right-extra-medium-important background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; background-image: none; padding: 0px 40px 0px 15px; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-ZSD77Xq1ND">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center"
                                                        style="position: relative;">
                                                        <p style="font-size: 1rem; line-height: 1.2em;"><strong><span
                                                                    style="color: rgb(0, 100, 0); font-size: 1rem; line-height: 1.2em;"><span
                                                                        style="font-family: oswald; font-size: 1rem; line-height: 1.2em; color: rgb(0, 100, 0);">SPIRITUAL
                                                                        HEALING CLASS</span></span></strong></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-e4a4WpBMMJ">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="text" id="accordion-oxjEoof7sL">
                                                    <div class="kartra_text kartra_text--text-medium kartra_text--light-grey kartra_text--text-left kartra_text--sm-text-center"
                                                        style="margin: 0px 0px 20px; position: relative;">
                                                        <p><span style="color:#000000;">Spiritual health and faith in
                                                                God is a cornerstone of Dr. Cooper-Dockery's Lifestyle
                                                                Medicine. By balancing your spiritual well-being, your
                                                                nutritional intake and your physical activity, you can
                                                                take control of your life and circumstances.</span></p>

                                                        <p> </p>

                                                        <p><span style="color:#000000;">Some people neglect this key
                                                                component to balance health but you shouldn't. We are
                                                                multidimensional and you want to indeed do well
                                                                physically, mentally, emotionally, and spiritually. This
                                                                is living the abundant life. Get that inner peace that
                                                                goes beyond the body.</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px; background-image: none; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="image" href="javascript: void(0);">
                                                    <picture>
                                                        <source type="image/webp"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1588093310.webp">
                                                        </source>
                                                        <source type="image/jpeg"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1588093310.jpg">
                                                        </source><img
                                                            class="kartra_image kartra_image--max-width-full sm-pull-center pull-right background_changer--blur0"
                                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                            style="border-width: 0px;border-style: none;border-color: rgb(51, 51, 51);margin: 0px 0px 20px;max-width: 100%;height: auto;opacity: 1;"
                                                            id="1522415303504_formbutton"
                                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1588093310.jpg">
                                                    </picture>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" data-component="grid" id="accordion-zNvEKGnHyK"
                                        style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;margin-top: 0px;background-image: none;">
                                        <div class="col-md-6">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-rrfieZUtq8">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center"
                                                        style="position: relative; margin: 0px 0px 20px;">
                                                        <p style="font-size: 1rem; line-height: 1.2em;"><span
                                                                style="font-size: 1rem; line-height: 1.2em;"><span
                                                                    style="font-family: oswald; font-size: 1rem; line-height: 1.2em;">What
                                                                    You Can Expect</span></span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-qQ1vVynOgn">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="list" id="accordion-6N1JjXG7e4">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-plus-square-o"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Competent Instruction</strong><br>
                                                                        Our balanced studies have been developed over
                                                                        many years of providing for the mind, body, and
                                                                        spirit. </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-plus-square-o"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Compassion and Care</strong><br>
                                                                        We understand this dimension and you'll be in
                                                                        assigned a chaplain or pastor who values
                                                                        compassionate care.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="accordion-tm4gaPQeSA">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center kartra_headline--sm-margin-top-extra-small-important"
                                                        style="position: relative; margin: 0px 0px 20px;">
                                                        <p style="font-size: 1rem;line-height: 1.2em;"><span
                                                                style="font-family: oswald; line-height: 1.2em; font-size: 1rem;">A
                                                                Snippet of What You'll Achieve</span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="accordion-zK0vZsGGkT">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="list" id="accordion-BjBzDAYUnX">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-heart-o"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><b>Abundant Health</b></p>

                                                                    <p>We wish above all things that you may prosper and
                                                                        be in good health, even as your soul is
                                                                        prospering (too).</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-heart-o"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><b>Peace and Joy</b></p>

                                                                    <p>Some people have wealth and physical health yet
                                                                        no real joy. This programs brings mental and
                                                                        emotional healing.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                    data-component="bundle" id="accordion-nncilpkFWu"
                                    style="padding: 25px; margin-top: 0px; margin-bottom: 70px;">
                                    <div style="background-color: rgb(49, 85, 40); border-radius: 0px; border-width: 0px; border-style: none; border-color: rgb(51, 51, 51); padding: 25px; background-image: none; opacity: 1;"
                                        class="background-item background-item--rounded-top-small-tiny-important background_changer--blur0">
                                    </div>
                                    <div class="row row--equal" data-component="grid" id="accordion-zkCc21Lgbp">
                                        <div class="col-md-2 column--vertical-center background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;background-image: none;opacity: 1;">
                                            <div
                                                class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                                <div data-component="headline" id="mXeZe">
                                                    <div class="kartra_headline kartra_headline--sm-text-center kartra_headline--h3 kartra_headline--text-center kartra_headline--black kartra_headline--margin-bottom-none"
                                                        style="position: relative;">
                                                        <p style="font-size: 1rem;line-height: 1.2em;"><strong><span
                                                                    style="font-size: 1rem; line-height: 1.2em;"><span
                                                                        style="font-family: oswald; color: rgba(255, 255, 255, 0.7); font-size: 1rem; line-height: 1.2em;">FREE!</span></span></strong>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;">
                                            <div
                                                class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                                <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--align-center social_icons_wrapper--margin-bottom-tiny social_icons_wrapper--negative-margin-left-right-extra-tiny"
                                                    data-component="bundle">
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 255, 255);"
                                                                class="kartra_icon__icon fa fa-star-half-full"></span>
                                                        </div>
                                                    </div>



                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-2 column--vertical-center background_changer--blur0"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="button" id="accordion-73TWJsFSrG"><a
                                                        href="javascript: void(0);"
                                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom sm-pull-center kartra_button1--solid kartra_button1--medium kartra_button1--squared pull-right toggle_optin"
                                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; font-family: lato;"
                                                        data-frame-id="_hkvs5qwnk" id="1589152125087_formbutton"
                                                        data-popup-src="https://app.kartra.com/elements/popup_optin_form_double_col_7.html"
                                                        target="_parent">Request Studies Now</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid"></div>
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-extra-small kartra_element_bg--sm-margin-left-right-extra-small-important"
                                    style="margin: 0px 0px 25px; padding: 50px 40px 40px;" data-component="bundle"
                                    id="jkHIyqnX33_v1VOzttdF5">
                                    <div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 1px; border-style: solid; border-color: rgb(238, 238, 238); padding: 40px; background-image: none; opacity: 1;"
                                        class="background-item background-item--border-extra-tiny background-item--border-light-white background_changer--blur0">
                                    </div>
                                    <div class="row row--equal" data-component="grid" id="accordion-kw63qIXFpj"
                                        style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;margin-top: 0px;background-image: none;">
                                        <div class="col-md-6 column--md-padding-right-extra-medium-important"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline" id="ygR52ZBkDr">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center"
                                                        style="position: relative;">
                                                        <p style="font-size: 1rem; line-height: 1.2em;"><strong><span
                                                                    style="color: rgb(0, 100, 0); font-size: 1rem; line-height: 1.2em;"><span
                                                                        style="font-family: oswald; font-size: 1rem; line-height: 1.2em; color: rgb(0, 100, 0);">RESIDENTIAL
                                                                        PROGRAM</span></span></strong></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider">
                                                    <hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 pull-center kartra_divider--full"
                                                        style="border-color: rgba(33, 33, 33, 0.2); border-top-style: solid; border-top-width: 1px; margin: 0px;">
                                                </div>
                                                <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--align-center social_icons_wrapper--margin-bottom-tiny social_icons_wrapper--negative-margin-left-right-extra-tiny pull-left"
                                                    data-component="bundle" style="margin: 0px -5px 10px;"
                                                    id="DVW20wgQUJ_iXiL9I4IHP">
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 140, 0);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 140, 0);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 140, 0);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 140, 0);"
                                                                class="kartra_icon__icon fa fa-star"></span>
                                                        </div>
                                                    </div>
                                                    <div data-component="icon" href="javascript: void(0);">
                                                        <div class="kartra_icon kartra_icon--top-none kartra_icon--hover-opacity-medium kartra_icon--white kartra_icon--margin-left-right-extra-tiny kartra_icon--medium"
                                                            style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(59, 89, 152);"
                                                            id="1539757460327_formbutton">
                                                            <span style="color: rgb(255, 140, 0);"
                                                                class="kartra_icon__icon fa fa-star-half-full"></span>
                                                        </div>
                                                    </div>



                                                </div>
                                                <div data-component="text" id="accordion-oxjEoof7sL">
                                                    <div class="kartra_text kartra_text--text-medium kartra_text--light-grey kartra_text--text-left kartra_text--sm-text-center"
                                                        style="margin: 0px 0px 20px; position: relative;">
                                                        <p>This onsite in-person 14 Days Program may be just the thing
                                                            you need to help reverse your disease. Get away from the
                                                            hustle and bustle to retreat and have that extra self-care
                                                            your mind and body desperately craves.</p>

                                                        <p> </p>

                                                        <p>An initial health and risk assessment guarantees that each
                                                            patient receives a personalized treatment plan that is
                                                            specific to their own unique and individual health needs.
                                                            This is personal touch at the next level</p>

                                                        <p> </p>

                                                        <p>Come experience the difference with our hydrotherapy
                                                            treatments and contrasting baths, nutrition fuels your body
                                                            with only the best essential vitamins and minerals. Start
                                                            your journey now. In 14 days you'll be glad you did.</p>
                                                    </div>
                                                </div>


                                                <div data-component="button" id="Pe2hJZXSv2">
                                                    <button onclick="openPaymentSelection(2)"
                                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--full-width kartra_button1--squared pull-center toggle_pagelink "
                                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 10px auto 20px; font-weight: 700; font-family: Lato;">Book Your Spot Now
                                                    </button>
                                                </div>
                                                <div data-component="list" id="JU2mZh3M4V">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-heartbeat"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Medical Provider Personalized
                                                                            Treatment</strong></p>

                                                                    <p>Sit down with an actual Medical Provider who
                                                                        understands your conditions and knows when to
                                                                        prescribe natural or conventional medicine.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-heartbeat"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><span
                                                                            style="font-size: inherit; background-color: rgba(0, 0, 0, 0);"><b>Prescribed
                                                                                Natural Remedies</b></span></p>

                                                                    <p><span
                                                                            style="font-size: inherit; font-weight: inherit; background-color: rgba(0, 0, 0, 0);">Herbal
                                                                            teas, supplements, exercise therapy,
                                                                            charcoal, counseling, poultices, and healthy
                                                                            diet plans.</span></p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-heartbeat"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Prescribed Hot and Cold Therapy</strong>
                                                                    </p>

                                                                    <p>Boost your immune system by increasing
                                                                        circulation and white blood cells. </p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px; border-color: rgb(103, 165, 74);">
                                                                <span style="color: rgb(103, 165, 74);"
                                                                    class="kartra_icon__icon fa fa-heartbeat"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><strong>Accommodations Included</strong></p>

                                                                    <p>Let you body heal as you sleep in
                                                                        a hotel-quality bed.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="column--vertical-center background_changer--blur0 col-md-6"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px; background-image: none; opacity: 1;">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="image" href="javascript: void(0);">
                                                    <picture>
                                                        <source type="image/webp"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9489409_1588095698774hypertension-check.webp">
                                                        </source>
                                                        <source type="image/jpeg"
                                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9489409_1588095698774hypertension-check.jpg">
                                                        </source><img
                                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                            style="border-width: 0px; border-style: none; border-color: rgb(51, 51, 51); margin: 0px auto 20px; width: 608px; max-width: 100%; height: auto; opacity: 1;"
                                                            id="1522416420180_formbutton"
                                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9489409_1588095698774hypertension-check.jpg">
                                                    </picture>
                                                </div>
                                                <div data-component="text" id="nWXCE0QMP8">
                                                    <div class="kartra_text"
                                                        style="position: relative; margin-top: 0px; margin-bottom: 40px;">
                                                        <p style="text-align: center;"><em>Your greatest wealth is your
                                                                health. Get healthy for life now!</em></p>
                                                    </div>
                                                </div>
                                                <div data-component="headline" id="oAobw3z7J3">
                                                    <div class="kartra_headline kartra_headline--h1 kartra_headline--font-weight-regular kartra_headline--black kartra_headline--text-left kartra_headline--sm-text-center kartra_headline--sm-margin-top-extra-small-important"
                                                        style="position: relative; margin: 0px 0px 20px;">
                                                        <p style="font-size: 1rem;line-height: 1.2em;"><span
                                                                style="font-family: oswald; line-height: 1.2em; font-size: 1rem;">A
                                                                Snippet of What You'll Achieve</span></p>
                                                    </div>
                                                </div>
                                                <div data-component="divider" id="vlDoTUTXsL">
                                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgba(0, 0, 0, 0.2); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgba(0, 0, 0, 0.2); border-bottom-color: rgba(0, 0, 0, 0.2); border-left-color: rgba(0, 0, 0, 0.2);">
                                                </div>
                                                <div data-component="list" id="FyWR2y67Pe">
                                                    <ul class="kartra_list">
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-user-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><b>Lean Healthy Cooking</b></p>

                                                                    <p>Replace meat, dairy, and sugar in your diet
                                                                        through a variety of delicious and easy recipes.
                                                                        Make better eating choices armed with
                                                                        nutritional knowledge.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-user-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><b>Learn Lifestyle Medicine</b></p>

                                                                    <p>Understand the cause of your diseases and learn
                                                                        natural remedies and habits to reverse their
                                                                        progression.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-user-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><span
                                                                            style="font-size: inherit; background-color: rgba(0, 0, 0, 0);"><b>Discover
                                                                                Simple Exercises</b></span></p>

                                                                    <p>Spend time in nature with simple exercises like
                                                                        walking.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                        <li class="kartra_list__item kartra_list__item--table"
                                                            href="javascript: void(0);">
                                                            <div class="kartra_icon kartra_icon--light-grey kartra_icon--top-none kartra_icon-negative-margin-top-special-extra-tiny-important kartra_icon--medium"
                                                                style="background-color: rgba(0, 0, 0, 0); margin: -6px auto 0px;">
                                                                <span style="color: rgb(255, 101, 101);"
                                                                    class="kartra_icon__icon fa fa-user-plus"></span>
                                                            </div>
                                                            <div
                                                                class="kartra_item_info kartra_item_info--padding-left-small">
                                                                <div class="kartra_item_info__text kartra_item_info__text--light-grey kartra_item_info__text--margin-bottom-extra-small"
                                                                    style="position: relative;">
                                                                    <p><span
                                                                            style="font-size: inherit; background-color: rgba(0, 0, 0, 0);"><b>Nutrition
                                                                                for Physical Health</b></span></p>

                                                                    <p>Enjoy meals that are delicious, low-fat,
                                                                        high-fiber, and packed with whole grains,
                                                                        legumes, and nuts.</p>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div data-component="button" id="wudvN1xT6H">
                                                    <button onclick="openPaymentSelection(2)"
                                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--full-width kartra_button1--squared pull-center toggle_pagelink "
                                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 10px auto 20px; font-weight: 700; font-family: Lato;">Book Your Spot Now
                                                    </button></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade popup_modal popup-modal popup-form-modal js_trackable_wrapper"
                data-button="1587406455559_formbutton" role="dialog" aria-hidden="true">
                <button type="button" class="closer close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="content content--popup-form-large content--padding-top-extra-medium"
                                style="background-color: rgb(49, 85, 40); padding: 0px;" id="_igjqrnxbt">
                                <div class="background_changer background_changer--blur0"
                                    style="background-image: none; opacity: 1;"></div>
                                <div class="background_changer_overlay" style="background-image: none;"></div>
                                <div class="container-fluid page-popup-form-container--large">
                                    <div class="row row--equal" data-component="grid">
                                        <div
                                            class="col-md-6 column--padding-left-right-extra-medium column--vertical-center">
                                            <div class="js_kartra_component_holder">

                                                <div data-component="headline">
                                                    <div class="kartra_headline kartra_headline--roboto-font kartra_headline--white kartra_headline--size-extra-large kartra_headline--text-center"
                                                        style="position: relative;">
                                                        <p><strong><span style="color: rgb(255, 255, 0);">Get Your FREE
                                                                    Account Today!</span></strong></p>
                                                    </div>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--extra-small kartra_text--white kartra_text--text-center"
                                                        style="position: relative;">
                                                        <p>Smart move! Just enter your details below now and you'll be
                                                            all set.<em> It's as simple as that.</em></p>
                                                    </div>
                                                </div>
                                                <div data-component="optin">
                                                    <div
                                                        class="optin_block_form_54 leads_capture kartra_optin_input_btn--shadow-01">
                                                        <div
                                                            class="leads-form kartra_page_optin_form popup-form-optin-double-col-style-4">
                                                            <div class="kartra_optin_wrapper form_class_hnFSiVQOXcoQ">
                                                                <form
                                                                    action="https://app.kartra.com//process/add_lead/hnFSiVQOXcoQ"
                                                                    data-input-class="kartra_optin_input_giant"
                                                                    data-submit-bg="rgb(243, 113, 33)"
                                                                    data-submit-color="rgb(255, 255, 255)"
                                                                    data-submit-type="Solid"
                                                                    data-submit-size="kartra_btn_giant"
                                                                    data-submit-corners="Rounded" data-submit-bold="700"
                                                                    data-field-style="rounded"
                                                                    style="margin-bottom: 30px; margin-top: 0px;"
                                                                    class="filled_optin unique_class__1b39orfwq form_class_hnFSiVQOXcoQ js_kartra_trackable_object"
                                                                    data-optin-id="hnFSiVQOXcoQ"
                                                                    data-domain="https://app.kartra.com/"
                                                                    data-field-bg="" data-field-color=""
                                                                    data-text-color="" data-display-icons="true"
                                                                    data-submit-text="Start FREE Account Now"
                                                                    data-submit-shadow="btn_shadow_none" target="_top"
                                                                    method="POST" data-kt-type="optin"
                                                                    data-kt-value="hnFSiVQOXcoQ"
                                                                    data-kt-owner="DpwDQa6g"
                                                                    data-effect="kartra_css_effect_6" data-asset-id="2">
                                                                    <div class="kartra_optin_r">
                                                                        <style>
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__1b39orfwq .kartra_optin_tnc-form button.btn.dropdown-toggle,
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__1b39orfwq .kartra_optin_cg button.btn.dropdown-toggle {
                                                                                background-color: ;
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__1b39orfwq .kartra_optin_i {
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__1b39orfwq .kartra_optin_clabel {
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__1b39orfwq ::-webkit-input-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__1b39orfwq ::-moz-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__1b39orfwq :-ms-input-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__1b39orfwq :-moz-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__1b39orfwq input[type=radio]+small,
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__1b39orfwq input[type=checkbox]+small {
                                                                                background-color: ;
                                                                            }
                                                                        </style>
                                                                        <div class="kartra_optin_c1">
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_input_rounded kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-person-1"></i>
                                                                                    <div class="kartra_optin_asterisk">
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        placeholder="First name"
                                                                                        class="required_hnFSiVQOXcoQ js_kartra_santitation kartra_optin_ti"
                                                                                        name="first_name"
                                                                                        data-santitation-type="name">
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_input_rounded kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-email"></i>
                                                                                    <div class="kartra_optin_asterisk">
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        placeholder="Email"
                                                                                        class="required_hnFSiVQOXcoQ js_kartra_santitation kartra_optin_ti"
                                                                                        name="email"
                                                                                        data-santitation-type="email">
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div class="js_gdpr_wrapper clearfix kartra_optin_gdpr_wrapper"
                                                                                    style="display: none;">
                                                                                    <div
                                                                                        class="gdpr_communications js_gdpr_communications kartra_optin_cg kartra_optin_gdpr_terms">
                                                                                        <div
                                                                                            class="kartra-optin-checkbox">
                                                                                            <label
                                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                                <input
                                                                                                    name="gdpr_communications"
                                                                                                    type="checkbox"
                                                                                                    class="js_gdpr_communications_check"
                                                                                                    value="1">

                                                                                                <small></small>


                                                                                                <span
                                                                                                    class="js_gdpr_label_communications">I
                                                                                                    would like to
                                                                                                    receive future
                                                                                                    communications</span>
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="gdpr_terms js_gdpr_terms  kartra_optin_cg kartra_optin_gdpr_terms">
                                                                                        <div
                                                                                            class="kartra-optin-checkbox">
                                                                                            <label
                                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                                <input name="gdpr_terms"
                                                                                                    type="checkbox"
                                                                                                    class="js_gdpr_terms_check"
                                                                                                    value="1">

                                                                                                <small></small>


                                                                                                <span
                                                                                                    class="js_gdpr_label_terms">I
                                                                                                    agree to the GDPR
                                                                                                    Terms &amp;
                                                                                                    Conditions</span><!--
                --><button type="button" class="kartra_gdpr_popover_button js_gdpr_button_popover_trigger">
                                                                                                    <i class="kartra-optin-lineico-infomation-circle js_kartra_popover_trigger js_kartra_popover_gdpr_trigger"
                                                                                                        data-popover="js_kartra_gdpr_popover"></i>
                                                                                                </button>
                                                                                            </label>
                                                                                        </div>
                                                                                        <div class="js_kartra_gdpr_popover js_kartra_popover kartra_optin_gdpr_terms_popover"
                                                                                            style="display: none;">
                                                                                            <div
                                                                                                class="kartra_optin_popover-title">
                                                                                                <div
                                                                                                    class="kartra_optin_well-inner kartra_optin_well-inner_npadding">
                                                                                                    <span
                                                                                                        class="js_gdpr_terms_text">I
                                                                                                        confirm that I
                                                                                                        am at least 16
                                                                                                        years of age or
                                                                                                        older<br>
                                                                                                        <br>
                                                                                                        I have read and
                                                                                                        accept any EULA,
                                                                                                        Terms and
                                                                                                        Conditions,
                                                                                                        Acceptable Use
                                                                                                        Policy, and/or
                                                                                                        Data Processing
                                                                                                        Addendum which
                                                                                                        has been
                                                                                                        provided to me
                                                                                                        in connection
                                                                                                        with the
                                                                                                        software,
                                                                                                        products and/or
                                                                                                        services. <br>
                                                                                                        <br>
                                                                                                        I have been
                                                                                                        fully informed
                                                                                                        and consent to
                                                                                                        the collection
                                                                                                        and use of my
                                                                                                        personal data
                                                                                                        for any purpose
                                                                                                        in connection
                                                                                                        with the
                                                                                                        software,
                                                                                                        products and/or
                                                                                                        services. <br>
                                                                                                        <br>
                                                                                                        I understand
                                                                                                        that certain
                                                                                                        data, including
                                                                                                        personal data,
                                                                                                        must be
                                                                                                        collected or
                                                                                                        processed in
                                                                                                        order for you to
                                                                                                        provide any
                                                                                                        products or
                                                                                                        services I have
                                                                                                        requested or
                                                                                                        contracted for.
                                                                                                        I understand
                                                                                                        that in some
                                                                                                        cases it may be
                                                                                                        required to use
                                                                                                        cookies or
                                                                                                        similar tracking
                                                                                                        to provide those
                                                                                                        products or
                                                                                                        services.. <br>
                                                                                                        <br>
                                                                                                        I understand
                                                                                                        that I have the
                                                                                                        right to request
                                                                                                        access annually
                                                                                                        to any personal
                                                                                                        data you have
                                                                                                        obtained or
                                                                                                        collected
                                                                                                        regarding me.
                                                                                                        You have agreed
                                                                                                        to provide me
                                                                                                        with a record of
                                                                                                        my personal data
                                                                                                        in a readable
                                                                                                        format. <br>
                                                                                                        <br>
                                                                                                        I also
                                                                                                        understand that
                                                                                                        I can revoke my
                                                                                                        consent and that
                                                                                                        I have the right
                                                                                                        to be forgotten.
                                                                                                        If I revoke my
                                                                                                        consent you will
                                                                                                        stop collecting
                                                                                                        or processing my
                                                                                                        personal data. I
                                                                                                        understand that
                                                                                                        if I revoke my
                                                                                                        consent, you may
                                                                                                        be unable to
                                                                                                        provide
                                                                                                        contracted
                                                                                                        products or
                                                                                                        services to me,
                                                                                                        and I can not
                                                                                                        hold you
                                                                                                        responsible for
                                                                                                        that. <br>
                                                                                                        <br>
                                                                                                        Likewise, if I
                                                                                                        properly request
                                                                                                        to be forgotten,
                                                                                                        you will delete
                                                                                                        the data you
                                                                                                        have for me, or
                                                                                                        make it
                                                                                                        inaccessible. I
                                                                                                        also understand
                                                                                                        that if there is
                                                                                                        a dispute
                                                                                                        regarding my
                                                                                                        personal data, I
                                                                                                        can contact
                                                                                                        someone who is
                                                                                                        responsible for
                                                                                                        handling
                                                                                                        data-related
                                                                                                        concerns. If we
                                                                                                        are unable to
                                                                                                        resolve any
                                                                                                        issue, you will
                                                                                                        provide an
                                                                                                        independent
                                                                                                        service to
                                                                                                        arbitrate a
                                                                                                        resolution. If I
                                                                                                        have any
                                                                                                        questions
                                                                                                        regarding my
                                                                                                        rights or
                                                                                                        privacy, I can
                                                                                                        contact the
                                                                                                        email address
                                                                                                        provided.</span>
                                                                                                </div>
                                                                                                <a href="javascript:void(0)"
                                                                                                    class="js_gdpr_popover_close kartra_optin_popover-close js_utility_popover_close"
                                                                                                    target="_parent">×</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <button type="submit"
                                                                                    class="kartra_optin_submit_btn kartra_optin_btn_block kartra_optin_btn_giant submit_button_hnFSiVQOXcoQ btn-rounded btn_shadow_none"
                                                                                    style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); font-weight: 700;">Start
                                                                                    FREE Account Now</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 column--vertical-bottom">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="image">
                                                    <img class="kartra_image kartra_image--full kartra_image--margin-bottom-none kartra_image--max-width-full pull-center"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-popup-optin-form/kp_popup_optin_form_double_4.png">
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
            <div class="modal fade popup_modal popup-modal popup-form-modal js_trackable_wrapper"
                data-button="1589152125087_formbutton" role="dialog" aria-hidden="true">
                <button type="button" class="closer close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="content content--popup-form-large"
                                style="background-color: rgb(49, 85, 40); padding: 0px;" id="_sv0kdvlie">
                                <div class="background_changer background_changer--blur0" style="opacity: 0.2;"
                                    data-bg="url(//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-popup-optin-form/kp_popup_optin_form_double_7.jpg)">
                                </div>
                                <div class="background_changer_overlay" style="background-image: none;"></div>
                                <div class="container-fluid page-popup-form-container--large">
                                    <div class="row row--equal" data-component="grid">
                                        <div class="col-md-9 column--padding-extra-medium">
                                            <div class="js_kartra_component_holder">
                                                <div data-component="headline">
                                                    <div class="kartra_headline kartra_headline--white kartra_headline--lato-font kartra_headline--h1 kartra_headline--text-center"
                                                        style="position: relative;">
                                                        <p>Get Started Now With</p>

                                                        <p><strong>Spiritual Healing Class</strong></p>
                                                    </div>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--lato-font kartra_text--text-medium kartra_text--font-weight-regular kartra_text--white kartra_text--text-center kartra_text--margin-bottom-extra-medium"
                                                        style="position: relative;">
                                                        <p>Enter your details below.</p>
                                                    </div>
                                                </div>
                                                <div data-component="optin">
                                                    <div class="optin_block_form_61 leads_capture">
                                                        <div
                                                            class="leads-form kartra_page_optin_form popup-form-optin-double-col-style-7">
                                                            <div class="kartra_optin_wrapper form_class_T6Mx3ClYFi9c">
                                                                <form
                                                                    action="https://app.kartra.com//process/add_lead/T6Mx3ClYFi9c"
                                                                    data-input-class="kartra_optin_input_giant"
                                                                    data-submit-bg="rgb(243, 113, 33)"
                                                                    data-submit-color="rgb(251, 251, 251)"
                                                                    data-submit-type="Solid"
                                                                    data-submit-size="kartra_btn_giant"
                                                                    data-submit-corners="Squared" data-submit-bold="700"
                                                                    data-field-style="box"
                                                                    style="margin-bottom: 20px; margin-top: 0px;"
                                                                    class="filled_optin js_kartra_trackable_object unique_class__7ix1shyhp form_class_T6Mx3ClYFi9c"
                                                                    data-optin-id="T6Mx3ClYFi9c"
                                                                    data-domain="https://app.kartra.com/"
                                                                    data-field-bg="" data-field-color=""
                                                                    data-text-color="" data-display-icons="true"
                                                                    data-submit-text="I'm Ready to Get Started Now"
                                                                    data-submit-shadow="btn_shadow_none"
                                                                    data-kt-type="optin" data-kt-value="T6Mx3ClYFi9c"
                                                                    data-kt-owner="DpwDQa6g" target="_top" method="POST"
                                                                    data-asset-id="3">
                                                                    <div class="kartra_optin_r">
                                                                        <style>
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__7ix1shyhp .kartra_optin_tnc-form button.btn.dropdown-toggle,
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__7ix1shyhp .kartra_optin_cg button.btn.dropdown-toggle {
                                                                                background-color: ;
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__7ix1shyhp .kartra_optin_i {
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__7ix1shyhp .kartra_optin_clabel {
                                                                                color: !important;
                                                                            }

                                                                            .unique_class__7ix1shyhp ::-webkit-input-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__7ix1shyhp ::-moz-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__7ix1shyhp :-ms-input-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            .unique_class__7ix1shyhp :-moz-placeholder {
                                                                                color: !important;
                                                                                opacity: 0.7;
                                                                            }

                                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__7ix1shyhp input[type=radio]+small,
                                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__7ix1shyhp input[type=checkbox]+small {
                                                                                background-color: ;
                                                                            }
                                                                        </style>
                                                                        <div class="kartra_optin_c1">
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-person-1"></i>
                                                                                    <div class="kartra_optin_asterisk">
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        placeholder="First name"
                                                                                        class="required_T6Mx3ClYFi9c js_kartra_santitation kartra_optin_ti"
                                                                                        name="first_name"
                                                                                        data-santitation-type="name">
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-email"></i>
                                                                                    <div class="kartra_optin_asterisk">
                                                                                    </div>
                                                                                    <input type="text"
                                                                                        placeholder="Email"
                                                                                        class="required_T6Mx3ClYFi9c js_kartra_santitation kartra_optin_ti"
                                                                                        name="email"
                                                                                        data-santitation-type="email">
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-mobile-phone-portrait"></i><input
                                                                                        type="text"
                                                                                        placeholder="Phone without country code"
                                                                                        name="phone"
                                                                                        class="js_kartra_santitation kartra_optin_ti"
                                                                                        data-santitation-type="numeric">
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div
                                                                                    class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
                                                                                    <i
                                                                                        class="kartra_optin_i kartra-optin-lineico-pencil-write-1"></i><textarea
                                                                                        placeholder="Tells us how we can help you..."
                                                                                        name="custom_3"
                                                                                        class="kartra_optin_ti"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="kartra_optin_cg">
                                                                                <div class="js_gdpr_wrapper clearfix kartra_optin_gdpr_wrapper"
                                                                                    style="display: none;">
                                                                                    <div
                                                                                        class="gdpr_communications js_gdpr_communications kartra_optin_cg kartra_optin_gdpr_terms">
                                                                                        <div
                                                                                            class="kartra-optin-checkbox">
                                                                                            <label
                                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                                <input
                                                                                                    name="gdpr_communications"
                                                                                                    type="checkbox"
                                                                                                    class="js_gdpr_communications_check"
                                                                                                    value="1">

                                                                                                <small></small>


                                                                                                <span
                                                                                                    class="js_gdpr_label_communications">I
                                                                                                    would like to
                                                                                                    receive future
                                                                                                    communications</span>
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div
                                                                                        class="gdpr_terms js_gdpr_terms  kartra_optin_cg kartra_optin_gdpr_terms">
                                                                                        <div
                                                                                            class="kartra-optin-checkbox">
                                                                                            <label
                                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                                <input name="gdpr_terms"
                                                                                                    type="checkbox"
                                                                                                    class="js_gdpr_terms_check"
                                                                                                    value="1">

                                                                                                <small></small>


                                                                                                <span
                                                                                                    class="js_gdpr_label_terms">I
                                                                                                    agree to the GDPR
                                                                                                    Terms &amp;
                                                                                                    Conditions</span><!--
                --><button type="button" class="kartra_gdpr_popover_button js_gdpr_button_popover_trigger">
                                                                                                    <i class="kartra-optin-lineico-infomation-circle js_kartra_popover_trigger js_kartra_popover_gdpr_trigger"
                                                                                                        data-popover="js_kartra_gdpr_popover"></i>
                                                                                                </button>
                                                                                            </label>
                                                                                        </div>
                                                                                        <div class="js_kartra_gdpr_popover js_kartra_popover kartra_optin_gdpr_terms_popover"
                                                                                            style="display: none;">
                                                                                            <div
                                                                                                class="kartra_optin_popover-title">
                                                                                                <div
                                                                                                    class="kartra_optin_well-inner kartra_optin_well-inner_npadding">
                                                                                                    <span
                                                                                                        class="js_gdpr_terms_text">I
                                                                                                        confirm that I
                                                                                                        am at least 16
                                                                                                        years of age or
                                                                                                        older<br>
                                                                                                        <br>
                                                                                                        I have read and
                                                                                                        accept any EULA,
                                                                                                        Terms and
                                                                                                        Conditions,
                                                                                                        Acceptable Use
                                                                                                        Policy, and/or
                                                                                                        Data Processing
                                                                                                        Addendum which
                                                                                                        has been
                                                                                                        provided to me
                                                                                                        in connection
                                                                                                        with the
                                                                                                        software,
                                                                                                        products and/or
                                                                                                        services. <br>
                                                                                                        <br>
                                                                                                        I have been
                                                                                                        fully informed
                                                                                                        and consent to
                                                                                                        the collection
                                                                                                        and use of my
                                                                                                        personal data
                                                                                                        for any purpose
                                                                                                        in connection
                                                                                                        with the
                                                                                                        software,
                                                                                                        products and/or
                                                                                                        services. <br>
                                                                                                        <br>
                                                                                                        I understand
                                                                                                        that certain
                                                                                                        data, including
                                                                                                        personal data,
                                                                                                        must be
                                                                                                        collected or
                                                                                                        processed in
                                                                                                        order for you to
                                                                                                        provide any
                                                                                                        products or
                                                                                                        services I have
                                                                                                        requested or
                                                                                                        contracted for.
                                                                                                        I understand
                                                                                                        that in some
                                                                                                        cases it may be
                                                                                                        required to use
                                                                                                        cookies or
                                                                                                        similar tracking
                                                                                                        to provide those
                                                                                                        products or
                                                                                                        services.. <br>
                                                                                                        <br>
                                                                                                        I understand
                                                                                                        that I have the
                                                                                                        right to request
                                                                                                        access annually
                                                                                                        to any personal
                                                                                                        data you have
                                                                                                        obtained or
                                                                                                        collected
                                                                                                        regarding me.
                                                                                                        You have agreed
                                                                                                        to provide me
                                                                                                        with a record of
                                                                                                        my personal data
                                                                                                        in a readable
                                                                                                        format. <br>
                                                                                                        <br>
                                                                                                        I also
                                                                                                        understand that
                                                                                                        I can revoke my
                                                                                                        consent and that
                                                                                                        I have the right
                                                                                                        to be forgotten.
                                                                                                        If I revoke my
                                                                                                        consent you will
                                                                                                        stop collecting
                                                                                                        or processing my
                                                                                                        personal data. I
                                                                                                        understand that
                                                                                                        if I revoke my
                                                                                                        consent, you may
                                                                                                        be unable to
                                                                                                        provide
                                                                                                        contracted
                                                                                                        products or
                                                                                                        services to me,
                                                                                                        and I can not
                                                                                                        hold you
                                                                                                        responsible for
                                                                                                        that. <br>
                                                                                                        <br>
                                                                                                        Likewise, if I
                                                                                                        properly request
                                                                                                        to be forgotten,
                                                                                                        you will delete
                                                                                                        the data you
                                                                                                        have for me, or
                                                                                                        make it
                                                                                                        inaccessible. I
                                                                                                        also understand
                                                                                                        that if there is
                                                                                                        a dispute
                                                                                                        regarding my
                                                                                                        personal data, I
                                                                                                        can contact
                                                                                                        someone who is
                                                                                                        responsible for
                                                                                                        handling
                                                                                                        data-related
                                                                                                        concerns. If we
                                                                                                        are unable to
                                                                                                        resolve any
                                                                                                        issue, you will
                                                                                                        provide an
                                                                                                        independent
                                                                                                        service to
                                                                                                        arbitrate a
                                                                                                        resolution. If I
                                                                                                        have any
                                                                                                        questions
                                                                                                        regarding my
                                                                                                        rights or
                                                                                                        privacy, I can
                                                                                                        contact the
                                                                                                        email address
                                                                                                        provided.</span>
                                                                                                </div>
                                                                                                <a href="javascript:void(0)"
                                                                                                    class="js_gdpr_popover_close kartra_optin_popover-close js_utility_popover_close"
                                                                                                    target="_parent">×</a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <button type="submit"
                                                                                    class="kartra_optin_submit_btn kartra_optin_btn_block kartra_optin_btn_giant submit_button_T6Mx3ClYFi9c btn_shadow_none"
                                                                                    style="background-color: rgb(243, 113, 33); color: rgb(251, 251, 251); font-weight: 700;">I'm
                                                                                    Ready to Get Started Now</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 column--padding-small column--vertical-center">
                                            <div style="background-color: #ffffff;" class="background-item"></div>
                                            <div class="js_kartra_component_holder">
                                                <div class="kartra_element_bg kartra_element_bg--thumb-size-small kartra_element_bg--align-center kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder"
                                                    data-component="bundle"
                                                    style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                                    <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                                        class="background-item background_changer--blur0 js-bg-next-gen"
                                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1588093310.jpg")'>
                                                    </div>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--font-weight-regular kartra_text--dim-grey kartra_text--text-center"
                                                        style="position: relative;">
                                                        <p><em>“I was broken and bruised and almost lost hope. I think
                                                                God for this program because not only is CWC taking care
                                                                of my physical and emotional health, they''re also
                                                                concerned about my spiritual well-being. Now I feel like
                                                                I have wall-to-wall vitality and truly experiencing the
                                                                promise of 3 John 2: 'I wish above all things you may
                                                                prosper and be in good health, even as your soul is
                                                                prospering!'”</em></p>
                                                    </div>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--text-medium kartra_text--font-weight-regular kartra_text--light-black kartra_text--text-center kartra_text--margin-bottom-extra-tiny"
                                                        style="position: relative;">
                                                        <p><strong>Jennifer</strong></p>
                                                    </div>
                                                </div>
                                                <div data-component="text">
                                                    <div class="kartra_text kartra_text--font-weight-regular kartra_text--blue kartra_text--text-center"
                                                        style="position: relative;">
                                                        <p><span style="color:#008000;">Student</span></p>
                                                    </div>
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
    <div class="content content--padding-medium content--padding-bottom-none content--padding-top-none"
        style="background-color: rgb(49, 85, 40); padding: 0px;" id="_6723f824ea44e">
        <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
        <div class="background_changer_overlay" style="background-image: none;"></div>
        <div>
            <div>
                <div class="row row--margin-left-right-none" data-component="grid">
                    <div class="col-md-12 column--padding-none">
                        <div class="js_kartra_component_holder">
                            <div data-component="divider" id="AmalMPkQaq">
                                <hr class="kartra_divider kartra_divider--border-small kartra_divider--border-dark-orange kartra_divider--margin-bottom-small pull-center kartra_divider--full"
                                    style="border-color: rgb(243, 113, 33); border-top-style: solid; border-top-width: 50px; margin: 0px auto 25px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="container">
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--white kartra_headline--open-sans-font kartra_headline--h6 kartra_headline--font-weight-bold kartra_headline--sm-text-center kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>INSIDE WCA</p>
                                    </div>
                                </div>
                                <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--flex-direction-column kartra_link_wrapper--align-left kartra_link_wrapper--sm-align-center kartra_link_wrapper--md-margin-bottom-extra-small"
                                    data-component="bundle">
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="../about"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="107" target="_parent">About Us</a>


                                    <!--<a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"-->
                                    <!--    href="https://cooperwellness.kartra.com/affiliates/154973"-->
                                    <!--    data-frame-id="_6723f824ea44e"-->
                                    <!--    style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "Open Sans";'-->
                                    <!--    target="_blank">Affiliates</a>-->
                                        <a
                                        class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="../othersites"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="192" target="_parent">Our Other Sites</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--white kartra_headline--open-sans-font kartra_headline--h6 kartra_headline--font-weight-bold kartra_headline--sm-text-center kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>SHOP</p>
                                    </div>
                                </div>
                                <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--flex-direction-column kartra_link_wrapper--align-left kartra_link_wrapper--sm-align-center kartra_link_wrapper--md-margin-bottom-extra-small"
                                    data-component="bundle">
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="../books"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="181" target="_parent">Books</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="../courses"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="191" target="_parent">Courses</a>
                                    <!--<a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"-->
                                    <!--    href="https://app.kartra.com/redirect_to/?asset=page&amp;id=X7M6t4dB2vZa"-->
                                    <!--    data-frame-id="_6723f824ea44e"-->
                                    <!--    style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'-->
                                    <!--    data-project-id="3" data-page-id="25" target="_parent">Supplements</a>-->


                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline">
                                    <div class="kartra_headline kartra_headline--white kartra_headline--open-sans-font kartra_headline--h6 kartra_headline--font-weight-bold kartra_headline--sm-text-center kartra_headline--margin-bottom-tiny"
                                        style="position: relative;">
                                        <p>JOIN THE COMMUNITY</p>
                                    </div>
                                </div>
                                <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--flex-direction-column kartra_link_wrapper--align-left kartra_link_wrapper--sm-align-center kartra_link_wrapper--md-margin-bottom-extra-small"
                                    data-component="bundle" id="vY5bCnVEve">
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="../programs/#accordion-kw63qIXFpj"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="261" target="_parent">8 Weeks to
                                        Wellness</a>
                                        <a
                                            class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                            href="../affiliate"
                                            data-frame-id="_6723f824ea44e"
                                            style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                            data-project-id="3" data-page-id="201" target="_parent">Affiliate program</a>



                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="js_kartra_component_holder">
                                <div data-component="headline">
                                    <div
                                        class="kartra_headline kartra_headline--white kartra_headline--open-sans-font kartra_headline--h6 kartra_headline--font-weight-bold kartra_headline--sm-text-center kartra_headline--md-margin-left-big-tiny kartra_headline--margin-bottom-tiny">
                                        <p>FOLLOW US</p>
                                    </div>
                                </div>
                                <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--sm-align-center social_icons_wrapper--margin-bottom-extra-small social_icons_wrapper--negative-margin-left-right-extra-tiny"
                                    data-component="bundle" id="gh5dIWFe6l_L1BbrTbCCI">
                                    <div data-component="icon">
                                        <a href="https://www.facebook.com/cooperwellness/" target="_blank"
                                            class="toggle_pagelink " data-frame-id="_6723f824ea44e">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span class="kartra_icon__icon fa fa-facebook"
                                                    style="color: rgb(158, 158, 158);"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://www.instagram.com/cooperwellnesscenter/" target="_blank"
                                            class="toggle_pagelink " data-frame-id="_6723f824ea44e">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(158, 158, 158);"
                                                    class="kartra_icon__icon fa fa-instagram"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://www.youtube.com/channel/UCihzseMaylCivEhN5lN9Peg/?sub_confirmation=1"
                                            class="toggle_pagelink" data-frame-id="_6723f824ea44e" target="_blank">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(158, 158, 158);"
                                                    class="kartra_icon__icon fa fa-youtube-square"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://twitter.com/DrCooperDockery" target="_blank"
                                            class="toggle_pagelink " data-frame-id="_6723f824ea44e">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
                                                <span style="color: rgb(158, 158, 158);"
                                                    class="kartra_icon__icon fa fa-twitter"></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--flex-direction-column kartra_link_wrapper--align-left kartra_link_wrapper--sm-align-center kartra_link_wrapper--md-margin-bottom-extra-small"
                                    data-component="bundle" id="sZb8qF367H_Hryymdc3bt">



                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant"
                                        href="javascript: void(0);" data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        target="_parent">3604 N. McColl Rd. McAllen, TX</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="kartra_element_bg kartra_element_bg--padding-top-special-medium kartra_element_bg--padding-bottom-tiny"
                data-component="bundle" style="margin-top: 0px; margin-bottom: 0px; padding: 30px 0px 10px;">
                <div style="background-color: rgb(64, 100, 55); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1;"
                    class="background-item background_changer--blur0"></div>
                <div class="container">
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-5 column--vertical-center">
                            <div class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                <div data-component="image" href="javascript: void(0);">
                                    <a href="../"
                                        data-project-id="3" data-page-id="111" class="toggle_pagelink "
                                        data-frame-id="_6723f824ea44e" target="_parent">
                                        <picture>
                                            <source type="image/webp"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.webp">
                                            </source>
                                            <source type="image/png"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                                            </source><img
                                                class="kartra_image kartra_image--max-width-full sm-pull-center kartra_image--margin-bottom-small pull-left background_changer--blur0"
                                                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 25px; opacity: 1; width: 257px; max-width: 100%; height: auto;"
                                                data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
                                        </picture>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7 column--vertical-center">
                            <div class="js_kartra_component_holder js_kartra_component_holder--height-auto">
                                <div class="kartra_link_wrapper kartra_link_wrapper--flex kartra_link_wrapper--align-right kartra_link_wrapper--sm-align-center kartra_link_wrapper--margin-bottom-big-tiny"
                                    data-component="bundle">
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink"
                                        href="../contact"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "open sans";'
                                        data-project-id="3" data-page-id="112" target="_parent">CONTACT US</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink"
                                        href="../terms"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "open sans";'
                                        data-project-id="3" data-page-id="5" target="_parent">DISCLAIMERS</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                        href="../privacypolicy"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "Open Sans";'
                                        data-project-id="3" data-page-id="4" target="_parent">PRIVACY POLICY</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink"
                                        href="../terms"
                                        data-frame-id="_6723f824ea44e" data-project-id="3" data-page-id="5"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "Open Sans";'
                                        target="_parent">TERMS OF USE</a>
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--open-sans-font kartra_text--font-weight-regular kartra_text--semi-pro-white kartra_text--text-right kartra_text--sm-text-center"
                                        style="position: relative;" aria-controls="cke_55" aria-activedescendant=""
                                        aria-autocomplete="list" aria-expanded="false">
                                        <p>Copyright © 2024 by Cooper Wellness Center. All Rights Reserved.</p>
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
        <script src="js/build/front/pages/jquery.lwtCountdown-1.0.js"></script>
        <script async defer src="js/build/front/pages/countdown.js"></script>
        <script async defer src="js/build/front/pages/optin.js"></script>
    </div>
    <!-- /#page -->
    <div style="height:0px;width:0px;opacity:0;position:fixed">
        <script>
                    ! function() {
                        function e() {
                            var e = ((new Date).getTime(), document.createElement("script"));
                            e.type = "text/javascript", e.async = !0, e.setAttribute("embed-id", "e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf"), e.src = "https://embed.adabundle.com/embed-scripts/e2a8e9c8-04f9-42cb-ba60-ba91aa1f5eaf";
                            var t = document.getElementsByTagName("script")[0];
                            t.parentNode.insertBefore(e, t)
                        }
				var t = window;
                    t.attachEvent ? t.attachEvent("onload", e) : t.addEventListener("load", e, !1)
			}();
        </script>
    </div>
    <div style="height:0px;width:0px;opacity:0;position:fixed">
        <!-- Meta Pixel Code -->
        <script>
                    ! function(f, b, e, v, n, t, s) {
				if (f.fbq) return;
                    n = f.fbq = function() {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
			}(window, document, 'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '1445107009514995');
                    fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=1445107009514995&ev=PageView&noscript=1" /></noscript>
        <!-- End Meta Pixel Code -->

        <!-- Facebook Pixel Code -->
        <script>
                    ! function(f, b, e, v, n, t, s) {
				if (f.fbq) return;
                    n = f.fbq = function() {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
			}(window, document, 'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                    fbq('init', '307347596535190');
                    fbq('track', 'PageView');
        </script>
        <noscript><img height="1" width="1" style="display:none"
                src="https://www.facebook.com/tr?id=307347596535190&ev=PageView&noscript=1" /></noscript>
        <!-- End Facebook Pixel Code -->

        <!-- Facebook Pixel Code -->
        <script>
                    ! function(f, b, e, v, n, t, s) {
				if (f.fbq) return;
                    n = f.fbq = function() {
                        n.callMethod ?
                            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                    };
                    if (!f._fbq) f._fbq = n;
                    n.push = n;
                    n.loaded = !0;
                    n.version = '2.0';
                    n.queue = [];
                    t = b.createElement(e);
                    t.async = !0;
                    t.src = v;
                    s = b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t, s)
			}(window, document, 'script',
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
    <script src="//app.kartra.com/resources/js/page_check?page_id=pByILe3giEoQ" async defer></script>
    <script>
                    if (typeof window['jQuery'] !== 'undefined') {
                        window.jsVars = {
                            "page_title": "Virtual Health and Wellness Program McAllen Texas",
                            "page_description": "Adopt our highly competitive wellness program packages, include a weekly class of 60-70 minutes and change your lifestyle. Start from $250 only.",
                            "page_keywords": "Wellness Program Package USA, Spiritual healing classes Tx, Cooper Weight Loss",
                            "page_robots": "index, follow",
                            "secure_base_url": "\/\/app.kartra.com\/",
                            "global_id": "pByILe3giEoQ"
                        };
                    window.global_id = 'pByILe3giEoQ';
                    window.secure_base_url = '//app.kartra.com/';
                    if (typeof Porthole !== 'undefined') {
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

                    function gtag() {
                        dataLayer.push(arguments);
				}
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
                                    data-type-id="201" data-type-owner="DpwDQa6g">
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