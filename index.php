<?php
session_start();
require('conn/conn.php');
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
    setcookie('referralCode', $referralCodeFromUrl, time() + (86400 * 30), "/");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dr Cooper Health and Disease Prevention Center</title>
    <meta name="description"
        content="We are a leading Health and Healing center in  Texas, helps you to boost your immunity in Covid 19 days.  Book your Apointment @ 956-627-3106.">
    <meta name="keywords"
        content="Wellness and Disease Prevention Center, Disease Prevention Center, Health and Healing Center, Boost Your Immunity in Covid">
    <meta name="robots" content="index, follow">
    <link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="author" content="">
    <meta property="og:title" content="">
    <meta property="og:description" content="">
    <meta property="og:image"
        content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9197948_1587103031619Medium-CWC-logo.png">

    <!-- Font icons preconnect -->
    <link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="//d2uolguxr56s4e.cloudfront.net" crossorigin>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//d2uolguxr56s4e.cloudfront.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>



    <!--
        Google fonts are computed and loaded on page build via save.js
        Individual stylesheets required are listed in /css/pages/skeleton.css
    -->

    <!--<link href="cssskeleton.min.css" rel="stylesheet">-->
    <link type="text/css" rel="preload"
        href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Varela+Round:300,300i,400,400i,600,600i,700,700i,900,900i|Quicksand:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="stylesheet" href="css/new_bootstrap.css">

    <link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

    <noscript>
        <link rel="stylesheet" href="css/kartra_components.css">
        <link rel="stylesheet" href="css/font-awesome.css">
        <link type="text/css" rel="stylesheet"
            href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Josefin+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Zilla+Slab:300,300i,400,400i,600,600i,700,700i,900,900i|Varela+Round:300,300i,400,400i,600,600i,700,700i,900,900i|Quicksand:300,300i,400,400i,600,600i,700,700i,900,900i|Montserrat:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Fira+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
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

        window.global_id = '6hMi8IQ4msrK';
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
        var google_analytics = null; <
        !--Global site tag(gtag.js) - Google Analytics-- >
            <
            script async src = "https://www.googletagmanager.com/gtag/js?id=UA-175519445-2" >
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
        data-kt-type="kartra_page_tracking" data-kt-value="6hMi8IQ4msrK" data-kt-owner="DpwDQa6g">
    </div>
    <div id="page" class="page container-fluid">
        <div id="page_background_color" class="row">
            <div class="content content--popup-overflow-visible"
                style="background-color: rgb(255, 255, 255); padding: 0px;" id="_5feca5d6e913f">
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
                                                    class="toggle_pagelink" data-frame-id="_5feca5d6e913f"
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
                                                    class="toggle_pagelink" data-frame-id="_5feca5d6e913f"
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
                                                    class="toggle_pagelink" data-frame-id="_5feca5d6e913f"
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
                                                    data-frame-id="_5feca5d6e913f" target="_blank">
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
                                <a href=""
                                    data-frame-id="_5feca5d6e913f" class="toggle_pagelink" data-project-id="3"
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
                                data-target="#navbar_HjFs14RpbR" aria-expanded="false" aria-controls="navbar">
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
                                            href="#" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: Roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="111" target="_parent">HOME</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="about" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="107" target="_parent">ABOUT</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="books" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="181" target="_parent">BOOKS</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="courses" data-color="#424242" data-frame-id="_5feca5d6e913f"
                                            onmouseover="this.style.backgroundColor='rgb(49, 85, 40)'"
                                            onmouseout="this.style.backgroundColor='transparent'"
                                            style="color: rgb(66, 66, 66); font-weight: 400; font-family: roboto; background-color: transparent;"
                                            data-project-id="3" data-page-id="191" target="_parent">COURSES</a>
                                    </li>
                                    <li class="propClone">
                                        <a class="nav__link--padding-top-bottom-extra-tiny nav__link--rounded-small nav__link--style-dark nav__link--style-three toggle_pagelink"
                                            href="programs" data-color="#424242" data-frame-id="_5feca5d6e913f"
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
                                        <a href="contact"
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

                                        const paystackPublicKey = "pk_test_f5b5f05ffa20e04d5a54bedf16e0605ddab5281c";

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
            <div class="content content--padding-large"
                style="background-color: rgb(255, 255, 255); padding: 30px 0px 40px;" id="_19z3ryo4f">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="video"
                                    data-thumbnail="https://d2uolguxr56s4e.cloudfront.net/img/shared/kartra_logo_color.svg"
                                    data-screenshot="false">
                                    <div class="kartra_video kartra_video_containerkXD4fThsiEdT js_kartra_trackable_object"
                                        style="margin-top: 0px; margin-bottom: 20px; border-radius: 0px; padding-bottom: 56.25%;"
                                        data-kt-type="video" data-kt-value="kXD4fThsiEdT" data-kt-owner="DpwDQa6g"
                                        id="kXD4fThsiEdT/zaaba/?autoplay=true&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=true"
                                        data-random_str="zaaba">
                                        <script
                                            src="https://app.kartra.com/video/kXD4fThsiEdT/zaaba/?autoplay=true&amp;mute_on_start=false&amp;show_controls=true&amp;sticky=false&amp;resume_playback=true"></script>
                                    </div>
                                </div>
                                <div data-component="image"><span class="el_wrapper"></span></div>
                                <div data-component="headline" id="A2ucp7vBJ5">
                                    <div class="kartra_headline kartra_headline--size-m-giant kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-big-tiny"
                                        style="position: relative;" aria-controls="cke_58" aria-activedescendant=""
                                        aria-autocomplete="list" aria-expanded="false">
                                        <p style="line-height: 1.2em; font-size: 1.6rem;"><strong><span
                                                    style="color: rgb(0, 51, 0); line-height: 1.2em; font-size: 1.6rem;"><span
                                                        style="line-height: 1.2em; font-size: 1.6rem; font-family: roboto; color: rgb(0, 51, 0);">Follow
                                                        Dr. Dona Cooper on Social Media</span></span></strong></p>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9475133_5ea7a3950d7e5_DRCooper-image.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9475133_5ea7a3950d7e5_DRCooper-image.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                            alt="Dr. Dona Cooper-Dockery, MD profile pic"
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; border-radius: 0px; opacity: 1; width: 243px; max-width: 100%; height: auto;"
                                            data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9475133_5ea7a3950d7e5_DRCooper-image.png">
                                    </picture>
                                </div>
                                <div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--sm-align-center social_icons_wrapper--margin-bottom-extra-small social_icons_wrapper--negative-margin-left-right-extra-tiny pull-center hover-zoomIn"
                                    data-component="bundle" id="4Q9pZzc13A_19PBrwX9u4" style="margin: 0px auto 20px;">
                                    <div data-component="icon">
                                        <a href="https://www.facebook.com/cooperwellness/" class="toggle_pagelink"
                                            data-frame-id="_19z3ryo4f" target="_blank">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--circled kartra_icon--giant"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(0, 72, 244);">
                                                <span style="color: rgb(0, 72, 244);"
                                                    class="kartra_icon__icon fa fa-facebook"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://www.instagram.com/cooperwellnesscenter/"
                                            class="toggle_pagelink" data-frame-id="_19z3ryo4f" target="_blank">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--circled kartra_icon--giant"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(255, 0, 201);">
                                                <span style="color: rgb(255, 0, 201);"
                                                    class="kartra_icon__icon fa fa-instagram"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://www.youtube.com/channel/UCihzseMaylCivEhN5lN9Peg/?sub_confirmation=1"
                                            class="toggle_pagelink" data-frame-id="_19z3ryo4f" target="_blank">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--circled kartra_icon--giant"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(255, 14, 0);">
                                                <span style="color: rgb(255, 14, 0);"
                                                    class="kartra_icon__icon fa fa-youtube-square"></span>
                                            </div>
                                        </a>
                                    </div>
                                    <div data-component="icon" href="javascript: void(0);">
                                        <a href="https://twitter.com/DrCooperDockery" class="toggle_pagelink"
                                            data-frame-id="_19z3ryo4f" target="_blank">
                                            <div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--circled kartra_icon--giant"
                                                style="background-color: rgba(0, 0, 0, 0); margin: 0px auto; border-color: rgb(0, 0, 0);">
                                                <span style="color: rgb(0, 0, 0);"
                                                    class="kartra_icon__icon fa fa-twitter"></span>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                                <div data-component="image" href="javascript: void(0);">
                                    <img class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="button"><a
                                        href="books"
                                        class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                        style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                        data-frame-id="_19z3ryo4f" data-project-id="3" data-page-id="181"
                                        target="_parent">BOOKS &amp; MEDIA BY DR. DONA COOPER</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-3 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--black-ipad-mockup kartra_element_wrapper--margin-left-right-extra-medium kartra_element_wrapper--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div class="background-item background-item--black-ipad background_changer--blur0 js-bg-next-gen"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200692_158711746812514dtah-frontcover.jpg")'>
                                    </div>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        class="joint_device_mock_up--frame"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/single-black-ipad-mockup.png">
                                </div>


                                <div data-component="text">
                                    <div class="kartra_text kartra_text--light-grey kartra_text--extra-small kartra_text--text-center kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>Get RX strategies to lose weight, reverse diabetes, and reduce cholesterol
                                            and medications.</p>
                                    </div>
                                </div>
                                <div data-component="button">
                                    <a href="books#Oo6wSmDgX4"
                                        class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink"
                                        data-frame-id="_19z3ryo4f"
                                        style="background-color: rgb(243, 113, 33); color: rgb(254, 255, 253); margin: 0px auto 20px; font-weight: 700; font-family: roboto;"
                                        target="_blank">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right"
                                            style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--black-ipad-mockup kartra_element_wrapper--margin-left-right-extra-medium kartra_element_wrapper--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div class="background-item background-item--black-ipad background_changer--blur0 js-bg-next-gen"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9269432_1587368417080DRCooper-39.jpg")'>
                                    </div>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        class="joint_device_mock_up--frame"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/single-black-ipad-mockup.png">
                                </div>


                                <div data-component="text">
                                    <div class="kartra_text kartra_text--light-grey kartra_text--extra-small kartra_text--text-center kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>The healing power of a healthy lifestyle series now available in both
                                            MP4 download and DVD.</p>
                                    </div>
                                </div>
                                <div data-component="button">
                                    <a href="https://bit.ly/healthylifestylebundle"
                                        class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink"
                                        data-frame-id="_19z3ryo4f"
                                        style="background-color: rgb(243, 113, 33); color: rgb(252, 255, 251); margin: 0px auto 20px; font-weight: 700; font-family: roboto;"
                                        target="_blank">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right"
                                            style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--black-ipad-mockup kartra_element_wrapper--margin-left-right-extra-medium kartra_element_wrapper--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div class="background-item background-item--black-ipad background_changer--blur0 js-bg-next-gen"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10233248_1590608009658Frontcover_GHFL.jpg")'>
                                    </div>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        class="joint_device_mock_up--frame"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/single-black-ipad-mockup.png">
                                </div>


                                <div data-component="text">
                                    <div class="kartra_text kartra_text--light-grey kartra_text--extra-small kartra_text--text-center kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>Discover the 9 Secret Pillars of health to live a longer, stronger, and
                                            energetic life.</p>
                                    </div>
                                </div>
                                <div data-component="button">
                                    <a href="books#fEDSfSrNLY"
                                        class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink"
                                        data-frame-id="_19z3ryo4f"
                                        style="background-color: rgb(243, 113, 33); color: rgb(252, 253, 251); margin: 0px auto 20px; font-weight: 700; font-family: roboto;"
                                        target="_blank">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right"
                                            style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--black-ipad-mockup kartra_element_wrapper--margin-left-right-extra-medium kartra_element_wrapper--margin-bottom-extra-small"
                                    data-component="bundle" style="margin-top: 0px; margin-bottom: 20px; padding: 0px;">
                                    <div class="background-item background-item--black-ipad background_changer--blur0 js-bg-next-gen"
                                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                        data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10233202_1590607933393CWC_Cookbook_eBook_Front_Cover.jpg")'>
                                    </div>
                                    <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        class="joint_device_mock_up--frame"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/single-black-ipad-mockup.png">
                                </div>


                                <div data-component="text">
                                    <div class="kartra_text kartra_text--light-grey kartra_text--extra-small kartra_text--text-center kartra_text--margin-bottom-extra-medium"
                                        style="position: relative;">
                                        <p>A 28-Day meal plan guide to guilt-free vegan recipes, shopping lists, and
                                            more.</p>
                                    </div>
                                </div>
                                <div data-component="button">
                                    <a href="books#accordion-1IF2TRY3F3"
                                        class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink"
                                        data-frame-id="_19z3ryo4f"
                                        style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-weight: 700; font-family: roboto;"
                                        target="_blank">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right"
                                            style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="row" data-component="grid">-->
                    <!--	<div class="col-md-12">-->
                    <!--		<div class="js_kartra_component_holder">-->
                    <!--			<div data-component="image" href="javascript: void(0);" id="7Dnbv0smoN">-->
                    <!--<img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;" data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">-->
                    <!--			</div>-->
                    <!--			<div data-component="button" id="c2AHdHqlia"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=X7M6t4dB2vZa" class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink" style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;" data-frame-id="_19z3ryo4f" data-project-id="3" data-page-id="25" target="_parent">Try Our Get Healthy for Life SUPPLEMENTS</a></div>-->
                    <!--		</div>-->
                    <!--	</div>-->
                    <!--</div>-->
                    <div class="row row--equal" data-component="grid" id="JM1BD64bkO"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-width: 0px; border-style: none; background-image: none;">
                        <!--<div class="col-md-4">-->
                        <!--<div class="js_kartra_component_holder">-->
                        <!--<div class="kartra_element_bg kartra_element_bg--box-one kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="accordion-zUVIkF9IyQ_PChjrfA2ht_Q7I3Flw7pa" style="margin: 0px; padding: 0px;">-->
                        <!--<div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 0px; border-style: none; padding: 30px 50px 10px; background-image: none; opacity: 1; border-color: rgb(51, 51, 51);" class="background-item background-item--sp-45-border-top background_changer--blur0"></div>-->
                        <!--<div data-component="image" href="javascript: void(0);">-->
                        <!--	<a href="javascript: void(0);" class="toggle_pagelink " data-frame-id="_19z3ryo4f" target="_blank" data-project-id="0" data-page-id="182"><span class="el_wrapper"></span></a>-->
                        <!--</div>-->
                        <!--<div data-component="image" href="javascript: void(0);" id="lZKLeItWYs"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=VJL1m3BKHpYf" class="toggle_pagelink" data-frame-id="_19z3ryo4f" data-project-id="3" data-page-id="203" target="_parent"><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" style="margin: 0px auto 20px; opacity: 1; width: 190px; max-width: 100%; height: auto; border-radius: 0px;" alt="" data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/666188074663Immune_Booster_FBAd.png"></a></div>-->
                        <!--<div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="491413PpQk" style="margin-top: 0px; margin-bottom: 0px; padding: 15px 15px 0px;">-->
                        <!--	<div style="border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1; background-color: rgba(0, 0, 0, 0);" class="background-item background_changer--blur0"></div>-->
                        <!--	<div data-component="text" id="JVDqkqJ9u2">-->
                        <!--		<div class="kartra_text kartra_text--large kartra_text--dim-black kartra_text--roboto-font kartra_text--font-weight-regular" style="position: relative;" aria-controls="cke_852" aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">-->
                        <!--			<p style="font-size: 0.8rem; line-height: 1.2em;"><strong><span style="font-size: 0.8rem; line-height: 1.2em;"><span style='line-height: 1.2em; padding: 0px 5px; font-family: "josefin sans"; font-size: 0.65rem;'><span style='background-color: rgb(255, 255, 0); line-height: 1.2em; font-family: "josefin sans"; font-size: 0.65rem;'>IMMUNITY BOOSTER PACKAGE:</span></span> </span></strong><span style='line-height: 1.2em; font-size: 0.8rem; font-family: "josefin slab";'>This 3-in-1 combo has essential supplements that are powerful as anti-inflammatory agents to boost your immune system, fight against super viruses, and promote overall health.</span></p>-->

                        <!--			<ul>-->
                        <!--			</ul>-->
                        <!--		</div>-->
                        <!--	</div>-->
                        <!--	<div data-component="button" id="xXHR4Nx1Q5">-->
                        <!--		<a href="/immunitypackage" class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink" data-frame-id="_19z3ryo4f" style="background-color: rgb(243, 113, 33); color: rgb(252, 255, 251); margin: 0px auto 20px; font-weight: 400; font-family: roboto;" data-project-id="3" data-page-id="203" target="_parent">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right" style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 400;"></span></a>-->
                        <!--	</div>-->

                        <!--</div>-->

                        <!--</div>-->
                        <!--</div>-->
                        <!--</div>-->
                        <!--<div class="col-md-4">-->
                        <!--	<div class="js_kartra_component_holder">-->
                        <!--		<div class="kartra_element_bg kartra_element_bg--box-one kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="KY5DdcEHMm" style="margin: 0px; padding: 0px;">-->
                        <!--			<div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 0px; border-style: none; padding: 30px 50px 10px; background-image: none; opacity: 1; border-color: rgb(51, 51, 51);" class="background-item background-item--sp-45-border-top background_changer--blur0"></div>-->
                        <!--			<div data-component="image" href="javascript: void(0);"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=9cE8vVbU62Yf" class="toggle_pagelink" data-frame-id="_19z3ryo4f" data-project-id="3" data-page-id="1" target="_parent"><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; opacity: 1; width: 190px; max-width: 100%; height: auto; border-radius: 0px;" alt="" data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/15462837324UltraGreen_Superfood.png"></a></div>-->
                        <!--			<div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="8DIiSM1LGm_RPfeoRgwVM" style="margin-top: 0px; margin-bottom: 0px; padding: 0px 15px;">-->
                        <!--				<div style="border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1; background-color: rgba(0, 0, 0, 0);" class="background-item background_changer--blur0"></div>-->
                        <!--				<div data-component="text" id="w6pcKF9UNC">-->
                        <!--					<div class="kartra_text kartra_text--large kartra_text--dim-black kartra_text--roboto-font kartra_text--font-weight-regular" style="position: relative;" aria-controls="cke_2892" aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">-->
                        <!--						<p style="font-size: 0.8rem; line-height: 1.2em;"><strong><span style="font-size: 0.8rem; line-height: 1.2em;"><span style='line-height: 1.2em; padding: 0px 5px; font-family: "josefin sans"; font-size: 0.65rem;'><span style='background-color: rgb(255, 255, 0); line-height: 1.2em; font-family: "josefin sans"; font-size: 0.65rem;'>ULTRA GREENS:</span></span> </span></strong><span style='line-height: 1.2em; font-size: 0.8rem; font-family: "josefin slab";'>Comprehensive and concentrated super greens filled with vitamins A and C, calcium, ion, protein and fiber. (<em>Soy-Free</em>) </span></p>-->
                        <!--					</div>-->
                        <!--				</div>-->

                        <!--			</div>-->
                        <!--			<div data-component="button" id="fJ3srJ5A1U">-->
                        <!--				<a href="/ultragreens" class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink" data-frame-id="_19z3ryo4f" style="background-color: rgb(243, 113, 33); color: rgb(252, 255, 251); margin: 0px auto 20px; font-weight: 700; font-family: roboto;" data-project-id="3" data-page-id="1" target="_parent">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right" style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>-->
                        <!--			</div>-->

                        <!--		</div>-->
                        <!--	</div>-->
                        <!--</div>-->
                        <!--<div class="col-md-4 background_changer--blur0" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px 60px; background-image: none; opacity: 1;">-->
                        <!--	<div class="js_kartra_component_holder">-->
                        <!--		<div class="kartra_element_bg kartra_element_bg--box-one kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="2aBrYo5G0x_JHt9BLnrGb" style="margin: 0px; padding: 0px;">-->
                        <!--			<div style="background-color: rgb(255, 255, 255); border-radius: 0px; border-width: 0px; border-style: none; padding: 30px 50px 10px; background-image: none; opacity: 1; border-color: rgb(51, 51, 51);" class="background-item background-item--sp-45-border-top background_changer--blur0"></div>-->
                        <!--			<div data-component="image" href="javascript: void(0);" id="p5nvs9je0R"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=xvJ5ljwLNnoQ" class="toggle_pagelink" data-frame-id="_19z3ryo4f" data-project-id="3" data-page-id="187" target="_parent"><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" style="margin: 0px auto 20px; opacity: 1; width: 190px; max-width: 100%; height: auto; border-radius: 0px;" alt="Balanced Meal" data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/12044585593Vanella_Plant_Protein-removebg-preview.png"></a></div>-->

                        <!--			<div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--padding-bottom-tiny kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder" data-component="bundle" id="FxQQzPZ2sV_iZCASiww6U" style="margin-top: 0px; margin-bottom: 0px; padding: 0px 15px;">-->
                        <!--				<div style="border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1; background-color: rgba(0, 0, 0, 0);" class="background-item background_changer--blur0"></div>-->
                        <!--				<div data-component="text" id="w6pcKF9UNC">-->
                        <!--					<div class="kartra_text kartra_text--large kartra_text--dim-black kartra_text--roboto-font kartra_text--font-weight-regular" style="position: relative;" aria-controls="cke_4263" aria-activedescendant="" aria-autocomplete="list" aria-expanded="false">-->
                        <!--						<p style="font-size: 0.8rem; line-height: 1.2em;"><strong><span style="font-size: 0.8rem; line-height: 1.2em;"><span style='line-height: 1.2em; padding: 0px 5px; font-family: "josefin sans"; font-size: 0.65rem; color: rgb(0, 0, 0);'><span style='background-color: rgb(255, 255, 0); line-height: 1.2em; font-family: "josefin sans"; font-size: 0.65rem; color: rgb(0, 0, 0);'>PLANT PROTEIN SHAKE:</span></span> </span></strong><span style="font-family:Josefin Slab;">It comes in vanilla or chocolate, medium or large, AND with <span style="font-family:Josefin Slab;">21 grams of protein per serving. <span style="font-family:Josefin Slab;">Non-GMO. No Gluten, Soy, or Dairy.</span></span></span></p>-->
                        <!--					</div>-->
                        <!--				</div>-->

                        <!--			</div>-->
                        <!--			<div data-component="button" id="K37XBm1Cgy">-->
                        <!--				<a href="/plant" class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--medium kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink" data-frame-id="_19z3ryo4f" style="background-color: rgb(243, 113, 33); color: rgb(252, 255, 251); margin: 0px auto 20px; font-weight: 700; font-family: Roboto; border-radius: 40px;" data-project-id="3" data-page-id="202" target="_parent">LEARN MORE<span class="kartra_icon__icon fa fa-chevron-right" style="color: rgb(49, 85, 40); border-color: rgb(49, 85, 40); font-weight: 700;"></span></a>-->
                        <!--			</div>-->

                        <!--		</div>-->
                        <!--	</div>-->
                        <!--</div>-->
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-12"></div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-medium" style="padding: 20px 0px; background-color: rgb(48, 85, 40);"
                id="_lr8ozdxgy">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row background_changer--blur0" data-component="grid"
                        style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 10px; background-image: none; opacity: 1;">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="4RL0wEJEbn">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 0px;">
                                        <p style="text-align: center; font-size: 1.8rem;">
                                            <font color="#ffffff"><span style="font-size: 1.8rem;"><b><span
                                                            style="font-size: 1.8rem;"><span
                                                                style="font-size: 2rem; font-family: roboto;">Are You
                                                                Ready to Unlock the Secret Pillars to Vibrant Health and
                                                                Longevity?</span> </span></b></span></font>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="text" id="MkGZjA3ohu">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 10px; margin-bottom: 20px;">
                                        <p style="text-align: center; line-height: 1.6em; font-size: 1rem;">
                                            <font face="roboto"><span
                                                    style="color: rgb(255, 165, 0); line-height: 1.6em; font-size: 1rem;"><i><span
                                                            style="color: rgb(255, 165, 0); line-height: 1.6em; font-size: 1rem;">"I
                                                            help people just like to you to get healthy for life! So you
                                                            live a longer, stronger, and energetic
                                                            life."</span></i></span></font>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-7">
                            <div class="js_kartra_component_holder">
                                <div data-component="video">
                                    <div class="kartra_video kartra_video--player_1"
                                        style="margin-top: 0px; margin-bottom: 20px; padding-bottom: 56.25%;">
                                        <iframe src="https://app.kartra.com/external_video/youtube/9Au_xsUi5No"
                                            scrolling="no" allowfullscreen="true" data-video-type="youtube"
                                            data-video="9Au_xsUi5No" width="100%" frameborder="0"></iframe>

                                        <div class="kartra_video_player_shadow"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 column--vertical-center background_changer--blur0"
                            style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
                            <div class="js_kartra_component_holder">
                                <div data-component="text" id="gQ3QYap9Ns">
                                    <div class="kartra_text"
                                        style="position: relative; margin-top: 0px; margin-bottom: 10px;">
                                        <p style="text-align: center; line-height: 1.4em; font-size: 0.8rem;">
                                            <font color="#ffffff"><span
                                                    style="font-size: 0.8rem; line-height: 1.4em;">Sign up to
                                                    <strong><span style="font-size: 0.8rem; line-height: 1.4em;">unlock
                                                            the first secret </span></strong><span
                                                        style="font-size: 0.8rem; line-height: 1.4em;">now and receive
                                                        our FREE Nutritional Guide.</span> </span></font>
                                        </p>
                                    </div>
                                </div>
                                <div data-component="optin" id="OTFwEPxswT">
                                    <div class="optin_block_form_67 leads_capture">
                                        <div class="leads-form kartra_page_optin_form">
                                            <div
                                                class="kartra_optin_wrapper form_class_okG1JvM29iZa form_class_hnFSiVQOXcoQ form_class_NaHAofC2eYZa">
                                                <form action="https://app.kartra.com//process/add_lead/NaHAofC2eYZa"
                                                    data-input-class="kartra_optin_input_medium"
                                                    data-submit-bg="rgb(243, 113, 33)"
                                                    data-submit-color="rgb(255, 255, 255)" data-submit-type="Solid"
                                                    data-submit-bold="700" data-submit-size="kartra_btn_medium"
                                                    data-submit-corners="Rounded"
                                                    class="filled_optin unique_class__9851ashgc form_class_okG1JvM29iZa js_kartra_trackable_object form_class_hnFSiVQOXcoQ form_class_NaHAofC2eYZa"
                                                    data-optin-id="NaHAofC2eYZa" data-domain="https://app.kartra.com/"
                                                    data-field-style="line" data-field-bg="rgb(243, 243, 243)"
                                                    data-field-color="rgb(255, 255, 255)"
                                                    data-text-color="rgb(251, 251, 251)" data-display-icons="true"
                                                    data-submit-text="YES! Send Me The Secrets Today!"
                                                    data-submit-shadow="btn_shadow_large" target="_top" method="POST"
                                                    style="margin-top: 0px; margin-bottom: 0px;" data-kt-type="optin"
                                                    data-kt-value="NaHAofC2eYZa" data-kt-owner="DpwDQa6g"
                                                    data-asset-id="0">
                                                    <div class="kartra_optin_r">
                                                        <style>
                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__9851ashgc .kartra_optin_tnc-form button.btn.dropdown-toggle,
                                                            div[class*="leads_capture"] .kartra_page_optin_form .unique_class__9851ashgc .kartra_optin_cg button.btn.dropdown-toggle {
                                                                border-bottom-color: rgb(243, 243, 243);
                                                                color: rgb(255, 255, 255) !important;
                                                            }

                                                            .unique_class__9851ashgc .kartra_optin_i {
                                                                color: rgb(255, 255, 255) !important;
                                                            }

                                                            .unique_class__9851ashgc .kartra_optin_clabel {
                                                                color: rgb(251, 251, 251) !important;
                                                            }

                                                            .unique_class__9851ashgc ::-webkit-input-placeholder {
                                                                color: rgb(255, 255, 255) !important;
                                                                opacity: 0.7;
                                                            }

                                                            .unique_class__9851ashgc ::-moz-placeholder {
                                                                color: rgb(255, 255, 255) !important;
                                                                opacity: 0.7;
                                                            }

                                                            .unique_class__9851ashgc :-ms-input-placeholder {
                                                                color: rgb(255, 255, 255) !important;
                                                                opacity: 0.7;
                                                            }

                                                            .unique_class__9851ashgc :-moz-placeholder {
                                                                color: rgb(255, 255, 255) !important;
                                                                opacity: 0.7;
                                                            }

                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__9851ashgc input[type=radio]+small,
                                                            div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__9851ashgc input[type=checkbox]+small {
                                                                border-color: rgb(243, 243, 243);
                                                                background-color: transparent;
                                                            }
                                                        </style>
                                                        <div class="kartra_optin_c1">
                                                            <div class="kartra_optin_cg">
                                                                <div
                                                                    class="kartra_optin_controls kartra_optin_input_medium kartra_optin_input_bottom_border kartra_optin_icon">
                                                                    <i class="kartra_optin_i kartraico-person"
                                                                        style="color: rgb(255, 255, 255);"></i>
                                                                    <div class="kartra_optin_asterisk"></div>
                                                                    <input type="text" placeholder="First name"
                                                                        class="required_NaHAofC2eYZa js_kartra_santitation kartra_optin_ti"
                                                                        name="first_name"
                                                                        data-santitation-type="front_name"
                                                                        style="border-bottom-color: rgb(243, 243, 243); color: rgb(255, 255, 255);">
                                                                </div>
                                                            </div>
                                                            <div class="kartra_optin_cg">
                                                                <div
                                                                    class="kartra_optin_controls kartra_optin_input_medium kartra_optin_input_bottom_border kartra_optin_icon">
                                                                    <i class="kartra_optin_i kartraico-email"
                                                                        style="color: rgb(255, 255, 255);"></i>
                                                                    <div class="kartra_optin_asterisk"></div>
                                                                    <input type="text" placeholder="Email"
                                                                        class="required_NaHAofC2eYZa js_kartra_santitation kartra_optin_ti"
                                                                        name="email" data-santitation-type="email"
                                                                        style="border-bottom-color: rgb(243, 243, 243); color: rgb(255, 255, 255);">
                                                                </div>
                                                            </div>
                                                            <div class="kartra_optin_cg">
                                                                <div class="js_gdpr_wrapper clearfix kartra_optin_gdpr_wrppr"
                                                                    style="">
                                                                    <div
                                                                        class="gdpr_communications js_gdpr_communications kartra_optin_cg kartra_optin_gdpr_terms">
                                                                        <div class="kartra-optin-checkbox">
                                                                            <label
                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                <input name="gdpr_communications"
                                                                                    type="checkbox"
                                                                                    class="js_gdpr_communications_check"
                                                                                    value="1">

                                                                                <small></small>


                                                                                <span
                                                                                    class="js_gdpr_label_communications"
                                                                                    style="color: rgb(251, 251, 251);">I
                                                                                    would like to receive future
                                                                                    communications</span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="gdpr_terms js_gdpr_terms kartra_optin_cg kartra_optin_gdpr_terms">
                                                                        <div class="kartra-optin-checkbox">
                                                                            <label
                                                                                class="kartra_optin_field-label kartra-optin-checkbox">
                                                                                <input name="gdpr_terms" type="checkbox"
                                                                                    class="js_gdpr_terms_check"
                                                                                    value="1">

                                                                                <small></small>


                                                                                <span class="js_gdpr_label_terms"
                                                                                    style="color: rgb(251, 251, 251);">I
                                                                                    agree to the GDPR Terms &amp;
                                                                                    Conditions</span><!--
                --><button type="button" class="kartra_gdpr_popover_button js_gdpr_button_popover_trigger js_theme_border">
                                                                                    <i class="kartraico-info_letter js_kartra_popover_trigger js_kartra_popover_gdpr_trigger"
                                                                                        data-popover="js_kartra_gdpr_popover"
                                                                                        style="color: rgb(251, 251, 251);"></i>
                                                                                </button>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button type="submit"
                                                                    class="kartra_optin_submit_btn kartra_optin_btn_block kartra_optin_btn_medium submit_button_NaHAofC2eYZa btn-rounded btn_shadow_large"
                                                                    style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); font-weight: 700;"
                                                                    disabled>YES! Send Me The Secrets Today!</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="referrer"
                                                        value="https://app.kartra.com/pages/sites/getframe/37844"><input
                                                        type="hidden" name="kuid"
                                                        value="84fc69f3-7a42-4c64-bac1-4adc22af796e">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="text" id="tGC1fZYUH5">
                                    <div class="kartra_text kartra_text--font-weight-regular kartra_text--text-small kartra_text--nunito-font kartra_text--light-black"
                                        style="position: relative;">
                                        <p style="text-align: center;">
                                            <font color="#d3d3d3"><b>2:18 mins Video shows the kind of life you can have
                                                    starting now. </b></font>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content content--padding-large"
                style="background-color: rgb(255, 255, 255); padding: 0px 0px 55px;" id="_nhsk0gout">
                <div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div class="container">
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="image" href="javascript: void(0);" id="z2sLPuvY4J">
                                    <img class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="uJTkTbdZnD">
                                    <img class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="button" id="46DfndPT5T"><a href="courses"
                                        class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                        style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                        data-frame-id="_nhsk0gout" data-project-id="3" data-page-id="191"
                                        target="_parent">COURSES BY DR. DONA COOPER</a></div>
                                <div data-component="carousel" id="0vGbZj9XUL">
                                    <div class="carousel-wrapper" style="margin: 0px auto 20px;">
                                        <div class="carousel slide kartra_carousel" data-selector="kartra_carousel"
                                            data-ride="carousel" data-interval="0"
                                            style="margin-left: auto; margin-right: auto; border-radius: 0px;"
                                            id="wmN1iitISf_Jx9auhuwUU">
                                            <ol class="carousel-indicators">
                                                <li data-target="#wmN1iitISf_Jx9auhuwUU" data-slide-to="0"
                                                    class="active" style="margin-right:4px">
                                                </li>
                                                <li data-target="#wmN1iitISf_Jx9auhuwUU" data-slide-to="1" class=""
                                                    style="margin-right:4px">
                                                </li>
                                                <li data-target="#wmN1iitISf_Jx9auhuwUU" data-slide-to="2" class=""
                                                    style="margin-right:4px">
                                                </li>
                                            </ol>
                                            <div class="carousel-inner" style="border-radius: inherit;">
                                                <div class="item active js-bg-next-gen" style=""
                                                    data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9319516_1587509248039Transformation_to_healthier_you_course_banner.png')">
                                                    <div class="carousel-caption">
                                                        <h3>Transformation to a Healthier You</h3>
                                                        <p></p>
                                                    </div>
                                                </div>
                                                <div class="item js-bg-next-gen" style=""
                                                    data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/30996546_642619c6df5b4_14D2AH-Course-Banner-640x640.png')">
                                                    <div class="carousel-caption">
                                                        <h3>14 Days to Amazing Health Course</h3>
                                                        <p></p>
                                                    </div>
                                                </div>
                                                <div class="item" style=""
                                                    data-bg="url('https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/270608314774woman_kneeling_beside_man.jpg')">
                                                    <div class="carousel-caption">
                                                        <h3>Become a Certified Health Coach</h3>
                                                        <p></p>
                                                    </div>
                                                </div>
                                            </div> <a class="left carousel-control" href="#wmN1iitISf_Jx9auhuwUU"
                                                role="button" data-slide="prev"
                                                style="border-top-left-radius: inherit; border-bottom-left-radius: inherit;"
                                                data-frame-id="_nhsk0gout" target="_parent"> <span
                                                    class="glyphicon-chevron-left fa fa-arrow-left"
                                                    aria-hidden="true"></span> <span class="sr-only">Previous</span></a>
                                            <a class="right carousel-control" href="#wmN1iitISf_Jx9auhuwUU"
                                                role="button" data-slide="next"
                                                style="border-top-right-radius: inherit; border-bottom-right-radius: inherit;"
                                                data-frame-id="_nhsk0gout" target="_parent"> <span
                                                    class="glyphicon-chevron-right fa fa-arrow-right"
                                                    aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
                                        </div>
                                    </div>
                                </div>
                                <div data-component="image" href="javascript: void(0);" id="3sA5rayGqg">
                                    <img class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid" id="EKOkHWDrKc">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="js_kartra_component_holder">

                                <div class="kartra_element_bg kartra_element_bg--padding-top-bottom-tiny kartra_element_bg--padding-left-right-extra-small pull-center kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder js_kartra_component_holder--height-auto"
                                    data-component="bundle" id="oaQvL1zSqs_ugBOvCUEYi_eLOI7cUkZ5_XcSZ5FMnxw"
                                    style="margin-top: 0px; margin-bottom: 30px; padding: 20px 30px 13px;">
                                    <div style="border-radius: 10px; border-color: rgb(49, 85, 40); border-style: double; border-width: 4px; background-image: none; opacity: 1; background-color: rgba(0, 0, 0, 0);"
                                        class="background-item background-item--border-style-double background_changer--blur0">
                                    </div>
                                    <div data-component="headline" id="ABy7uhnytV">
                                        <div class="kartra_headline kartra_headline--size-m-giant kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-big-tiny"
                                            style="position: relative;" aria-controls="cke_55" aria-activedescendant=""
                                            aria-autocomplete="list" aria-expanded="false">
                                            <p style="line-height: 1.2em; font-size: 1.6rem;">
                                                <font color="#c73312" face="roboto"><b>IMPACT TESTIMONIALS</b></font>
                                            </p>
                                        </div>
                                    </div>
                                </div>




                            </div>
                        </div>
                    </div>
                    <div class="row row--equal" data-component="grid">
                        <div class="col-md-4 column--sm-margin-bottom-small column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--border-black-transparent-near-grey-medium column--margin-bottom-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: #eeeeee"
                                        class="background-item background-item--overlap background-item--rounded-tiny">
                                        <span class="background-item__arrow background-item__arrow--left-arrow-down"
                                            style="left: 30px;"></span>
                                    </div>
                                    <div data-component="text">
                                        <div class="kartra_text kartra_text--roboto-font kartra_text--light-grey kartra_text--overlap kartra_text--quotes-medium-right kartra_text--padding-right-small kartra_text--margin-bottom-none"
                                            style="position: relative;">
                                            <p><em>“Dr. Cooper-Dockery has brilliantly crafted a tool that you can
                                                    use for yourself, your family, and your institution, you could live
                                                    smarter, longer, healthier, happier, and save a bundle of
                                                    money.</em></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="kartra_testimonial_author_block kartra_testimonial_author_block--flex kartra_testimonial_author_block--vertical-center"
                                    data-component="bundle">
                                    <div class="kartra_testimonial_author_block__image">
                                        <div class="kartra_element_bg kartra_element_bg--thumb-size-medium kartra_element_bg--align-left"
                                            data-component="bundle"
                                            style="margin-top: 0px; margin-bottom: 0px; padding: 0px;">
                                            <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                                class="background-item background_changer--blur0 js-bg-next-gen"
                                                data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195131_1587092361176Dr.-Errol-Bryce.jpeg")'>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="kartra_testimonial_author_block__info kartra_testimonial_author_block__info--padding-left-extra-small kartra_testimonial_author_block__info--adjust-width js_kartra_component_holder">
                                        <div data-component="headline" id="2mgLhKm0NQ">
                                            <div class="kartra_headline kartra_headline--sm-text-center kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--dim-black kartra_headline--margin-bottom-extra-tiny"
                                                style="position: relative;">
                                                <p>Errol B. Bryce, MD, FACP</p>
                                            </div>
                                        </div>
                                        <div data-component="text" id="skzq7uTE0R">
                                            <div class="kartra_text kartra_text--sm-text-center kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--margin-bottom-extra-medium"
                                                style="position: relative;">
                                                <p>Adj. Asst. Professor of Medicine, UNT HSC</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--sm-margin-bottom-small column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--border-black-transparent-near-grey-medium column--margin-bottom-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: #eeeeee"
                                        class="background-item background-item--overlap background-item--rounded-tiny">
                                        <span class="background-item__arrow background-item__arrow--left-arrow-down"
                                            style="left: 30px;"></span>
                                    </div>
                                    <div data-component="text" id="X51sPLSEIq">
                                        <div class="kartra_text kartra_text--text-medium kartra_text--open-sans-font kartra_text--light-grey kartra_text--overlap kartra_text--quotes-medium-right kartra_text--font-weight-medium kartra_text--padding-right-small"
                                            style="position: relative;">
                                            <p style="font-size: 0.65rem;"><em><span style="font-size: 0.65rem;">"I came
                                                        to Dr. Cooper because I had diabetes. Now I'm off my medication,
                                                        continue to do exercise, and follow the diet. If you follow her
                                                        directions, everything will be OK.</span></em></p>
                                        </div>
                                    </div>

                                </div>
                                <div class="kartra_testimonial_author_block kartra_testimonial_author_block--flex kartra_testimonial_author_block--vertical-center"
                                    data-component="bundle">
                                    <div class="kartra_testimonial_author_block__image">
                                        <div class="kartra_element_bg kartra_element_bg--thumb-size-medium kartra_element_bg--align-left"
                                            data-component="bundle"
                                            style="margin-top: 0px; margin-bottom: 0px; padding: 0px;">
                                            <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                                class="background-item background_changer--blur0 js-bg-next-gen"
                                                data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198664_1587106431296Ana_Garcia.jpg")'>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="kartra_testimonial_author_block__info kartra_testimonial_author_block__info--padding-left-extra-small kartra_testimonial_author_block__info--adjust-width js_kartra_component_holder">
                                        <div data-component="headline" id="6Fl1XyyIrb">
                                            <div class="kartra_headline kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--text-left kartra_headline--dim-black kartra_headline--font-weight-semi-bold kartra_headline--margin-bottom-extra-tiny"
                                                style="position: relative;">
                                                <p>Anna Garcia</p>
                                            </div>
                                        </div>
                                        <div data-component="text" id="SLc3RifKEB">
                                            <div class="kartra_text kartra_text--open-sans-font kartra_text--text-left kartra_text--letter-spacing-extra-tiny kartra_text--dim-grey kartra_text--font-weight-medium kartra_text--text-small kartra_text--margin-bottom-none"
                                                style="position: relative;">
                                                <p>Satisfied Patient</p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 column--sm-margin-bottom-small column--vertical-center">
                            <div class="js_kartra_component_holder">
                                <div class="kartra_element_bg kartra_element_bg--padding-small kartra_element_bg--border-black-transparent-near-grey-medium column--margin-bottom-small js_kartra_component_holder"
                                    data-component="bundle">
                                    <div style="background-color: #eeeeee"
                                        class="background-item background-item--overlap background-item--rounded-tiny">
                                        <span class="background-item__arrow background-item__arrow--left-arrow-down"
                                            style="left: 30px;"></span>
                                    </div>
                                    <div data-component="text" id="RQ2Pv4yVUI">
                                        <div class="kartra_text kartra_text--roboto-font kartra_text--light-grey kartra_text--overlap kartra_text--quotes-medium-right kartra_text--padding-right-small kartra_text--margin-bottom-none"
                                            style="position: relative;">
                                            <p><em>“</em><i>As a family physician, I acknowledge the importance of
                                                    delivering health information in a very simple way. To get healthy
                                                    does not mean to navigate a complex medical system, or to use the
                                                    most recent medications as many times portrayed in the media. To be
                                                    healthy consists in following practical steps as presented by Dr.
                                                    Dona Cooper-Dockery. I strongly encourage everybody to engage in
                                                    this program. Hope you enjoy it, and concurrently get healthy.”</i>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="kartra_testimonial_author_block kartra_testimonial_author_block--flex kartra_testimonial_author_block--vertical-center"
                                    data-component="bundle">
                                    <div class="kartra_testimonial_author_block__image">
                                        <div class="kartra_element_bg kartra_element_bg--thumb-size-medium kartra_element_bg--align-left"
                                            data-component="bundle"
                                            style="margin-top: 0px; margin-bottom: 0px; padding: 0px;">
                                            <div style="border-radius: 50px; background-color: rgba(0, 0, 0, 0); border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;"
                                                class="background-item background_changer--blur0 js-bg-next-gen"
                                                data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9195076_1587092166346Salazar-Alamillo-Ubaldo-M.D.-217x300.jpg")'>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="kartra_testimonial_author_block__info kartra_testimonial_author_block__info--padding-left-extra-small kartra_testimonial_author_block__info--adjust-width js_kartra_component_holder">
                                        <div data-component="headline" id="7YZvGOVpDF">
                                            <div class="kartra_headline kartra_headline--sm-text-center kartra_headline--open-sans-font kartra_headline--h5 kartra_headline--font-weight-semi-bold kartra_headline--dim-black kartra_headline--margin-bottom-extra-tiny"
                                                style="position: relative;">
                                                <p>Ubaldo Salazar, MD.</p>
                                            </div>
                                        </div>
                                        <div data-component="text" id="RdLIrpddG9">
                                            <div class="kartra_text kartra_text--sm-text-center kartra_text--open-sans-font kartra_text--text-small kartra_text--font-weight-medium kartra_text--dim-grey kartra_text--margin-bottom-extra-medium"
                                                style="position: relative;">
                                                <p>Family Practice Physician</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" data-component="grid">
                        <div class="col-md-12">
                            <div class="js_kartra_component_holder">
                                <div data-component="image" href="javascript: void(0);" id="I0yhjOncw2">
                                    <img class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 40px; opacity: 1;"
                                        data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-divider-5.png">
                                </div>
                                <div data-component="button" id="0KPARvEcQ8"><a href="othersites"
                                        class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                        style="border-color: rgb(255, 255, 255); background-color: rgb(48, 85, 40); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                        data-frame-id="_nhsk0gout" data-project-id="3" data-page-id="192"
                                        target="_parent">OUR OTHER WEBSITES</a></div>
                                <div data-component="image" href="javascript: void(0);">
                                    <picture>
                                        <source type="image/webp"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/19724583_1621533056VlGSalmon_Arts_and_Crafts_Online_Store_Website_7.webp">
                                        </source>
                                        <source type="image/png"
                                            data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/19724583_1621533056VlGSalmon_Arts_and_Crafts_Online_Store_Website_7.png">
                                        </source><img
                                            class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                            src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                            onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                            alt=""
                                            style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; border-radius: 0px; opacity: 1;"
                                            data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/19724583_1621533056VlGSalmon_Arts_and_Crafts_Online_Store_Website_7.png">
                                    </picture>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content" style="padding: 40px 0px 0px; background-color: rgba(255, 255, 255, 0.47);"
                id="_wxiqk1js8">
                <div class="background_changer background_changer--blur0" style="opacity: 1; background-image: none;">
                </div>
                <div class="background_changer_overlay" style="background-image: none;"></div>
                <div>
                    <div>
                        <div class="container">
                            <div class="row background_changer--blur0" data-component="grid"
                                style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 30px; background-image: none; opacity: 1;">
                                <div class="col-md-12">
                                    <div class="js_kartra_component_holder">
                                        <div data-component="button" id="6sZSaND8HH"><a
                                                href="https://www.faithfulpathinternational.org/"
                                                class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                                style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                                data-frame-id="_wxiqk1js8" target="_blank">FAITHFUL PATH INTERNATIONAL
                                                ORGANIZATION</a></div>
                                        <div data-component="image" href="javascript: void(0);" id="nchYxN5Tr6"><a
                                                href="https://www.faithfulpathinternational.org/"
                                                class="toggle_pagelink" target="_blank" data-frame-id="_wxiqk1js8">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710904_1588801298610FPIM-site1.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710904_1588801298610FPIM-site1.jpg">
                                                    </source><img
                                                        class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                                        alt=""
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; border-radius: 0px; opacity: 1;"
                                                        data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710904_1588801298610FPIM-site1.jpg">
                                                </picture>
                                            </a></div>
                                        <div data-component="button" id="Wt1LOHGWol"><a
                                                href="https://www.cooperinternalmedicine.com/"
                                                class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                                style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                                data-frame-id="_wxiqk1js8" target="_blank">COOPER INTERNAL MEDICINE</a>
                                        </div>
                                        <div data-component="image" href="javascript: void(0);" id="hqvbJ2cDTn"><a
                                                href="https://www.cooperinternalmedicine.com/" class="toggle_pagelink"
                                                target="_blank" data-frame-id="_wxiqk1js8">
                                                <picture>
                                                    <source type="image/webp"
                                                        data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710324_1588799627202CIM-site1.webp">
                                                    </source>
                                                    <source type="image/jpeg"
                                                        data-srcset="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710324_1588799627202CIM-site1.jpg">
                                                    </source><img
                                                        class="kartra_image kartra_image--full pull-center background_changer--blur0"
                                                        src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs="
                                                        onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';"
                                                        alt=""
                                                        style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; border-radius: 0px; opacity: 1;"
                                                        data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/9710324_1588799627202CIM-site1.jpg">
                                                </picture>
                                            </a></div>
                                        <div data-component="button" id="Wt1LOHGWol"><a onclick="openDoctorMeetingModal(event)"
                                                class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink"
                                                style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;"
                                                data-frame-id="_wxiqk1js8" target="_blank">BOOK A VIRTUAL
                                                APPOINTMENT</a></div>
                                        <!--<div data-component="button" id="Wt1LOHGWol"><a href="https://www.foodamed.com/" class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink" style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;" data-frame-id="_wxiqk1js8" target="_blank">FOODAMED VEGAN RESTAURANT</a></div>-->
                                        <!--<div data-component="image" href="javascript: void(0);" id="DktwDVob6l"><a href="https://www.foodamed.com/" class="toggle_pagelink" target="_blank" data-frame-id="_wxiqk1js8"><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='//d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" alt="" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; border-radius: 0px; opacity: 1;" data-original="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/1106499583313FoodAMed-Website.JPG"></a></div>-->
                                        <!--<div data-component="button" id="Wt1LOHGWol"><a href="https://www.foodamed.com/product-category/meal-plans/" class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink" style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;" data-frame-id="_wxiqk1js8" target="_blank">WEIGHT LOSS MEAL PLANS</a></div>-->
                                        <!--<div data-component="button" id="P0yf38txsw"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=pByILe3giEoQ" class="kartra_button1 kartra_button1--default kartra_button1--solid kartra_button1--full-width kartra_button1--squared kartra_button1--shadow-large pull-center toggle_pagelink" style="border-color: rgb(255, 255, 255); background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; border-radius: 6px; font-weight: 700; font-family: Lato;" data-frame-id="_wxiqk1js8" data-project-id="3" data-page-id="201" target="_parent">WELLNESS PROGRAMS</a></div>-->
                                        <!--<div data-component="image" href="javascript: void(0);" id="tmyrrCH11V">-->

                                    </div>
                                    <div data-component="text" id="9sPYWjqaz6">
                                        <div class="kartra_text"
                                            style="position: relative; margin-top: 15px; margin-bottom: 30px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--<div class="content" style="padding: 0px 0px 30px; background-color: rgb(243, 113, 33);" id="_muyzel5sm">-->
        <!--<div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>-->
        <!--<div class="background_changer_overlay" style="background-image: none;"></div>-->
        <!--<div>-->
        <!--	<div class="row row--equal row--margin-left-right-none" data-component="grid">-->
        <!--		<div class="col-md-12 column--padding-top-bottom-extra-large background_changer--blur0" style="margin-top: 0px; margin-bottom: 0px; padding: 0px; background-color: rgb(255, 255, 255); border-radius: 0px; border-style: none; border-width: 0px; background-image: none; opacity: 1;">-->
        <!--			<div class="js_kartra_component_holder js_kartra_component_holder--min-height-auto">-->
        <!--				<div class="kartra_element_bg kartra_element_bg--padding-top-bottom-tiny kartra_element_bg--padding-left-right-extra-small pull-center kartra_element_bg--margin-bottom-extra-small js_kartra_component_holder js_kartra_component_holder--height-auto" data-component="bundle" id="vq5ZYiN9H2_5qWkDX8yZX" style="margin-top: 5px; margin-bottom: 30px; padding: 20px 30px 13px;">-->
        <!--					<div style="border-radius: 10px; border-color: rgba(255, 255, 255, 0.2); border-style: double; border-width: 3px; background-image: none; opacity: 1; background-color: rgba(255, 255, 255, 0);" class="background-item background-item--border-style-double background_changer--blur0"></div>-->
        <!--				</div>-->
        <!--				<div class="kartra_element_wrapper kartra_element_wrapper--mockup kartra_element_wrapper--three-ipad-mockup" data-component="bundle" id="PDai61hGBe_nq9WTGJWhg" style="margin-top: 25px; margin-bottom: 0px; padding: 0px;">-->
        <!--					<div style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;" class="background-item background-item--first-ipad background_changer--blur0 js-bg-next-gen" data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200734_1587117751093GHClub-pkg-1024x683.jpg")'></div>-->
        <!--					<div style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;" class="background-item background-item---second-ipad background_changer--blur0 js-bg-next-gen" data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9314254_1587496099176Juices_CWC.jpg")'></div>-->
        <!--					<div style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; opacity: 1;" class="background-item background-item--third-ipad background_changer--blur0 js-bg-next-gen" data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9200813_1587117966140DrCooper-9.jpg")'></div>-->
        <!--					<img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" class="joint_device_mock_up--frame" data-original="//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-component/kp-three-ipad.png">-->
        <!--				</div>-->
        <!--			</div>-->
        <!--		</div>-->
        <!--	</div>-->
        <!--</div>-->
        <!--    <div class="container">-->
        <!--	    <div class="row" data-component="grid" id="Bt0V0mjGpO">-->
        <!--		    <div class="col-md-12">-->
        <!--			    <div class="js_kartra_component_holder">-->
        <!--                                <div data-component="button" id="rFuD8tkds2">-->
        <!--					    <a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=X7M6t4dB2vZa" class="kartra_button1 kartra_button1--roboto-font kartra_button1--white-bg-blue-text kartra_button1--font-weight-medium kartra_button1--icon-right kartra_button1--solid kartra_button1--full-width kartra_button1--rounded kartra_button1--shadow-small pull-center toggle_pagelink" data-frame-id="_muyzel5sm" style="background-color: rgb(49, 85, 40); color: rgb(252, 255, 251); margin: 25px auto 20px; font-weight: 700; font-family: Roboto; border-radius: 40px;" data-project-id="3" data-page-id="25" data-effect="kartra_css_effect_7" target="_parent">&gt;&gt; View Our Supplements Catalog Here &lt;&lt;<span class="kartra_icon__icon fa fa-shopping-cart" style="color: rgb(252, 253, 251); border-color: rgb(252, 253, 251); font-weight: 700;"></span></a>-->
        <!--								</div>-->
        <!-- 						</div>-->
        <!--  				</div>-->
        <!--   		</div>-->
        <!--					<div class="row row--equal" data-component="grid"></div>-->
        <!--  			<div class="row" data-component="grid"></div>-->
        <!--</div>-->
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
                                        href="about"
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
                                        href="othersites"
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
                                        href="books"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="181" target="_parent">Books</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="courses"
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
                                        href="programs/#accordion-kw63qIXFpj"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="261" target="_parent">8 Weeks to
                                        Wellness</a>
                                    <!--<a-->
                                    <!--    class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"-->
                                    <!--    href="https://cooperwellness.kartra.com/page/weightloss"-->
                                    <!--    data-frame-id="_6723f824ea44e"-->
                                    <!--    style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "Open Sans";'-->
                                    <!--    target="_blank">Weight Loss</a>-->
                                    <a
                                        class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
                                        href="programs"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "open sans";'
                                        data-project-id="3" data-page-id="201" target="_parent">Programs</a>



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
                                    <a href=""
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
                                        href="contact"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "open sans";'
                                        data-project-id="3" data-page-id="112" target="_parent">CONTACT US</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink"
                                        href="terms"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "open sans";'
                                        data-project-id="3" data-page-id="5" target="_parent">DISCLAIMERS</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink "
                                        href="privacypolicy"
                                        data-frame-id="_6723f824ea44e"
                                        style='color: rgba(255, 255, 255, 0.8); font-weight: 700; font-family: "Open Sans";'
                                        data-project-id="3" data-page-id="4" target="_parent">PRIVACY POLICY</a>
                                    <a class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-bold kartra_list__link--semi-pro-white kartra_list__link--padding-left-right-tiny kartra_list__link--hover-opacity-giant kartra_list__link--margin-bottom-extra-tiny toggle_pagelink"
                                        href="terms"
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
    <script src="//app.kartra.com/resources/js/page_check?page_id=6hMi8IQ4msrK" async defer></script>
    <script>
        if (typeof window['jQuery'] !== 'undefined') {
            window.jsVars = {
                "page_title": "Dr Cooper Health  and Disease Prevention Center",
                "page_description": "We are a leading Health and Healing center in  Texas, helps you to boost your immunity in Covid 19 days.  Book your Apointment @ 956-627-3106.",
                "page_keywords": "Wellness and Disease Prevention Center, Disease Prevention Center, Health and Healing Center, Boost Your Immunity in Covid",
                "page_robots": "index, follow",
                "secure_base_url": "\/\/app.kartra.com\/",
                "global_id": "6hMi8IQ4msrK"
            };
            window.global_id = '6hMi8IQ4msrK';
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
                                    data-type-id="111" data-type-owner="DpwDQa6g">
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