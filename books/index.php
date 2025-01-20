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
    <title>Buy Health and Wellness Books by Dr Cooper</title>
    <meta name="description"
        content="Dr. Dona Cooper is one of the best Physicians and founder of Cooper Wellness Center has written many books on diet plan and healthy lifestyle.">
    <meta name="keywords" content="Get Stronger and Energetic Life, health and wellness books by dr. cooper">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image" content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587366273.jpg">

    <!-- Font icons preconnect -->
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="//d2uolguxr56s4e.cloudfront.net" crossorigin>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//d2uolguxr56s4e.cloudfront.net">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- SweetAlert CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-J3ENz16HEr8XZTRrs3R4MF4ibXWoH1QlR16MJYgMnYgAFK3lrQo3zypfSBYyf2c4" crossorigin="anonymous"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQ6qr0rs03YzFSWuUlym5X2pLN3vN5ghNpb2dUjwg5dkt/h95dH8bdEqH" crossorigin="anonymous"> -->



    <!--
        Google fonts are computed and loaded on page build via save.js
        Individual stylesheets required are listed in /css/pages/skeleton.css
    -->

    <!--<link href="cssskeleton.min.css" rel="stylesheet">-->
    <link type="text/css" rel="preload"
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Aclonica:300,300i,400,400i,600,600i,700,700i,900,900i|Architects+Daughter:300,300i,400,400i,600,600i,700,700i,900,900i|Permanent+Marker:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Aclonica:300,300i,400,400i,600,600i,700,700i,900,900i|Architects+Daughter:300,300i,400,400i,600,600i,700,700i,900,900i|Permanent+Marker:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
    </noscript>

    <script>
        /*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
        (function(w) {
            "use strict";
            if (!w.loadCSS) {
                w.loadCSS = function() {}
            }
            var rp = loadCSS.relpreload = {};
            rp.support = function() {
                var ret;
                try {
                    ret = w.document.createElement("link").relList.supports("preload")
                } catch (e) {
                    ret = false
                }
                return function() {
                    return ret
                }
            }();
            rp.bindMediaToggle = function(link) {
                var finalMedia = link.media || "all";

                function enableStylesheet() {
                    link.media = finalMedia
                }
                if (link.addEventListener) {
                    link.addEventListener("load", enableStylesheet)
                } else if (link.attachEvent) {
                    link.attachEvent("onload", enableStylesheet)
                }
                setTimeout(function() {
                    link.rel = "stylesheet";
                    link.media = "only x"
                });
                setTimeout(enableStylesheet, 3e3)
            };
            rp.poly = function() {
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
                    w.addEventListener("load", function() {
                        rp.poly();
                        w.clearInterval(run)
                    })
                } else if (w.attachEvent) {
                    w.attachEvent("onload", function() {
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

        window.global_id = 'eARVKzNMhyZa';
        window.secure_base_url = '//app.kartra.com/';
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-175519445-2');
    </script>

    <script type="text/javascript">
        (function(c, l, a, r, i, t, y) {
            c[a] = c[a] || function() {
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
            c[a] = c[a] || function() {
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
</head>

<body>
    <div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object"
        data-kt-type="kartra_page_tracking" data-kt-value="eARVKzNMhyZa" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content content--popup-overflow-visible"
                style="background-color: rgb(255, 255, 255); padding: 0px;" id="_66bbb30d1e28e">
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
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d1e28e"
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
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d1e28e"
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
                                                    class="toggle_pagelink" data-frame-id="_66bbb30d1e28e"
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
                                                    data-frame-id="_66bbb30d1e28e" target="_blank">
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
                                    data-frame-id="_66bbb30d1e28e" class="toggle_pagelink" data-project-id="3"
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
                            class="navbar-collapse collapse nav-elem-col js_kartra_component_holder navbar-collapse--md-sm-padding-right-none">
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
                                    const openExchangeAppID = $_ENV['open_exchange_api_key'];
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
                                                console.log("exchange rate from api:", exchangeData.rates.GHS);
                                                console.log("Cost object:", cost);
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
                                        const paystackPublicKey = "<?php echo $paystackPublicKey; ?>";

                                        // Initialize Paystack payment
                                        const handler = PaystackPop.setup({
                                            key: paystackPublicKey,
                                            email: email,
                                            amount: amountInPesewas,
                                            currency: "GHS",
                                            ref: "VMeet" + Math.floor((Math.random() * 1000000000) + 1),
                                            metadata: {
                                                custom_fields: [{
                                                        display_name: "Phone",
                                                        variable_name: "phone",
                                                        value: phone
                                                    },
                                                    {
                                                        display_name: "Duration",
                                                        variable_name: "duration",
                                                        value: duration
                                                    },
                                                    {
                                                        display_name: "Datetime",
                                                        variable_name: "datetime",
                                                        value: datetime
                                                    },
                                                    {
                                                        display_name: "Name",
                                                        variable_name: "name",
                                                        value: name
                                                    },
                                                    {
                                                        display_name: "Reason",
                                                        variable_name: "reason",
                                                        value: reason
                                                    },
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
                                                    window.location.href = "pay/meeting_pay.php?reference=" + response.reference;
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
            <div class="content content--padding-top-medium content--border-bottom-extra-tiny content--border-full-grey"
                style="background-color: rgb(255, 255, 255); padding: 50px 0px 0px;" id="_bnrk0ouz3">
                <div class="background_changer background_changer--blur0 js-bg-next-gen" style="opacity: 1;"
                    data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587369429.jpg")'></div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" id="accordion-Z2Q2IJxx5P" data-component="grid">
                        <div class="col-md-7">
                            <div class="js_kartra_component_holder">
                                <div class="js_kartra_component_holder" id="accordion-XGVl4lfxB9"
                                    data-component="bundle">

                                    <div data-component="headline">
                                        <div class="kartra_headline kartra_headline--h2 kartra_headline--black kartra_headline--source-serif-pro-font kartra_headline--text-center kartra_headline--margin-bottom-extra-tiny"
                                            style="position: relative; margin-top: 0px; margin-bottom: 0px;">
                                            <p style="font-size: 1.2rem;"><strong><span
                                                        style="color: rgb(255, 255, 0); font-size: 1.2rem;"><span
                                                            style="font-family: roboto; font-size: 1.2rem; color: rgb(255, 255, 0);">eBOOKS
                                                            BY DR. DONA COOPER</span></span></strong></p>
                                        </div>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text kartra_text--lato-font kartra_text--light-grey kartra_text--font-weight-thin kartra_text--text-center kartra_text--margin-bottom-special-medium"
                                            style="position: relative; margin-top: 0px; margin-bottom: 10px;">
                                            <p><strong><em>
                                                        <font color="#ffffff">Physician and Founder of Cooper Wellness
                                                            Center</font>
                                                    </em></strong></p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-md-offset-1">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195003_1587091962380DRCooper-image.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195003_1587091962380DRCooper-image.png">
                                        </source><img
                                            class="kartra_image kartra_image--max-width-full kartra_image--margin-bottom-none pull-center background_changer--blur0"
                                            id="1522417521945_formbutton"
                                            style="border-width: 0px; border-style: none; border-color: rgb(51, 51, 51); margin: 0px auto; opacity: 1; width: 204px; max-width: 100%; height: auto;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195003_1587091962380DRCooper-image.png">
                                    </picture>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" style="padding-top: 60px; padding-bottom: 40px; background-color: rgb(245, 245, 245);"
                id="_jdqqkwtyf">
                <div class="background_changer background_changer--blur0" style="background-image: none;"></div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" id="accordion-iRbWvNHCWE" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div id="accordion-o3JW2mESBC" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto 20px; width: 33%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200692_158711746812514dtah-frontcover.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200692_158711746812514dtah-frontcover.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="ZYTAlHiZf1"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200692_158711746812514dtah-frontcover.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">1</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="Oo6wSmDgX4" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">14 DAYS TO AMAZING
                                                                        HEALTH</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="fu84S0mtEQ" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 40); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="http://amzn.to/2r6crMR" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE</a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text" id="iuM88Kci4G">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p style="text-align: justify;"><em>Fourteen Days to Amazing Health i</em>s
                                            written to educate, inspire, and empower readers to take charge of their
                                            health and prevent and reverse chronic diseases using lifestyle
                                            modifications. As a physician practicing internal medicine for more than
                                            twenty-seven years, Dr. Cooper-Dockery has been saddened by the fact that
                                            despite good-quality health care and the advancements in modern medical
                                            science, people are still developing chronic diseases and dying at an
                                            alarming rate. This has propelled her to offer a better alternative, one
                                            that will attack the root causes of diseases, build stronger immune systems,
                                            and promote good health and longevity.</p>
                                    </div>
                                </div>
                                <div id="iEWaRVp6DQ" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);"
                                        onclick="openPaymentPopup('1')"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <script>
                                    let priceInUSD;
                                    let priceInNGN;
                                    let bookName;
                                    let bookpath;

                                    function openPaymentPopup(bookId) {
                                        console.log("Fetching payment details for book ID:", bookId);
                                        const formData = new FormData();
                                        formData.append('book_id', bookId);
                                        fetch('books.php', {
                                            method: 'POST',
                                            body: formData
                                        })
                                            .then(response => {
                                                console.log("HTTP Response:", response); // Logs HTTP status and headers
                                                return response.text(); // Get raw response text for debugging
                                            })
                                            .then(rawData => {
                                                console.log("Raw Data from Server:", rawData);
                                                try {
                                                    const data = JSON.parse(rawData); // Attempt to parse JSON
                                                    console.log("Parsed JSON Data:", data);
                                                    if (data.success) {
                                                        // Process and display data
                                                        document.getElementById('price').innerText = data.data.price_ghs;
                                                        bookName = data.data.title;
                                                        bookpath = data.data.path;
                                                        // console.log("Name of book passed into bookName", bookName);
                                                        document.getElementById('book_name').innerText = bookName;                                                        
                                                        document.getElementById('modal_price').value = data.data.price_ghs;
                                                        document.getElementById('usd_price').innerText = data.data.price_usd;
                                                        document.getElementById('book_id').value = bookId;
                                                        document.getElementById('modal_price').value = data.data.price_ghs;
                                                        priceInUSD = data.data.price_usd;
                                                        priceInNGN = parseFloat(data.data.price_ngn);
                                                        console.log("Price in USD:", priceInUSD);
                                                        console.log("Price in NGN:", priceInNGN, typeof priceInNGN);
                                                    
                                                        // Show the modal
                                                        document.getElementById('paymentModal').style.display = 'flex';
                                                    } else {
                                                        console.error("Server responded with an error:", data.message);
                                                        alert(`Error: ${data.message}`);
                                                    }
                                                } catch (error) {
                                                    console.error("Failed to parse JSON. Raw response:", rawData, "Error:", error);
                                                    alert('An unexpected error occurred. Please try again later.');
                                                }
                                            })
                                            .catch(err => {
                                                console.error("Fetch Error:", err);
                                                alert('Failed to fetch price details. Please check your connection or try again.');
                                            });
                                    }
                                    // Close the modal when clicking outside of it
                                    window.onclick = function(event) {
                                        var modal = document.getElementById("paymentModal");
                                        if (event.target == modal) {
                                            modal.style.display = "none";
                                        }
                                    }
                                </script>
                                <script src="https://js.paystack.co/v1/inline.js"></script>

                                <!-- Modal -->
                                <div id="paymentModal" class="modal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1000; display: none; justify-content: center; align-items: center;">
                                    <div class="modal-content" style="background-color: #fff; width: 90%; max-width: 400px; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); text-align: center; position: relative;">
                                        <h4 style="font-size: 24px; font-weight: bold; color: #333; margin-bottom: 20px;">Confirm Your Payment for <span id="book_name"></span></h4>
                                        <p style="color: #555; margin-bottom: 20px;">Price: USD <span id="usd_price" style="font-weight: bold;"></span> = GHS <span id="price" style="font-weight: bold;"></span></p>
                                        <!--<p>Conversion rate: <span id="rate" style="font-weight: bold;"></span></p>-->
                                        <!--<p style="font-size: 15px; color: #555; margin-bottom: 20px;">Amount Payable: GHS <span id="amount" style="font-weight: bold;"></span></p>-->


                                        <form id="paymentForm" onsubmit="payWithPaystack(event)" style="display: flex; flex-direction: column; align-items: center;">
                                            <input type="hidden" name="book_id" id="book_id">
                                            <input type="hidden" name="price" id="modal_price">

                                            <label for="email" style="align-self: flex-start; font-size: 14px; margin-bottom: 5px; color: #333;">Email:</label>
                                            <input type="mail" id="mail" name="email" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="phone" style="align-self: flex-start; font-size: 14px; margin-bottom: 5px; color: #333;">Phone:</label>
                                            <input type="text" id="phone" name="phone" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">

                                            <label for="currency" style="align-self: flex-start; font-size: 14px; margin-bottom: 5px; color: #333;">Currency:</label>
                                            <select id="currency" name="currency" required style="width: 100%; padding: 10px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                                                <option value="GHS">GHS Cedis</option>
                                                <option value="USD" disabled>USD Dollars</option>
                                                <option value="NGN" disabled>NGN Naira</option>
                                            </select>
                                            <button type="submit" id="paymentproceed" style="padding: 10px 20px; background-color: #f37121; color: #fff; border: none; border-radius: 4px; font-size: 18px; cursor: pointer; font-weight: bold;">Proceed to Payment</button>
                                            <script>
                                                document.querySelector('form').addEventListener('submit', function() {
                                                    const paymentproceed = document.getElementById('paymentproceed');
                                                    paymentproceed.disabled = true;
                                                    paymentproceed.style.backgroundColor = 'grey';
                                                    paymentproceed.innerHTML = 'Payment processing...'
                                                });
                                            </script>
                                        </form>
                                        <button onclick="document.getElementById('paymentModal').style.display='none';" style="position: absolute; top: 10px; right: 10px; background: none; border: none; font-size: 20px; cursor: pointer; color: #555;">&times;</button>
                                    </div>
                                    <script>
                                        function payWithPaystack(e) {
                                            e.preventDefault();
                                            const email = document.getElementById("mail").value;
                                            const phone = document.getElementById("phone").value;
                                            bookName = bookName;
                                            bookpath = bookpath;
                                            // console.log("Book name:", bookName);  
                                            const bookId = document.getElementById("book_id").value;
                                            const priceInGHS = document.getElementById("modal_price").value;
                                            const currency = document.getElementById("currency").value;
                                            const paystackPublicKey = "<?php echo $paystackPublicKey; ?>";
                                            let amount = 0;
                                            // Handle different currencies
                                            if (currency === "GHS") {
                                                // Convert GHS to Pesewas (1 GHS = 100 Pesewas)
                                                amount = Math.round(priceInGHS * 100);
                                            } else if (currency === "USD") {
                                                // Convert USD to Cents (1 USD = 100 cents)
                                                amount = Math.round(priceInUSD * 100);
                                            } else if (currency === "NGN") {
                                                // Convert NGN to Kobo (1 NGN = 100 Kobo)
                                                amount = Math.round(priceInNGN * 100);
                                                // console.log("Nigerian amount:", priceInNGN, typeof priceInNGN);
                                            } else {
                                                console.error("Unsupported currency");
                                                return;
                                            }

                                            // Initialize Paystack payment
                                            const handler = PaystackPop.setup({
                                                key: paystackPublicKey,
                                                email: email,
                                                amount: amount,
                                                currency: currency,
                                                phone: phone,
                                                ref: "BOOK" + Math.floor((Math.random() * 1000000000) + 1),
                                                metadata: {
                                                    custom_fields: [{
                                                            display_name: "Phone",
                                                            variable_name: "phone",
                                                            value: phone
                                                        },
                                                        {
                                                            display_name: "Book ID",
                                                            variable_name: "bookId",
                                                            value: bookId
                                                        },
                                                        {
                                                            display_name: "Book Name",
                                                            variable_name: "bookName",
                                                            value: bookName
                                                        },
                                                        {
                                                            display_name: "Book Path",
                                                            variable_name: "bookpath",
                                                            value: bookpath
                                                        }
                                                    ]
                                                },
                                                callback: function(response) {
                                                    // Payment was successful, use SweetAlert for success message
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Payment Successful!',
                                                        text: 'Please click Ok and hold on as we process your payment',
                                                        confirmButtonText: 'OK'
                                                    }).then(() => {
                                                        window.location.href = "../pay/pay_succesful.php?reference=" + response.reference;
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
                                </div>
                                <div id="y4c98zpiUT" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10233248_1590608009658Frontcover_GHFL.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10233248_1590608009658Frontcover_GHFL.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="1522652135377_formbutton"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10233248_1590608009658Frontcover_GHFL.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: rgb(36, 194, 218); line-height: 1em;">
                                                             </div>

                                                        <p style="line-height: 1em; font-size: 22px;"><span
                                                                style="font-size: 22px; line-height: 1em;">2</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="fEDSfSrNLY" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">GET HEALTHY FOR
                                                                        LIFE</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="YsrMIvQPcP" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://amzn.to/2ZFiM6l" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="text" id="ue8xu86KGT">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p style="text-align: justify;"><strong>This book can save your
                                                life!</strong> It is the Ultimate Prescription and Practical Solution
                                            for Living Disease-free. Dr. Dona Cooper-Dockery reveals the 9 secret
                                            pillars of health to live a longer, stronger, and energetic life. You will
                                            probably never read about these pillars in any other book or learn about
                                            them from your doctor but they are the cornerstone secrets to vibrant well
                                            being, longevity, and disease prevention. This unique program has
                                            revolutionized the health of Dr. Cooper’s patients, many of whom are now
                                            disease-free. Others are enjoying better health on fewer medications. <a
                                                href="https://www.amazon.com/Sea-Saludable-Por-Vida-Secretos/dp/0997337915/ref=tmm_pap_swatch_0?_encoding=UTF8&amp;qid=&amp;sr="
                                                target="_blank" data-frame-id="_jdqqkwtyf"><em><strong>(Also available
                                                        in Spanish)</strong></em></a></p>
                                    </div>
                                </div>
                                <div id="ZGYUfrttFC" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('7');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="NoMKOlyS6w" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9707344_1588792206002Incredibly_Delicious_vegan-recipes-v2.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9707344_1588792206002Incredibly_Delicious_vegan-recipes-v2.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="yyIV5mPcrC"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9707344_1588792206002Incredibly_Delicious_vegan-recipes-v2.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">3</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">INCREDIBLY DELICIOUS
                                                                        VEGAN RECIPES (2nd Ed.)</span></span></strong>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="8RUEeFcxSR" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://amzn.to/2IZZLl0" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p style="text-align: justify;">It’s quite interesting that food, a basic
                                            necessity of life, could either promote chronic diseases or assist in the
                                            prevention and reversal of diseases. In the early centuries, Hippocrates, an
                                            ancient Greek Physician regarded as the father of medicine wrote,
                                            <em><span><span>“let food be thy medicine and medicine be thy food.”
                                                    </span></span></em>Thomas Edison, the American inventor, echoed the
                                            same sentiment when he wrote, <em><span><span>“The doctor of the future will
                                                        give no medicine, but will interest her or his patients in the
                                                        care of the human frame, in a proper diet, and in the cause and
                                                        prevention of disease.” </span></span></em><span>(For the
                                                1st edition, <a href="http://amzn.to/2pqEs5d" target="_blank"
                                                    data-frame-id="_jdqqkwtyf"><span>clic</span></a><span><a
                                                        href="http://amzn.to/2pqEs5d" target="_blank"
                                                        data-frame-id="_jdqqkwtyf"><span>k
                                                            here</span></a>).</span></span>
                                        </p>
                                    </div>
                                </div>
                                <div id="JxPLqBNdrT" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" ;"
                                        data-frame-id="_jdqqkwtyf" onclick="openPaymentPopup('8')">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="8tFb9azADB" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200703_158711756069614dtah-manual-frontcover.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200703_158711756069614dtah-manual-frontcover.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200703_158711756069614dtah-manual-frontcover.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">4</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">14 DAYS TO AMAZING
                                                                        HEALTH WORK MANUAL</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="J3Q0DTVt1d" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://amzn.to/2MfPvtC" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p><span
                                                style="font-size: inherit; font-weight: inherit; text-align: justify; background-color: rgba(0, 0, 0, 0);">It
                                                is the companion guide to the Dr. Cooper-Dockery’s life-transforming
                                                book, </span><b
                                                style="font-size: inherit; text-align: justify; background-color: rgba(0, 0, 0, 0);">Fourteen
                                                Days to Amazing Health:</b><span
                                                style="font-size: inherit; font-weight: inherit; text-align: justify; background-color: rgba(0, 0, 0, 0);"> </span><i
                                                style="font-size: inherit; font-weight: inherit; text-align: justify; background-color: rgba(0, 0, 0, 0);">Success
                                                Strategies to Lose Weight, Reverse Diabetes, Improve Blood Pressure,
                                                Reduce Cholesterol, Reduce Medications, Get Fit and Energized Mentally
                                                and Spiritually.</i></p>

                                        <p>This AMAZING HEALTH WORK MANUAL IS THE ULTIMATE PRESCRIPTION TO CHANGING
                                            HEALTHCARE OUTCOMES.</p>

                                        <p> </p>

                                        <p><b>The manual includes:</b> – fourteen days of educational health topics; – a
                                            fourteen-day meal plan; – a three-level fitness program; and – more than one
                                            hundred delicious, healthy recipes.</p>
                                    </div>
                                </div>
                                <!-- <div id="xVqPUWaGED" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('2')"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div> -->
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 350px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">5</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <h1><span><strong><span style="font-size: 1rem;">MY HEALTH AND
                                                                        THE CREATOR (DIGITAL)</span></strong></span>
                                                        </h1>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="th1qPwccW0" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=f816af5fd431d0a73efe92c044087c75"-->
                                                <!--        data-frame-id="_jdqqkwtyf" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="f816af5fd431d0a73efe92c044087c75"-->
                                                <!--        data-funnel-id="162427" data-product-id="162427"-->
                                                <!--        data-price-point="f816af5fd431d0a73efe92c044087c75"-->
                                                <!--        data-asset-id="0" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <h1>This is a set of eight digital bible study guides which emphasize God’s
                                            design for our physical, mental and spiritual health. </h1>

                                        <ol type="bullet">
                                            <li>Good Health</li>
                                            <li>Nutrition</li>
                                            <li>Happiness &amp; Health</li>
                                            <li>Temperance</li>
                                            <li>Rest &amp; Health</li>
                                            <li>Water &amp; Health</li>
                                            <li>Exercise</li>
                                            <li>Trust in Divine Power</li>
                                        </ol>
                                    </div>
                                </div>
                                <div id="bo0WUAOkGt" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);"
                                        onclick="openPaymentPopup('3')"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>

                                <!--<div data-component="button" id="ndvetVwAQe"><a-->
                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=fee28ea9820b6f1daf03f5c4960d6776"-->
                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--medium kartra_button1--squared pull-left toggle_product default_checkout"-->
                                <!--        style="background-color: rgb(68, 188, 210); color: rgb(255, 255, 255); margin: 0px 0px 20px; font-weight: 700; font-family: lato;"-->
                                <!--        data-kt-type="checkout" data-kt-owner="DpwDQa6g"-->
                                <!--        data-kt-value="fee28ea9820b6f1daf03f5c4960d6776" data-funnel-id="239306"-->
                                <!--        data-product-id="239306" data-price-point="fee28ea9820b6f1daf03f5c4960d6776"-->
                                <!--        data-frame-id="_jdqqkwtyf" data-asset-id="2" target="_parent"> Haga clic aquí-->
                                <!--        para español</a></div>-->
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                    <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image" id="esS6uTmRrP">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 350px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269890_1587370470325large_My_Health_Creator_Eng.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                    <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                    <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                    <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">6</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><span><strong><span style="font-size: 1rem;">MY HEALTH AND
                                                                        THE CREATOR (PHYSICAL)</span></strong></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                    <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                    <div id="50LPAYMYF6" data-component="button"><a
                           class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"
                           style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"
                           href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=b596454b8c65d11b46e3b64532b20e58"
                           data-frame-id="_jdqqkwtyf" data-kt-layout="0"
                           data-kt-type="checkout" data-kt-owner="DpwDQa6g"
                           data-kt-value="b596454b8c65d11b46e3b64532b20e58"
                           data-funnel-id="228785" data-product-id="228785"
                           data-price-point="b596454b8c65d11b46e3b64532b20e58"
                           data-asset-id="3" target="_parent">LEARN MORE </a></div>
                    </div>
                                        </div>
                    </div>
                                </div>
                    <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                    <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <h1>This is a set of eight physical bible study guides which emphasize God’s
                                            design for our physical, mental and spiritual health. </h1>

                                        <ol type="bullet">
                                            <li>Good Health</li>
                                            <li>Nutrition</li>
                                            <li>Happiness &amp; Health</li>
                                            <li>Temperance</li>
                                            <li>Rest &amp; Health</li>
                                            <li>Water &amp; Health</li>
                                            <li>Exercise</li>
                                            <li>Trust in Divine Power</li>
                                        </ol>
                                        <p> </p>
                                    </div>
                                </div>
                    <div id="80wDsbD9Af" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('4');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                    <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                    </div>
                        </div>
                    </div> -->
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269518_1587368807878Exercise-Nutrition-Manual-temp.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269518_1587368807878Exercise-Nutrition-Manual-temp.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269518_1587368807878Exercise-Nutrition-Manual-temp.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">7</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">EXERCISE &amp;
                                                                        NUTRITION MANUAL</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="2fA1SSyH3m" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://amzn.to/2uusJ9r" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <h2>WELCOME TO WELLNESS! Once<b> </b>again, we are excited that you have joined
                                            the Cooper Wellness and Disease Prevention Program. We are committed to
                                            preventing and reducing chronic diseases through lifestyle medicine,
                                            countless number of people have used this program and they are doing well.
                                            This book is a supplement to the Wellness Program Manual and for maximum
                                            health benefits, these books should be used together. You are to use the
                                            recipes and exercise routines as guides as you develop healthier habits. <a
                                                href="https://www.amazon.com/gp/product/1733165460/ref=dbs_a_def_rwt_bibl_vppi_i7"
                                                target="_blank" data-frame-id="_jdqqkwtyf"><em><strong>(Also available
                                                        in Spanish)</strong></em></a>
                                        </h2>
                                    </div>
                                </div>
                                <div id="6g1QXx15VO" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('5');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" style="padding-top: 60px; padding-bottom: 40px; background-color: rgb(245, 245, 245);"
                id="_redytwojy">
                <div class="background_changer background_changer--blur0" style="background-image: none;"></div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" id="accordion-iRbWvNHCWE" data-component="grid"></div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">

                                <div href="javascript: void(0);" data-component="image" id="wv74qnKiPR">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692417_1595731729536UNDO_HYPERTENSION_with_Bleed_1final-page-001.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692417_1595731729536UNDO_HYPERTENSION_with_Bleed_1final-page-001.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="ZYTAlHiZf1"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692417_1595731729536UNDO_HYPERTENSION_with_Bleed_1final-page-001.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">8</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="Oo6wSmDgX4" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p>
                                                            <font face="oswald"><span style="font-size: 22px;"><b>UNDO
                                                                        HYPERTENSION GUIDE</b></span></font>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="fu84S0mtEQ" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&id=ef48d993ace3be5f0ea5d7c8067e420c&kuid=0337e906-87d3-4acd-97d7-a9069d7f7a9c-1730592610&kref=eARVKzNMhyZa" data-frame-id="_jdqqkwtyf"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text" id="iuM88Kci4G">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>This guide will help you to undo hypertension. Implement these insights right
                                            now. "Scientists have  shown that food is the most powerful disease
                                            modifying factor that we can control to change our healthcare destiny. Of
                                            course, in order to attain true health, there are other vital lifestyle
                                            factors that we must also consider and allow to become daily habits. In this
                                            simple nutritional guide, you'll discover eight daily essentials for healthy
                                            living that will propel you on your way to amazing health and
                                            longevity." <strong><em>(Available in print and digital
                                                    download)</em></strong></p>
                                    </div>
                                </div>
                                <div id="iEWaRVp6DQ" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('9');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="y4c98zpiUT" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692446_1595731935423UNDO_DIABETES_BROCHURE_2-page-001.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692446_1595731935423UNDO_DIABETES_BROCHURE_2-page-001.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="1522652135377_formbutton"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692446_1595731935423UNDO_DIABETES_BROCHURE_2-page-001.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: rgb(36, 194, 218); line-height: 1em;">
                                                             </div>

                                                        <p style="line-height: 1em; font-size: 22px;"><span
                                                                style="font-size: 22px; line-height: 1em;">9</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="fEDSfSrNLY" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">UNDO DIABETES
                                                                        GUIDE</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="YsrMIvQPcP" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=cbe5e5991e7053c1be829be3e6ae88ac"-->
                                                <!--        data-frame-id="_redytwojy" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="cbe5e5991e7053c1be829be3e6ae88ac"-->
                                                <!--        data-funnel-id="189332" data-product-id="189332"-->
                                                <!--        data-price-point="cbe5e5991e7053c1be829be3e6ae88ac"-->
                                                <!--        data-asset-id="7" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="text" id="ue8xu86KGT">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>You don't have to let diabetes ruin your life. There's ample solutions to
                                            help you thrive and undo diabetes with this guide. "Scientists have  shown
                                            that food is the most powerful disease modifying factor that we can control
                                            to change our healthcare destiny. Of course, in order to attain true health,
                                            there are other vital lifestyle factors that we must also consider and allow
                                            to become daily habits. In this simple nutritional guide, you'll discover
                                            eight daily essentials for healthy living that will propel you on your way
                                            to amazing health and longevity."</p>

                                        <p style="text-align: justify;"><strong><em>(Available in print and digital
                                                    download)</em></strong></p>

                                        <p style="text-align: justify;"> </p>
                                    </div>
                                </div>
                                <div id="ZGYUfrttFC" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('10');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="NoMKOlyS6w" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692625_1595733039961FIGHT_CANCER_AND_BOOST_IMMUNE_SYSTEM_with_Bleed_1_final-page-001.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692625_1595733039961FIGHT_CANCER_AND_BOOST_IMMUNE_SYSTEM_with_Bleed_1_final-page-001.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="yyIV5mPcrC"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692625_1595733039961FIGHT_CANCER_AND_BOOST_IMMUNE_SYSTEM_with_Bleed_1_final-page-001.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">10</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">FIGHT CANCER &amp;
                                                                        BOOST IMMUNE SYSTEM</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="56l9PC7gOb" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=dad51d9dd1bb9574b7f7ff5a0e3b1a9f"-->
                                                <!--        data-frame-id="_redytwojy" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="dad51d9dd1bb9574b7f7ff5a0e3b1a9f"-->
                                                <!--        data-funnel-id="189518" data-product-id="189518"-->
                                                <!--        data-price-point="dad51d9dd1bb9574b7f7ff5a0e3b1a9f"-->
                                                <!--        data-asset-id="9" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>Cancer is one of the most feared and dreaded disease today. This guide will
                                            show you the foods that not only fight cancer but also boost your immune
                                            system. "Scientists have  shown that food is the most powerful disease
                                            modifying factor that we can control to change our healthcare destiny. Of
                                            course, in order to attain true health, there are other vital lifestyle
                                            factors that we must also consider and allow to become daily habits. In this
                                            simple nutritional guide, you'll discover eight daily essentials for healthy
                                            living that will propel you on your way to amazing health and
                                            longevity." <strong><em>(Available in print and digital
                                                    download)</em></strong></p>
                                    </div>
                                </div>
                                <div id="JxPLqBNdrT" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('6');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="8tFb9azADB" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692497_1595732313847HEART_DISEASE_1final-page-001.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692497_1595732313847HEART_DISEASE_1final-page-001.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692497_1595732313847HEART_DISEASE_1final-page-001.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">11</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">UNDO HEART DISEASE
                                                                        GUIDE</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="J3Q0DTVt1d" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=cd4b14b6d1bf8deecc9ef9dda8b92303"-->
                                                <!--        data-frame-id="_redytwojy" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="cd4b14b6d1bf8deecc9ef9dda8b92303"-->
                                                <!--        data-funnel-id="189519" data-product-id="189519"-->
                                                <!--        data-price-point="cd4b14b6d1bf8deecc9ef9dda8b92303"-->
                                                <!--        data-asset-id="11" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>Undo heart disease with this guide, starting today. "Scientists have  shown
                                            that food is the most powerful disease modifying factor that we can control
                                            to change our healthcare destiny. Of course, in order to attain true health,
                                            there are other vital lifestyle factors that we must also consider and allow
                                            to become daily habits. In this simple nutritional guide, you'll discover
                                            eight daily essentials for healthy living that will propel you on your way
                                            to amazing health and longevity." <strong><em>(Available in print and
                                                    digital download)</em></strong></p>
                                    </div>
                                </div>
                                <div id="xVqPUWaGED" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('11');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692475_1595732095079OBESITY_AND_FATTFY_LIVER_1final-page-001.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692475_1595732095079OBESITY_AND_FATTFY_LIVER_1final-page-001.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 262px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692475_1595732095079OBESITY_AND_FATTFY_LIVER_1final-page-001.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">12</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <h1><span style="font-family:oswald;"><strong><span
                                                                        style="font-size: 1rem;">UNDO OBESITY &amp;
                                                                        FATTY LIVER GUIDE</span></strong></span></h1>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="th1qPwccW0" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=8dfe1b6a735b9b141023d8c9b975a323"-->
                                                <!--        data-frame-id="_redytwojy" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="8dfe1b6a735b9b141023d8c9b975a323"-->
                                                <!--        data-funnel-id="189521" data-product-id="189521"-->
                                                <!--        data-price-point="8dfe1b6a735b9b141023d8c9b975a323"-->
                                                <!--        data-asset-id="13" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>Undo obesity and fatty liver now with this simple guide. "Scientists have 
                                            shown that food is the most powerful disease modifying factor that we can
                                            control to change our healthcare destiny. Of course, in order to attain true
                                            health, there are other vital lifestyle factors that we must also consider
                                            and allow to become daily habits. In this simple nutritional guide, you'll
                                            discover eight daily essentials for healthy living that will propel you on
                                            your way to amazing health and longevity." <strong><em>(Available in print
                                                    and digital download)</em></strong></p>
                                    </div>
                                </div>
                                <div id="bo0WUAOkGt" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('12');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div id="accordion-x0WBg35OK6" href="javascript: void(0);" data-component="image">
                                    <a href="https://www.amazon.com/Wellness-Program-Manual-Disease-Prevention-ebook/dp/B0842RMFMF/ref=sr_1_4?crid=19TH9B4CT8SE0&amp;dchild=1&amp;keywords=dr.+dona+cooper-dockery&amp;qid=1587402636&amp;sprefix=Dr.+Dona+Cooper%2Caps%2C413&amp;sr=8-4"
                                        class="toggle_pagelink " data-frame-id="_redytwojy" target="_blank">
                                        <picture>
                                            <source type="image/webp"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692584_1595732684635UNDO_HYPERTENSION_with_Bleed_1final-page-008.webp">
                                            </source>
                                            <source type="image/jpeg"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692584_1595732684635UNDO_HYPERTENSION_with_Bleed_1final-page-008.jpg">
                                            </source><img
                                                class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                id="7vdvn963gc"
                                                style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/11692584_1595732684635UNDO_HYPERTENSION_with_Bleed_1final-page-008.jpg">
                                        </picture>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;">13</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">UNDO DISEASES
                                                                        BOOKLETS COMBO</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="50LPAYMYF6" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=c5201f859866d890966e50186866d8ca"-->
                                                <!--        data-frame-id="_redytwojy" data-kt-type="checkout"-->
                                                <!--        data-kt-owner="DpwDQa6g"-->
                                                <!--        data-kt-value="c5201f859866d890966e50186866d8ca"-->
                                                <!--        data-funnel-id="189524" data-product-id="189524"-->
                                                <!--        data-price-point="c5201f859866d890966e50186866d8ca"-->
                                                <!--        data-asset-id="15" target="_parent">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>Get all <u>5</u> booklets above in one combo package: <em>Undo Hypertension,
                                                Undo Diabetes, Fight &amp; Boost Immune System, Undo Heart Disease, and
                                                Undo Obesity &amp; Fatty Liver.</em> </p>

                                        <p>"Scientists have  shown that food is the most powerful disease modifying
                                            factor that we can control to change our healthcare destiny. Of course, in
                                            order to attain true health, there are other vital lifestyle factors that we
                                            must also consider and allow to become daily habits. In this simple
                                            nutritional guide, you'll discover eight daily essentials for healthy living
                                            that will propel you on your way to amazing health and longevity." </p>

                                        <p><strong><em>(Available in print and digital download)</em></strong></p>

                                        <p> </p>
                                    </div>
                                </div>
                                <!-- <div id="SyiOPIV3DN" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('13');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div> -->
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image" id="oIqNO4SRbb">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269479_1587368608664Incredibly_Delicious_Cover_for_Kindle.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269479_1587368608664Incredibly_Delicious_Cover_for_Kindle.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="yyIV5mPcrC"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269479_1587368608664Incredibly_Delicious_Cover_for_Kindle.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">14</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="accordion-1IF2TRY3F3" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <p><strong><span style="font-size:22px;"><span
                                                                        style="font-family:oswald;">INCREDIBLY DELICIOUS
                                                                        VEGAN (1ST ED.)</span></span></strong></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="wpFcwA8kmf" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink "-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="http://amzn.to/2pqEs5d" data-frame-id="_redytwojy"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text" id="2sF6DTGVUl">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p style="text-align: justify;">It’s quite interesting that food, a basic
                                            necessity of life, could either promote chronic diseases or assist in the
                                            prevention and reversal of diseases. In the early centuries, Hippocrates, an
                                            ancient Greek Physician regarded as the father of medicine wrote,
                                            <em><span>“let food be thy medicine and medicine be thy food.”
                                                </span></em>Thomas Edison, the American inventor, echoed the same
                                            sentiment when he wrote, <em><span><span>“The doctor of the future will give
                                                        no medicine, but will interest her or his patients in the care
                                                        of the human frame, in a proper diet, and in the cause and
                                                        prevention of disease.”</span></span></em>
                                        </p>
                                    </div>
                                </div>
                                <!-- <div id="BpDSstJHng" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('14');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div> -->
                                <div id="Vi75H0mwXX" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" style="padding-top: 60px; padding-bottom: 40px; background-color: rgb(245, 245, 245);"
                id="_i2v9c7fr0">
                <div class="background_changer background_changer--blur0" style="background-image: none;"></div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" id="accordion-iRbWvNHCWE" data-component="grid"></div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">

                                <div href="javascript: void(0);" data-component="image" id="oYG1O1lPnS">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9281007_1587403186306English-Wellness-Manual-Front.webp">
                                        </source>
                                        <source type="image/jpeg"
                                            data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9281007_1587403186306English-Wellness-Manual-Front.jpg">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            id="7vdvn963gc"
                                            style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9281007_1587403186306English-Wellness-Manual-Front.jpg">
                                    </picture>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">
                                        <div class="col-xs-2 col-md-1 column--vertical-center">
                                            <div class="js_kartra_component_holder">
                                                <div id="dJ6xv" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                                        style="position: relative;width: 50px;height: 50px;">
                                                        <div class="background-item background-item--rounded-full"
                                                            style="background-color: #24c2da;"> </div>

                                                        <p style="font-size: 22px; line-height: 1em;"><span
                                                                style="font-size: 22px; line-height: 1em;">15</span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-7 col-md-8 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; padding: 0px 15px 0px 25px; background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <div id="Oo6wSmDgX4" data-component="headline">
                                                    <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                                        style="position: relative; margin: 0px;">
                                                        <h3><span><strong><span style="font-family: oswald;"><span
                                                                            style="font-size: 0.8rem; font-family: oswald;">COOPER
                                                                            WELLNESS CENTER PROGRAM
                                                                            MANUAL</span></span></strong></span></h3>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-xs-3 col-md-3 column--vertical-center"
                                            style="background-color: rgba(0, 0, 0, 0);border-radius: 0px;border-width: 0px;border-style: none;padding: 0px 15px 0px 25px;background-image: none;">
                                            <div class="js_kartra_component_holder">
                                                <!--<div id="NPb74hxk8i" data-component="button"><a-->
                                                <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_pagelink"-->
                                                <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                                <!--        href="https://amzn.to/37RlaHY" data-frame-id="_i2v9c7fr0"-->
                                                <!--        target="_blank">LEARN MORE </a></div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion-N1LJq53bEO" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto; width: 100%; border-top: 1px solid rgb(204, 204, 204); border-right-style: solid; border-bottom-style: solid; border-left-style: solid; border-right-color: rgb(204, 204, 204); border-bottom-color: rgb(204, 204, 204); border-left-color: rgb(204, 204, 204);">
                                </div>
                                <div data-component="text" id="iuM88Kci4G">
                                    <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                        style="position: relative;">
                                        <p>There are many sincere individuals who recognize and understand that it is
                                            important to improve their behaviors with regard to health. They also intend
                                            to make changes that would contribute to improving their lifestyle and
                                            therefore, their quality of life. However, many times, in their thirst for
                                            change, most do not achieve their goals and become so frustrated that they
                                            quit moving forward.</p>

                                        <p>That doesn't have to be you my friend. You can transform your health and
                                            we're committed to reducing and improving our patients’ conditions through
                                            lifestyle medicine. We are excited that you have chosen to join the Cooper
                                            Wellness &amp; Disease Prevention Center Wellness Program and this manual
                                            will serve as a pivotal guide on your journey.  <a data-frame-=""
                                                href="https://www.amazon.com/Manual-Programa-Para-Bienestar-Spanish/dp/0997337974/ref=sr_1_11?dchild=1&amp;qid=1587403932&amp;refinements=p_27%3ADr.+Dona+Cooper-Dockery&amp;s=books&amp;sr=1-11&amp;text=Dr.+Dona+Cooper-Dockery"
                                                target="_blank" data-frame-id="_i2v9c7fr0"><em><strong>(Also available
                                                        in Spanish)</strong></em></a></p>
                                    </div>
                                </div>
                                <div id="y4c98zpiUT" data-component="divider">
                                    <hr class="kartra_divider kartra_divider--border-tiny kartra_divider--border-full-light-grey kartra_divider--full"
                                        style="margin: 0px auto;width: 100%;border-top: 1px solid rgb(204, 204, 204);border-right-style: solid;border-bottom-style: solid;border-left-style: solid;border-right-color: rgb(204, 204, 204);border-bottom-color: rgb(204, 204, 204);border-left-color: rgb(204, 204, 204);padding-bottom: 10px;">
                                </div>
                                <div id="kdOwVUW4Xs" data-component="button">
                                    <a
                                        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                        href="javascript:void(0);" onclick="openPaymentPopup('15');"
                                        data-frame-id="_jdqqkwtyf">
                                        <span
                                            class="kartra_icon__icon fa fa-book"
                                            style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                        GET YOUR COPY
                                    </a>
                                </div>
                            </div>


                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">
                        <div class="col-md-4">
                            <div class="js_kartra_component_holder">
                                <div href="javascript: void(0);" data-component="image" id="3zACWy23CS">
                                    <a href="https://amzn.to/3vAPemy" class="toggle_pagelink" target="_blank"
                                        data-frame-id="_i2v9c7fr0">
                                        <picture>
                                            <source type="image/webp"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784401_16288853548cmCharlieGoesBookFrontCover300dpiRGB.webp">
                                            </source>
                                            <source type="image/jpeg"
                                                data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784401_16288853548cmCharlieGoesBookFrontCover300dpiRGB.jpg">
                                            </source><img
                                                class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                id="7vdvn963gc"
                                                style="border-width: 0px; margin: 0px auto 20px; width: 256px; height: auto; max-width: 100%; opacity: 1; border-color: rgb(51, 51, 51); border-style: none;"
                                                src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" alt=""
                                                data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784401_16288853548cmCharlieGoesBookFrontCover300dpiRGB.jpg">
                                        </picture>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div id="p9YbMeQ9sa" data-component="headline">
                                        <div class="kartra_headline kartra_headline--h3 kartra_headline--white kartra_headline--lato-font kartra_headline--font-weight-bold kartra_headline--vertical-center kartra_headline--size-extra-small kartra_headline--xs-size-tiny kartra_headline--margin-bottom-none"
                                            style="position: relative;width: 50px;height: 50px;">
                                            <div class="background-item background-item--rounded-full"
                                                style="background-color: #24c2da;"> </div>

                                            <p style="font-size: 22px; line-height: 1em;"><span
                                                    style="font-size: 22px; line-height: 1em;">16</span></p>
                                        </div>
                                    </div>
                                    <!--<div id="9jmV0JIALH" data-component="button"><a-->
                                    <!--        class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-right toggle_product default_checkout"-->
                                    <!--        style="background-color: rgb(49, 85, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 400; padding: 8px 12px; font-family: Lato;"-->
                                    <!--        href="https://app.kartra.com/redirect_to/?asset=checkout&amp;id=78b49a57e0b449e990767bd5d3765a29"-->
                                    <!--        data-frame-id="_i2v9c7fr0" data-kt-type="checkout" data-kt-owner="DpwDQa6g"-->
                                    <!--        data-kt-value="78b49a57e0b449e990767bd5d3765a29" data-funnel-id="314234"-->
                                    <!--        data-product-id="314234" data-price-point="78b49a57e0b449e990767bd5d3765a29"-->
                                    <!--        data-asset-id="16" target="_parent">GET IT NOW...</a></div>-->
                                    <div id="XpsYOaa8S9" data-component="headline">
                                        <div class="kartra_headline kartra_headline--h2 kartra_headline--sapphire-blue kartra_headline--alegreya-sans-font kartra_headline--text-left kartra_headline--margin-bottom-none"
                                            style="position: relative; margin: 0px;">
                                            <h3><span><strong><span style="font-family: oswald;"><span
                                                                style="font-size: 0.8rem; font-family: oswald;">CHARLIE
                                                                GOES TO THE DOCTOR</span></span></strong></span></h3>
                                        </div>
                                    </div>
                                    <div data-component="text" id="xw07RLzBFC">
                                        <div class="kartra_text kartra_text--text-left kartra_text--sm-text-center kartra_text--light-grey"
                                            style="position: relative;">
                                            <h4>
                                                <b>Who really likes to get sick or be taken to the doctor? </b>I’m sure
                                                no one likes that. When poor Charlie got a sore throat and tummy ache,
                                                he felt so awful that mom rushed him over to Dr. Cooper, who discovered
                                                Charlie had a tonsil infection. However, instead of giving him
                                                medication, Dr. Cooper took Charlie on her <b>GET HEALTHY</b> train for
                                                the ride of a lifetime. Charlie met many friends who taught him the
                                                secrets of good health and how to build a strong immune system to keep
                                                his body healthy and free of infection and disease. You'll love the
                                                beautiful illustrations. Enjoy the ride and activities along the way!
                                            </h4>
                                        </div>
                                    </div>
                                    <div id="FDgEndCVNZ" data-component="button">
                                        <a
                                            class="kartra_button1 kartra_button1--default kartra_button1--box-shadow-inset-bottom kartra_button1--solid kartra_button1--small kartra_button1--squared pull-left toggle_pagelink"
                                            style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px; font-weight: 700; padding: 8px 12px; font-family: Lato;"
                                            href="javascript:void(0);" onclick="openPaymentPopup('16');"
                                            data-frame-id="_jdqqkwtyf">
                                            <span
                                                class="kartra_icon__icon fa fa-book"
                                                style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>
                                            GET YOUR COPY
                                        </a>
                                    </div>
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">

                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">

                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">

                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">

                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="row" id="accordion-hNUQVoeruR"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 80px; background-image: none;"
                        data-component="grid">

                        <div class="col-md-8">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_headline_block__index js_kartra_component_holder">
                                    <div class="row row--equal" id="zcwnT"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; margin-top: 0px; margin-bottom: 20px; background-image: none;"
                                        data-component="grid">



                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade popup_modal popup-modal js_trackable_wrapper" id="popup_landing" data-delay="20"
                data-reocur="every" role="dialog" aria-hidden="true">
                <button type="button" class="closer close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">×</span></button>
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">

                            <div class="content content--popup-large" style="background-color: rgb(255,255,255);"
                                id="_gw0zunvc0">
                                <div class="background_changer"></div>
                                <div class="background_changer_overlay"></div>
                                <div class="container-fluid page-popup-container--large">
                                    <div
                                        class="kartra_element_bg kartra_element_bg--margin-left-negative kartra_element_bg--margin-right-negative kartra_element_bg--margin-bottom-none kartra_element_bg--padding-top-bottom-special-large-left-right-small-sm-adjust kartra_element_bg--margin-bottom-extra-small kartra_element_bg--popup-2-box-shadow js_kartra_component_holder">
                                        <div style="background-color: rgba(255, 255, 255, 0.4);"
                                            class="background-item background-item--border-popup-2-box"></div>
                                        <div data-component="headline" id="xdhBlCbS5x">
                                            <div class="kartra_headline kartra_headline--size-giant kartra_headline--dim-grey kartra_headline--text-center kartra_headline--oswald-font kartra_headline--font-weight-medium"
                                                style="position: relative;">
                                                <p><span
                                                        style='background-color: rgb(255, 255, 0); font-family: "permanent marker"; color: rgb(0, 0, 0);'>Newly Release
                                                        for Kids!</span></p>
                                            </div>
                                        </div>
                                        <div data-component="image" href="javascript: void(0);"><a
                                                href="https://amzn.to/3vAPemy" class="toggle_pagelink" target="_blank"
                                                data-frame-id="_gw0zunvc0">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784502_1628885706jO6Charliebook-little-girl-reading.webp">
                                                    </source>
                                                    <source type="image/png"
                                                        data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784502_1628885706jO6Charliebook-little-girl-reading.png">
                                                    </source><img
                                                        class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                                        alt=""
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; opacity: 1;"
                                                        data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/21784502_1628885706jO6Charliebook-little-girl-reading.png">
                                                </picture>
                                            </a></div>


                                        <div class="row" data-component="grid">
                                            <div class="col-md-12 column--padding-none">
                                                <div class="js_kartra_component_holder">



                                                    <div data-component="button">
                                                        <div data-component="button"><a href="javascript:void(0);"
                                                                class="kartra_button1 kartra_button1--default kartra_button1--oswald-font kartra_button1--border-extra-small kartra_button1--border-white kartra_button1--shadow-06 kartra_button1--solid kartra_button1--large kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_product js_kartra_trackable_object dg_popup"
                                                                style="background-color: rgb(252, 0, 0); color: rgb(255, 251, 251); margin: 0px auto 20px; font-weight: 700; font-family: Oswald;"
                                                                data-frame-id="_gw0zunvc0" data-kt-layout="0"
                                                                data-kt-type="checkout" data-kt-owner="DpwDQa6g"
                                                                data-kt-value="78b49a57e0b449e990767bd5d3765a29"
                                                                data-funnel-id="314234" data-product-id="314234"
                                                                data-price-point="78b49a57e0b449e990767bd5d3765a29"
                                                                rel="78b49a57e0b449e990767bd5d3765a29"
                                                                data-asset-id="19" target="_parent">GET YOUR COPY NOW
                                                                &gt;&gt;&gt;</a></div>
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
        <script async defer src="https://app.kartra.com/resources/js/popup"></script>
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
    <script src="//app.kartra.com/resources/js/page_check?page_id=eARVKzNMhyZa" async defer></script>
    <script>
        if (typeof window['jQuery'] !== 'undefined') {
            window.jsVars = {
                "page_title": "Buy Health and Wellness Books by Dr Cooper",
                "page_description": "Dr. Dona Cooper is one of the best Physicians and founder of Cooper Wellness Center has written many books on diet plan and healthy lifestyle.",
                "page_keywords": "Get Stronger and Energetic Life, health and wellness books by dr. cooper",
                "page_robots": "index, follow",
                "secure_base_url": "\/\/app.kartra.com\/",
                "global_id": "eARVKzNMhyZa"
            };
            window.global_id = 'eARVKzNMhyZa';
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
    <!-- <div class="gdpr_flapjack_banner js_gdpr_flapjack_banner lang-var-{language_code}" style="display: none;">
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
                                    data-type-id="181" data-type-owner="DpwDQa6g">
                                    {:lang_general_save}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!--// GDPR cookie BANNER -->

    <script src="//app.kartra.com/resources/js/kartra_embed_wild_card?type=kartra_page&amp;owner=DpwDQa6g"></script>
    <!--// GDPR cookie BANNER -->
</body>

</html>