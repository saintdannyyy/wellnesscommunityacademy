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

$open_exchange_api_key = $_ENV['open_exchange_api_key'] ?? null;

if (!$open_exchange_api_key) {
	echo json_encode(['success' => false, 'message' => 'API key for Open Exchange Rates is missing.']);
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Get Healthy Lifestyle Courses in Texas - Dr Cooper</title>
	<meta name="description" content="Are you struggling with overweighting or low cholesterol? Take our healthy lifestyle courses by certified health coach Dr. Cooper. Enroll today.">
	<meta name="keywords" content="Get Healthy Lifestyle Courses">
	<meta name="robots" content="index, follow">
	<link rel="shortcut icon" href="//d2uolguxr56s4e.cloudfront.net/img/shared/favicon.ico">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="author" content="">
	<meta property="og:title" content="">
	<meta property="og:description" content="">
	<meta property="og:image" content="https://d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587358872.jpg">

	<!-- Font icons preconnect -->
	<link rel="preconnect" href="//fonts.gstatic.com" crossorigin>
	<link rel="preconnect" href="//fonts.googleapis.com" crossorigin>
	<link rel="preconnect" href="//d2uolguxr56s4e.cloudfront.net" crossorigin>

	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link rel="dns-prefetch" href="//fonts.googleapis.com">
	<link rel="dns-prefetch" href="//d2uolguxr56s4e.cloudfront.net">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
	<script src="https://js.paystack.co/v1/inline.js"></script>
	<!-- SweetAlert CDN -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



	<!--
        Google fonts are computed and loaded on page build via save.js
        Individual stylesheets required are listed in /css/pages/skeleton.css
    -->

	<!--<link href="cssskeleton.min.css" rel="stylesheet">-->
	<link type="text/css" rel="preload" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Dancing+Script:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Patua+One:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<link rel="stylesheet" href="css/new_bootstrap.css">

	<link rel="preload" href="css/kartra_components.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
	<link rel="preload" href="css/font-awesome.css" as="style" onload="this.onload=null;this.rel='stylesheet'">

	<noscript>
		<link rel="stylesheet" href="css/kartra_components.css">
		<link rel="stylesheet" href="css/font-awesome.css">
		<link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Lato:300,300i,400,400i,600,600i,700,700i,900,900i|Raleway:300,300i,400,400i,600,600i,700,700i,900,900i|Roboto+Condensed:300,300i,400,400i,600,600i,700,700i,900,900i|Dancing+Script:300,300i,400,400i,600,600i,700,700i,900,900i|Oswald:300,300i,400,400i,600,600i,700,700i,900,900i|Courgette:300,300i,400,400i,600,600i,700,700i,900,900i|Patua+One:300,300i,400,400i,600,600i,700,700i,900,900i|Nunito:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i|Open+Sans:300,300i,400,400i,600,600i,700,700i,900,900i&display=swap">
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

		window.global_id = '5FsLyYml1uZa';
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
	<link rel="preconnect" href="//vip.timezonedb.com">
	<link rel="dns-prefetch" href="//vip.timezonedb.com">
</head>

<body>
	<div style="height:0px;width:0px;opacity:0;position:fixed" class="js_kartra_trackable_object" data-kt-type="kartra_page_tracking" data-kt-value="5FsLyYml1uZa" data-kt-owner="DpwDQa6g">
	</div>
	<div id="page" class="page container-fluid">
		<div id="page_background_color" class="row">
			<div class="content content--popup-overflow-visible" style="background-color: rgb(255, 255, 255); padding: 0px;" id="_66bbb30d2243b">
				<div class="overflow_background_wrapper">
					<div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
					<div class="background_changer_overlay" style="background-image: none;"></div>
				</div>
				<nav class="navbar navbar-inverse navbar-light navbar-light--border-bottom-light">
					<div class="kartra_element_bg kartra_element_bg--padding-top-bottom-tiny" style="margin-top: 0px; margin-bottom: 0px; padding: 10px 0px;">
						<div style="background-color: rgb(243, 113, 33); border-radius: 0px; border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; background-image: none; opacity: 1;" class="background-item background_changer--blur0"></div>
						<div class="container">
							<div class="row row--equal row--xs-equal" data-component="grid">
								<div class="column--vertical-center col-xs-6">
									<div class="js_kartra_component_holder js_kartra_component_holder--min-height-auto">
										<div class="kartra_headline_block kartra_headline_block--flex kartra_headline_block--vertical-center kartra_headline_block--justify-content-start" data-component="bundle">
											<div class="kartra_headline_block__index">
												<div data-component="icon" href="javascript: void(0);">
													<div class="kartra_icon kartra_icon--margin-left-negative-like-tiny kartra_icon--white kartra_icon--top-adjust kartra_icon--royal-blue kartra_icon--small" style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
														<span class="kartra_icon__icon fa fa-phone-square" style="color: rgb(49, 85, 40);"></span>
													</div>
												</div>
											</div>
											<div class="kartra_headline_block__info">
												<div data-component="text">
													<div class="kartra_text kartra_text--dim-black kartra_text--extra-small kartra_text--font-weight-medium kartra_text--margin-bottom-none" style="position: relative;">
														<p>(956) 627-3106</p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="column--vertical-center col-xs-6">
									<div class="js_kartra_component_holder js_kartra_component_holder--min-height-auto">
										<div class="social_icons_wrapper social_icons_wrapper--flex social_icons_wrapper--sm-align-center social_icons_wrapper--margin-bottom-extra-small social_icons_wrapper--negative-margin-left-right-extra-tiny pull-right" data-component="bundle" id="FaDTPsEGB8_xcO7anT4eY" style="margin: 0px -5px 20px;">
											<div data-component="icon">
												<a href="https://www.facebook.com/cooperwellness/" class="toggle_pagelink" data-frame-id="_66bbb30d2243b" target="_blank">
													<div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium" style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
														<span style="color: rgb(255, 255, 255);" class="kartra_icon__icon fa fa-facebook"></span>
													</div>
												</a>
											</div>
											<div data-component="icon" href="javascript: void(0);">
												<a href="https://www.instagram.com/cooperwellnesscenter/" class="toggle_pagelink" data-frame-id="_66bbb30d2243b" target="_blank">
													<div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium" style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
														<span style="color: rgb(255, 255, 255);" class="kartra_icon__icon fa fa-instagram"></span>
													</div>
												</a>
											</div>
											<div data-component="icon" href="javascript: void(0);">
												<a href="https://www.youtube.com/channel/UCihzseMaylCivEhN5lN9Peg/?sub_confirmation=1" class="toggle_pagelink" data-frame-id="_66bbb30d2243b" target="_blank">
													<div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium" style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
														<span style="color: rgb(255, 255, 255);" class="kartra_icon__icon fa fa-youtube-square"></span>
													</div>
												</a>
											</div>
											<div data-component="icon" href="javascript: void(0);">
												<a href="https://twitter.com/DrCooperDockery" class="toggle_pagelink" data-frame-id="_66bbb30d2243b" target="_blank">
													<div class="kartra_icon kartra_icon--margin-left-right-extra-tiny kartra_icon--hover-opacity-medium kartra_icon--dark-grey kartra_icon--medium" style="background-color: rgba(0, 0, 0, 0); margin: 0px auto;">
														<span style="color: rgb(255, 255, 255);" class="kartra_icon__icon fa fa-twitter"></span>
													</div>
												</a>
											</div>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="container nav-elem-wrapper nav-elem-wrapper--md-sm-flex nav-elem-wrapper--md-sm-vertical-center nav-elem-wrapper--md-sm-justify-content-space-between">
						<div class="navbar-header nav-elem-col">
							<div data-component="image">
								<a href="../" data-frame-id="_66bbb30d2243b" class="toggle_pagelink" data-project-id="3" data-page-id="111" target="_parent">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
										</source><img class="kartra_image kartra_image--logo kartra_image--margin-bottom-none pull-left background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="border-color: rgb(49, 85, 40); border-style: none; border-width: 0px; margin: 0px; opacity: 1;" data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9267270_1587359598894Logo-GetHealthywithdrCooper-final-1024x244.png">
									</picture>
								</a>
							</div>
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar_9vxxjb5a2X" aria-expanded="false" aria-controls="navbar">
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

									<div class="dropdown">
										<?php if (isset($_SESSION['customer_id'])): ?>
											<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
												style="background-color: #28a745; border: none; color: white; padding: 5px 5px; border-radius: 5px;">
												<span style="font-size: 20px; color: white; border-radius: 50%; background-color: #28a745; padding: 10px;"><?php echo strtoupper(substr($_SESSION['customer_name'], 0, 3)); ?></span>
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
												style="min-width: 150px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); border-radius: 5px;">
												<a class="dropdown-item" href="#" style="padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
													Logout
												</a>
												<form id="logout-form" action="../acc/auth/logout.php" method="POST" style="display: none;">
													<input type="hidden" name="logout" value="true">
												</form>
											</div>
										<?php else: ?>
											<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
												data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
												style="background-color: #dc3545; border: none; color: white; padding: 10px 20px; border-radius: 5px;">
												<i class="fas fa-user-circle" style="font-size: 20px; color: white;"></i>
											</button>
											<div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
												style="min-width: 150px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); border-radius: 5px;">
												<a class="dropdown-item" href="../acc/auth/login.php" style="padding: 10px 20px; color: #333; text-decoration: none; font-size: 14px;">
													Login
												</a>
												<div class="dropdown-divider" style="margin: 5px 0; border-top: 1px solid #e9ecef;"></div>
												<a class="dropdown-item" href="../acc/auth/register.php"
													style="padding: 10px 20px; color: #dc3545; text-decoration: none; font-size: 14px;">
													Register
												</a>
											</div>
										<?php endif; ?>
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
												local curency <br /> Amount Payable: <span id="totalCost">$75</span>
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
									const openExchangeAppID = <?php echo $open_exchange_api_key; ?>
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
			<div class="content" style="padding: 10px 0px 0px; background-color: rgb(49, 85, 40);" id="_ezi5nhvkw">
				<div class="background_changer background_changer--blur0 js-bg-next-gen" style="opacity: 1;" data-bg='url("//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587360286.jpg")'></div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container">
					<div class="row background_changer--blur0" data-component="grid" id="SCqYbb8WwV" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 30px; margin-bottom: 10px; background-image: none; opacity: 1;">
						<div class="col-md-10 col-md-offset-1" id="1s1aTnfZnx">
							<div class="js_kartra_component_holder">

								<div data-component="text">
									<div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium" style="position: relative; margin-top: 0px; margin-bottom: 0px;">
										<p style="font-size: 1rem; line-height: 1.4em;"><strong><span style="font-size: 4rem; line-height: 2em;">
													<font color="#ffffff"><span style="line-height: 2em; font-size: 4rem;"><span style="line-height: 2em; font-size: 4rem;">CWC COURSES</span></span></font>
												</span></strong></p>
									</div>
								</div>

							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="z3j3qIhtCy">
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
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="content" style="padding: 40px 0px 20px; background-color: rgba(0, 0, 0, 0);" id="_qsyk01xxr">
				<div class="background_changer background_changer--blur0" style="background-image: none; opacity: 1;"></div>
				<div class="background_changer_overlay" style="background-image: none;"></div>
				<div class="container">
					<div class="row background_changer--blur0" data-component="grid" id="DyL9ZcRTqw" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 20px; background-image: none; opacity: 1;">
						<div class="col-md-10 col-md-offset-1">
							<div class="js_kartra_component_holder">

								<!--Text Block -->


								<!-- Heading-->

								<!--Text Block -->

								<!--Divider -->

								<!-- List-->

								<!-- Buttons-->
								<div data-component="headline" id="ihzIWROIek">
									<div class="kartra_headline kartra_headline--size-extra-large kartra_headline--roboto-slab-font kartra_headline--font-weight-regular kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-small" style="position: relative; margin-top: 0px; margin-bottom: 7px;">
										<p style="font-size: 1.8rem; line-height: 1.2em;">
											<font color="#1f2f4f" face="roboto"><b>GET HEALTHY FOR LIFE!</b></font>
										</p>
									</div>
								</div>
								<div class="inline_elements_wrapper" style="justify-content: center;">


								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0 row--equal" data-component="grid" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 30px; background-image: none; opacity: 1;">
						<div class="col-md-5">
							<div class="js_kartra_component_holder">
								<div data-component="image" href="javascript: void(0);">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364336.webp">
										</source>
										<source type="image/jpeg" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364336.jpg">
										</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 339px; max-width: 100%; height: auto;" id="1546804492842_formbutton" data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/unsplash_1587364336.jpg">
									</picture>
								</div>
								<div data-component="image" href="javascript: void(0);" id="LUbsbwUElg">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9708468_1588794836866coming-soon-3.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9708468_1588794836866coming-soon-3.png">
										</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 231px; max-width: 100%; height: auto;" data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9708468_1588794836866coming-soon-3.png">
									</picture>
								</div>
							</div>
						</div>
						<div class="column--vertical-center background_changer--blur0 col-md-7" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
							<div class="js_kartra_component_holder">
								<div data-component="text" id="elNqg7Asmh">
									<div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium" style="position: relative; margin-top: 0px; margin-bottom: 30px;">
										<p style="font-size: 0.8rem;"><b><span style="font-size: 0.8rem;">Live Longer, Stronger, and More Energetic...</span></b></p>

										<p style="font-size: 0.8rem; text-align: justify;"> </p>

										<p style="font-size: 0.8rem; text-align: justify;">When you unlock and master the 9 secret pillars of vibrant health and longevity. You'll also discover the foundational principles of true health and healing so you can enjoy a disease-free live.</p>

										<p style="font-size: 0.8rem; text-align: justify;"> </p>

										<p style="font-size: 0.8rem; text-align: justify;">Only practical solutions are taught in this course so you will not only get well but stay well also. Learn about nature's balm for depression, high blood pressure, and how to safely manage stress.</p>

										<p style="font-size: 0.8rem; text-align: justify;"> </p>

										<p style="text-align: justify;"><span style="font-size:0.80rem;">Get the recipe for happiness in your overall health when you im<font color="#696969" face="roboto"><span style="font-size: 0.8rem;">plement these key habits so you can thrive in your social relationships and live in peace. Click below to get all the details and start right now.</span></font></span></p>
									</div>
								</div>
								<div data-component="button" style="width: auto;" id="wQKLT4i974">
									<a href="javascript: void(0);" class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 kartra_button1--solid kartra_button1--large kartra_button1--squared kartra_button1--shadow-small pull-center toggle_optin" style='font-weight: 400; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";' data-frame-id="_qsyk01xxr" id="1588794160919_formbutton" data-popup-src="https://app.kartra.com/elements/popup_optin_form_single_col_2.html" target="_parent"><span class="kartra_icon__icon fa fa-hand-pointer-o" style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 400;"></span>SIGN UP FOR RELEASE NOTIFICATION</a>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="divider">
									<hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 kartra_divider--full">
								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" id="ji9fY24g8B" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 20px; background-image: none; opacity: 1;">
						<div class="col-md-10 col-md-offset-1">
							<div class="js_kartra_component_holder">

								<!--Text Block -->


								<!-- Heading-->

								<!--Text Block -->

								<!--Divider -->

								<!-- List-->

								<!-- Buttons-->
								<div data-component="headline" id="T77v5Zc2dh">
									<div class="kartra_headline kartra_headline--letter-spacing-extra-tiny kartra_headline--orange-tomato kartra_headline--text-center kartra_headline--h5 kartra_headline--font-weight-medium kartra_headline--montserrat-font" style="position: relative; margin-top: 0px; margin-bottom: 20px;">
										<p style="font-size: 2rem; line-height: 1.2em;"><strong><span style="font-size: 2rem; line-height: 1.2em;"><span style="line-height: 1.2em; font-family: roboto; color: rgb(31, 47, 79); font-size: 2rem;">FOURTEEN DAYS</span></span></strong> <strong><span style="font-size: 2rem; line-height: 1.2em;"><span style="line-height: 1.2em; font-family: roboto; color: rgb(194, 174, 131); font-size: 2rem;">to Amazing Health!</span></span></strong></p>
									</div>
								</div>
								<div class="inline_elements_wrapper" style="justify-content: center;">


								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0 row--equal" data-component="grid" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 30px; background-image: none; opacity: 1;">
						<div class="col-md-5">
							<div class="js_kartra_component_holder">
								<div data-component="image" href="javascript: void(0);">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3797007_155374820656314DTAH-combine-1024x726.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3797007_155374820656314DTAH-combine-1024x726.png">
										</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="border-color: rgb(49, 85, 40); border-style: none; border-width: 0px; margin: 0px auto 20px; opacity: 1; width: 445px; max-width: 100%; height: auto;" id="1544928147202_formbutton" data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3797007_155374820656314DTAH-combine-1024x726.png">
									</picture>
								</div>
							</div>
						</div>
						<div class="column--vertical-center background_changer--blur0 col-md-7" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
							<div class="js_kartra_component_holder">
								<div data-component="text" id="Sj6ug1YRde">
									<div class="kartra_text" style="position: relative;">
										<p style="font-size: 0.8rem; line-height: 1.4em; text-align: center;"><b><span style="font-size: 0.8rem; line-height: 1.4em;"><span style="line-height: 1.4em; font-size: 0.8rem;">Are you struggling to lose weight, reduce blood pressure, lower cholesterol, or get of medications? </span></span></b><span style="background-color: rgb(255, 255, 0); line-height: 1.4em; font-size: 0.8rem;">Even After You've ...</span></p>

										<ul>
											<li style="text-align: justify;"><span><span style="font-size:0.65rem;">Tried this and diet plan <em><span style="font-size: 0.65rem;"><span style="font-size: 0.65rem;">without </span></span></em>success ... just to feel hopeless and let down?</span></span></li>
											<li style="text-align: justify;"><span><span style="font-size:0.65rem;">Gone from doctor to doctor but your blood work shows no improvement ... making you more discouraged and depressed?</span></span></li>
											<li style="text-align: justify;"><span><span style="font-size:0.65rem;">Spent a lot of time, money, and energy on hype that over promised and under delivered ... leaving you feeling like a big failure?</span></span></li>
										</ul>
										<h4 style="text-align: justify; line-height: 1.4em;">
											<span style="font-size: 0.65rem; line-height: 1.4em;"><strong><span style="font-size: 0.65rem; line-height: 1.4em;"><span style="font-size: 0.65rem; line-height: 1.4em;">Before </span></span></strong>You Entertain Another Thought of Throwing In The Towel, Let Me Tell You How Today Can Change Everything.<span style="font-size: 0.65rem; line-height: 1.4em;"> </span></span><strong><span><span style="line-height: 1.4em; font-size: 0.65rem;"><em><span style="line-height: 1.4em; font-size: 0.65rem;"><span style="background-color: rgb(255, 255, 0); line-height: 1.4em; font-size: 0.65rem;">In ONLY 14 Days</span></span></em>!</span></span></strong>
										</h4>
									</div>
								</div>
								<div data-component="button" style="width: auto;" id="Ld7aMsYGz5">
									<a href="../14daystoamazinghealthcourse" class="kartra_button1 kartra_button1--default kartra_button1--roboto-condensed-font kartra_button1--solid kartra_button1--large kartra_button1--squared pull-center toggle_pagelink " style='color: rgb(255, 255, 255); border-color: rgb(194, 174, 131); background-color: rgb(243, 113, 33); font-weight: 400; margin: 0px auto 20px; font-family: "Roboto Condensed";' data-frame-id="_qsyk01xxr" data-project-id="3" data-page-id="196" target="_parent"><span class="kartra_icon__icon fa fa-arrow-right" style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 400;"></span>GET AMAZING HEALTH IN 14 DAYS NOW!</a>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="j77iFcvzDr">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="divider">
									<hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 kartra_divider--full">
								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" id="49SaAPVFDI" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 20px; background-image: none; opacity: 1;">
						<div class="col-md-10 col-md-offset-1">
							<div class="js_kartra_component_holder">

								<!--Text Block -->


								<!-- Heading-->

								<!--Text Block -->

								<!--Divider -->

								<!-- List-->

								<!-- Buttons-->
								<div data-component="headline" id="KfXee9LeWu">
									<div class="kartra_headline kartra_headline--size-extra-large kartra_headline--roboto-slab-font kartra_headline--font-weight-regular kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-small" style="position: relative; margin-top: 0px; margin-bottom: 10px;">
										<p style="font-size: 1.8rem; line-height: 1.2em;"><strong><span style="line-height: 1.2em; font-size: 1.8rem;"><span style="font-family: roboto; font-size: 1.8rem; color: rgb(31, 47, 79); line-height: 1.2em;">Transformation </span><span style="font-family: roboto; font-size: 1.8rem; color: rgb(194, 174, 131); line-height: 1.2em;">To a Healthier You</span></span></strong></p>
									</div>
								</div>
								<div class="inline_elements_wrapper" style="justify-content: center;">


								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 30px; background-image: none; opacity: 1;" id="aNMYRJZpX3">
						<div class="col-md-5">
							<div class="js_kartra_component_holder">
								<div data-component="image" href="javascript: void(0);">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3829645_1554045518705New_Transformation_to_healthier_you_course.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3829645_1554045518705New_Transformation_to_healthier_you_course.png">
										</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px auto 20px; opacity: 1; width: 540px; max-width: 100%; height: auto;" id="1544928106201_formbutton" data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/3829645_1554045518705New_Transformation_to_healthier_you_course.png">
									</picture>
								</div>
							</div>
						</div>
						<div class="col-md-7">
							<div class="js_kartra_component_holder">
								<div data-component="text">
									<div class="kartra_text" style="position: relative;">
										<p style="font-size: 0.8rem; text-align: justify;"><span style="font-size:0.80rem;">Lose Weight, Improve Health and Energy level. This is everyone's goal but not all achieve it. <strong><span style="font-size: 0.8rem;">Until now... </span></strong>There are many courses online promoting health and wellness. However, you're getting relevant expert insights from an actual medical doctor with over 27 years of experience. <strong><span style="font-size: 0.8rem;">BUT,</span></strong> with this, you'll <em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">finally</span></span></em> have access to a course with step-by-step <strong><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">life-changing secrets</span></span></strong> about four pillars of health, which, when followed, will indeed propel you to optimal health for life.</span></p>
									</div>
								</div>
								<div data-component="button" style="width: auto;" id="wQKLT4i974">
									<a href="../transformationcourse" class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 kartra_button1--solid kartra_button1--large kartra_button1--squared kartra_button1--shadow-small pull-center toggle_pagelink " style='font-weight: 400; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";' data-frame-id="_qsyk01xxr" data-project-id="3" data-page-id="198" target="_parent">VIEW COURSE INFO PAGE NOW</a>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="kHowOZpzHO">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="divider">
									<hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 kartra_divider--full">
								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" id="pV7KKoMdTx" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 20px; background-image: none; opacity: 1;">
						<div class="col-md-10 col-md-offset-1">
							<div class="js_kartra_component_holder">

								<!--Text Block -->


								<!-- Heading-->

								<!--Text Block -->

								<!--Divider -->

								<!-- List-->

								<!-- Buttons-->
								<div data-component="headline" id="V2CTxHIuar">
									<div class="kartra_headline kartra_headline--size-extra-large kartra_headline--roboto-slab-font kartra_headline--font-weight-regular kartra_headline--text-center kartra_headline--dim-black kartra_headline--margin-bottom-small" style="position: relative; margin-top: 0px; margin-bottom: 10px;">
										<p style="font-size: 1.8rem; line-height: 1.2em;">
											<font color="#1f2f4f" face="roboto"><b>CERTIFIED HEALTH COACH</b></font>
										</p>
									</div>
								</div>
								<div class="inline_elements_wrapper" style="justify-content: center;">


								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 30px; background-image: none; opacity: 1;">
						<div class="col-md-5">
							<div class="js_kartra_component_holder">
								<div data-component="image" href="javascript: void(0);"><a href="https://app.kartra.com/redirect_to/?asset=page&amp;id=zp7QjqlNKPrK" class="toggle_pagelink " data-project-id="3" data-page-id="36" data-frame-id="_qsyk01xxr" target="_parent">
										<picture>
											<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10000412_1589854052547CWC-staff-team2.webp">
											</source>
											<source type="image/jpeg" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10000412_1589854052547CWC-staff-team2.jpg">
											</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" id="1544660852442_formbutton" style="border-color: rgb(49, 85, 40); border-style: solid; border-width: 0px; margin: 0px auto 20px; opacity: 1; width: 445px; max-width: 100%; height: auto;" data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/10000412_1589854052547CWC-staff-team2.jpg">
										</picture>
									</a></div>
							</div>
						</div>
						<div class="col-md-7">
							<div class="js_kartra_component_holder">
								<div data-component="text" id="elNqg7Asmh">
									<div class="kartra_text kartra_text--dim-grey kartra_text--text-center kartra_text--extra-small kartra_text--margin-bottom-extra-medium" style="position: relative;">
										<h1><span><strong><span><span>Health coaches are needed now more than ever before!</span></span></strong> </span></h1>

										<p style="text-align: justify;">Society is poised for a major health revolution. <em>Dr. C<span><span>ooper Life Coaching Institute </span></span></em>can prepare you to pivot into this field and take advantage of this new paradigm shift. Gain the strategic advantage now by positioning yourself to be the go-to-person for personal wellness intervention. <span>Our curriculum gives you the knowledge, confidence, and tools you need to succeed.</span> Review the three options below and lets get started now. </p>

										<p style="text-align: justify;"> </p>

										<p><strong><span><span>Start A Rewarding Career As a Certified Health Coach Today.</span></span></strong></p>

										<h2 style="text-align: justify;"><em><span><span>...WITHOUT LEAVING YOUR HOME OR SACRIFICING 3-4 YEARS OF YOUR LIFE</span></span></em></h2>
									</div>
								</div>
								<div data-component="button" style="width: auto;" id="wQKLT4i974">
									<a href="../healthcoachcourse" class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 kartra_button1--solid kartra_button1--large kartra_button1--squared kartra_button1--shadow-small pull-center toggle_pagelink " style='font-weight: 400; background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";' data-frame-id="_qsyk01xxr" data-project-id="3" data-page-id="36" target="_parent">GET COURSE INFO NOW</a>
								</div>
							</div>
						</div>
					</div>
					<div class="row" data-component="grid" id="SqL0XsHwNo">
						<div class="col-md-12">
							<div class="js_kartra_component_holder">
								<div data-component="divider" id="lRDiktzwuK">
									<hr class="kartra_divider kartra_divider--border-extra-tiny kartra_divider--border-dim-black-opaque-25 kartra_divider--full">
								</div>
							</div>
						</div>
					</div>
					<div class="row background_changer--blur0" data-component="grid" id="apqN2AfzSy" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 20px; background-image: none; opacity: 1;">
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
							</div>
						</div>
					</div>
					<div class="row row--equal background_changer--blur0" data-component="grid" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; margin-top: 0px; margin-bottom: 10px; background-image: none; opacity: 1;">
						<div class="col-md-5">
							<div class="js_kartra_component_holder">
								<div data-component="image" href="javascript: void(0);">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2548000_1542617857288DRCooper-image1.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2548000_1542617857288DRCooper-image1.png">
										</source><img class="kartra_image kartra_image--full pull-center background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" id="1544660852442_formbutton" style="border-color: rgb(31, 47, 79); border-style: solid; border-width: 0px; margin: 0px auto 10px; opacity: 1; width: 379px; max-width: 100%; height: auto;" data-original="//d11n7da8rpqbjy.cloudfront.net/strategicsecrets/2548000_1542617857288DRCooper-image1.png">
									</picture>
								</div>
								<div data-component="image" href="javascript: void(0);">
									<picture>
										<source type="image/webp" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.webp">
										</source>
										<source type="image/png" data-srcset="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
										</source><img class="kartra_image kartra_image--full pull-left background_changer--blur0" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" onerror="this.onerror=null;this.src='https://d2uolguxr56s4e.cloudfront.net/img/kartrapages/placeholder.jpg';" style="border-color: rgb(51, 51, 51); border-style: none; border-width: 0px; margin: 0px 0px 20px; opacity: 1;" data-original="//d11n7da8rpqbjy.cloudfront.net/cooperwellness/9198738_1587106853801CWC_As_Featured_in-logos-bar.png">
									</picture>
								</div>
							</div>
						</div>
						<div class="column--vertical-center background_changer--blur0 col-md-7" style="background-color: rgba(0, 0, 0, 0); border-radius: 0px; border-style: none; border-width: 0px; padding: 0px 15px; background-image: none; opacity: 1;">
							<div class="js_kartra_component_holder">
								<div data-component="headline" id="mCj3i0nbem">
									<div class="kartra_headline kartra_headline--letter-spacing-extra-tiny kartra_headline--orange-tomato kartra_headline--text-center kartra_headline--h5 kartra_headline--font-weight-medium kartra_headline--montserrat-font" style="position: relative; margin-top: 0px; margin-bottom: 20px;">
										<p style="font-size: 1.2rem; line-height: 1.2em;"><strong>
												<font face="roboto"><span style="color: rgb(31, 47, 79); font-size: 1.2rem; line-height: 1.2em;"><span style="font-size: 1.2rem; line-height: 1.2em; color: rgb(31, 47, 79);">MEET YOUR INSTRUCTOR</span></span></font>
											</strong></p>
									</div>
								</div>
								<div data-component="text" id="g2IrYsypDK">
									<div class="kartra_text kartra_text--open-sans-font kartra_text--font-weight-regular kartra_text--dim-black kartra_text--text-right kartra_text--sm-text-center" style="position: relative;">
										<p style="font-size: 0.8rem; text-align: center;"><strong><span style="font-size: 0.8rem;"><em><span style="font-size: 0.8rem;"><span style='font-size: 0.8rem; font-family: "dancing script";'>Dr. Dona Cooper-Dockery, M.D.</span></span></em></span></strong></p>
									</div>
								</div>
								<div data-component="text">
									<div class="kartra_text" style="position: relative;">
										<p style="font-size: 0.8rem; text-align: center;"><strong><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">Physician  |  TV Producer  |  Bestselling Author</span></span></strong></p>

										<p style="font-size: 0.8rem; text-align: justify;"><span style="font-size:0.80rem;">Dr. Dona Cooper has dedicated over 27 years in positively changing healthcare outcomes both nationally and internationally. She is board certified in internal medicine and holds memberships in the <em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">American Academy of Lifestyle Medicine</span></span></em> and the <em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">American Medical Association</span></span></em>.</span></p>

										<p style="font-size: 0.8rem; text-align: justify;"><br><span style="font-size:0.80rem;">She is the founder and director of <em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">Cooper Internal Medicine</span></span></em> and the <em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">Cooper Wellness and Disease Prevention Center </span></span></em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">and </span></span>is actively engages in various healthy lifestyle seminars and free medical care not only in the USA but also in countries such as Haiti, Jamaica, Ghana, Senegal, the Philippines, and Europe. </span></p>

										<p style="font-size: 0.8rem; text-align: justify;"> </p>

										<p style="font-size: 0.8rem; text-align: justify;"><span style="font-size:0.80rem;">Dr. Cooper is the bestselling author of <span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;"><em><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;">Get Healthy For Life, </span></span></em><span style="font-size: 0.8rem;"><em>Incredibly Delicious Vegan Recipes</em>, and </span></span></span><span style="font-size: 0.8rem;"><span style="font-size: 0.8rem;"><em>Fourteen Days to Amazing Health</em>. Her </span></span></span><span style="font-size:0.80rem;">popular TV show<i> </i>airs bi-weekly on local channels, Facebook, YouTube, Roku, and in over 40 million homes across the America.  </span></p>
									</div>
								</div>
								<div data-component="button" style="width: auto;" id="wQKLT4i974">
									<a href="../about/drdonacooper" class="kartra_button1 kartra_button1--roboto-condensed-font kartra_button1--light-coral-two kartra_button1--box-shadow-inset-bottom-opacity07 kartra_button1--shadow-02 kartra_button1--solid kartra_button1--large kartra_button1--squared kartra_button1--shadow-small pull-center toggle_pagelink " style='font-weight: 700; background-color: rgb(49, 85, 40); color: rgb(255, 255, 255); margin: 0px auto 20px; font-family: "Roboto Condensed";' data-frame-id="_qsyk01xxr" data-project-id="3" data-page-id="200" target="_parent"><span class="kartra_icon__icon fa fa-arrow-right" style="color: rgb(255, 255, 255); border-color: rgb(255, 255, 255); font-weight: 700;"></span>READ FULL PROFILE</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal fade popup_modal popup-modal popup-form-modal js_trackable_wrapper" data-button="1588794160919_formbutton" role="dialog" aria-hidden="true">
				<button type="button" class="closer close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-body">

							<div class="content content--padding-large content--popup-form-large" style="background-color: rgb(255, 255, 255);" id="_9phkrs38t">
								<div class="background_changer" style="opacity: 0.1;" data-bg="url(//d2uolguxr56s4e.cloudfront.net/img/kartrapages/kp-popup-optin-form/kp_popup_optin_form_2.jpg)"></div>
								<div class="background_changer_overlay"></div>
								<div class="container-fluid page-popup-form-container--large">
									<div class="row" data-component="grid">
										<div class="col-md-10 col-md-offset-1 column--padding-left-right-small">
											<div class="js_kartra_component_holder">

												<div data-component="headline">
													<div class="kartra_headline kartra_headline--light-black kartra_headline--roboto-font kartra_headline--extra-giant kartra_headline--font-weight-bold kartra_headline--margin-bottom-extra-tiny kartra_headline--text-center" style="position: relative;">
														<p style="font-size: 2.66rem;"><span style="font-size:2.66rem;">Release Notification</span></p>
													</div>
												</div>
												<div data-component="headline">
													<div class="kartra_headline kartra_headline--orange-tomato kartra_headline--roboto-font kartra_headline--h4 kartra_headline--font-weight-regular kartra_headline--margin-bottom-medium kartra_headline--text-center" style="position: relative;">
														<p>Get Priority Notice When New Products are Released!</p>
													</div>
												</div>
												<div data-component="text">
													<div class="kartra_text kartra_text--font-weight-regular kartra_text--text-center kartra_text--mine-shaft-opaque-75 kartra_text--margin-bottom-medium kartra_text--extra-small" style="position: relative;">
														<p><em>Enter your name and email below now.</em></p>
													</div>
												</div>

												<div data-component="optin">
													<div class="optin_block_form_17 leads_capture kartra_optin_input_btn--shadow-02">
														<div class="leads-form kartra_page_optin_form popup-form-optin-style-2">
															<div class="kartra_optin_wrapper form_class_NaHAofC2eYZa">
																<form action="https://app.kartra.com//process/add_lead/NaHAofC2eYZa" data-input-class="kartra_optin_input_giant" data-submit-bg="rgb(243, 113, 33)" data-submit-color="#FFFFFF" data-submit-type="Solid" data-submit-bold="700" data-submit-size="kartra_btn_giant" data-submit-corners="Squared" data-submit-shadow="btn_shadow_small" data-field-style="box" style="margin-bottom: 20px; margin-top: 0px;" class="filled_optin js_kartra_trackable_object unique_class__mf64tkl12 form_class_NaHAofC2eYZa" data-optin-id="NaHAofC2eYZa" data-domain="https://app.kartra.com/" data-field-bg="" data-field-color="" data-text-color="" data-display-icons="true" data-submit-text="Sign Up" data-kt-type="optin" data-kt-value="NaHAofC2eYZa" data-kt-owner="DpwDQa6g" target="_top" method="POST" data-asset-id="0">
																	<div class="kartra_optin_r">
																		<style>
																			div[class*="leads_capture"] .kartra_page_optin_form .unique_class__mf64tkl12 .kartra_optin_tnc-form button.btn.dropdown-toggle,
																			div[class*="leads_capture"] .kartra_page_optin_form .unique_class__mf64tkl12 .kartra_optin_cg button.btn.dropdown-toggle {
																				background-color: ;
																				color: !important;
																			}

																			.unique_class__mf64tkl12 .kartra_optin_i {
																				color: !important;
																			}

																			.unique_class__mf64tkl12 .kartra_optin_clabel {
																				color: !important;
																			}

																			.unique_class__mf64tkl12 ::-webkit-input-placeholder {
																				color: !important;
																				opacity: 0.7;
																			}

																			.unique_class__mf64tkl12 ::-moz-placeholder {
																				color: !important;
																				opacity: 0.7;
																			}

																			.unique_class__mf64tkl12 :-ms-input-placeholder {
																				color: !important;
																				opacity: 0.7;
																			}

																			.unique_class__mf64tkl12 :-moz-placeholder {
																				color: !important;
																				opacity: 0.7;
																			}

																			div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__mf64tkl12 input[type=radio]+small,
																			div[class*="leads_capture"] .kartra_page_optin_form .kartra_optin_wrapper .unique_class__mf64tkl12 input[type=checkbox]+small {
																				background-color: ;
																			}
																		</style>
																		<div class="kartra_optin_c1">
																			<div class="kartra_optin_cg">
																				<div class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
																					<i class="kartra_optin_i kartra-optin-lineico-person-1"></i>
																					<div class="kartra_optin_asterisk"></div>
																					<input type="text" placeholder="First name" class="required_NaHAofC2eYZa js_kartra_santitation kartra_optin_ti" name="first_name" data-santitation-type="name">
																				</div>
																			</div>
																			<div class="kartra_optin_cg">
																				<div class="kartra_optin_controls kartra_optin_input_giant kartra_optin_icon">
																					<i class="kartra_optin_i kartra-optin-lineico-email"></i>
																					<div class="kartra_optin_asterisk"></div>
																					<input type="text" placeholder="Email" class="required_NaHAofC2eYZa js_kartra_santitation kartra_optin_ti" name="email" data-santitation-type="email">
																				</div>
																			</div>
																			<div class="kartra_optin_cg">
																				<div class="js_gdpr_wrapper clearfix kartra_optin_gdpr_wrapper" style="display: none;">
																					<div class="gdpr_communications js_gdpr_communications kartra_optin_cg kartra_optin_gdpr_terms">
																						<div class="kartra-optin-checkbox">
																							<label class="kartra_optin_field-label kartra-optin-checkbox">
																								<input name="gdpr_communications" type="checkbox" class="js_gdpr_communications_check" value="1">

																								<small></small>


																								<span class="js_gdpr_label_communications">I would like to receive future communications</span>
																							</label>
																						</div>
																					</div>
																					<div class="gdpr_terms js_gdpr_terms  kartra_optin_cg kartra_optin_gdpr_terms">
																						<div class="kartra-optin-checkbox">
																							<label class="kartra_optin_field-label kartra-optin-checkbox">
																								<input name="gdpr_terms" type="checkbox" class="js_gdpr_terms_check" value="1">

																								<small></small>


																								<span class="js_gdpr_label_terms">I agree to the GDPR Terms &amp; Conditions</span>
																							</label>
																						</div>
																						<i class="kartra-optin-lineico-infomation-circle js_kartra_popover_trigger js_kartra_popover_gdpr_trigger" data-popover="js_kartra_gdpr_popover"></i>
																						<div class="js_kartra_gdpr_popover js_kartra_popover kartra_optin_gdpr_terms_popover" style="display: none;">
																							<div class="kartra_optin_popover-title">
																								<div class="kartra_optin_well-inner kartra_optin_well-inner_npadding">
																									<span class="js_gdpr_terms_text">I confirm that I am at least 16 years of age or older<br>
																										<br>
																										I have read and accept any EULA, Terms and Conditions, Acceptable Use Policy, and/or Data Processing Addendum which has been provided to me in connection with the software, products and/or services. <br>
																										<br>
																										I have been fully informed and consent to the collection and use of my personal data for any purpose in connection with the software, products and/or services. <br>
																										<br>
																										I understand that certain data, including personal data, must be collected or processed in order for you to provide any products or services I have requested or contracted for. I understand that in some cases it may be required to use cookies or similar tracking to provide those products or services.. <br>
																										<br>
																										I understand that I have the right to request access annually to any personal data you have obtained or collected regarding me. You have agreed to provide me with a record of my personal data in a readable format. <br>
																										<br>
																										I also understand that I can revoke my consent and that I have the right to be forgotten. If I revoke my consent you will stop collecting or processing my personal data. I understand that if I revoke my consent, you may be unable to provide contracted products or services to me, and I can not hold you responsible for that. <br>
																										<br>
																										Likewise, if I properly request to be forgotten, you will delete the data you have for me, or make it inaccessible. I also understand that if there is a dispute regarding my personal data, I can contact someone who is responsible for handling data-related concerns. If we are unable to resolve any issue, you will provide an independent service to arbitrate a resolution. If I have any questions regarding my rights or privacy, I can contact the email address provided.</span>
																								</div>
																								<a href="javascript:void(0)" class="js_gdpr_popover_close kartra_optin_popover-close js_utility_popover_close" target="_parent">×</a>
																							</div>
																						</div>
																					</div>
																				</div>
																				<button type="submit" class="kartra_optin_submit_btn kartra_optin_btn_block kartra_optin_btn_giant submit_button_NaHAofC2eYZa btn_shadow_small" style="background-color: rgb(243, 113, 33); color: rgb(255, 255, 255); font-weight: 700;">Sign Up</button>
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
											<!--<a-->
											<!--    class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"-->
											<!--    href="https://cooperwellness.kartra.com/page/weightloss"-->
											<!--    data-frame-id="_6723f824ea44e"-->
											<!--    style='color: rgba(255, 255, 255, 0.8); font-weight: 400; font-family: "Open Sans";'-->
											<!--    target="_blank">Weight Loss</a>-->
											<a
												class="kartra_list__link kartra_list__link--open-sans-font kartra_list__link--font-weight-regular kartra_list__link--margin-bottom-extra-small kartra_list__link--semi-pro-white kartra_list__link--hover-opacity-giant toggle_pagelink"
												href="../programs"
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
	<script src="//app.kartra.com/resources/js/page_check?page_id=5FsLyYml1uZa" async defer></script>
	<script>
		if (typeof window['jQuery'] !== 'undefined') {
			window.jsVars = {
				"page_title": "Get Healthy Lifestyle Courses in Texas - Dr Cooper",
				"page_description": "Are you struggling with overweighting or low cholesterol? Take our healthy lifestyle courses by certified health coach Dr. Cooper. Enroll today.",
				"page_keywords": "Get Healthy Lifestyle Courses",
				"page_robots": "index, follow",
				"secure_base_url": "\/\/app.kartra.com\/",
				"global_id": "5FsLyYml1uZa"
			};
			window.global_id = '5FsLyYml1uZa';
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
									<input type="checkbox" name="gdpr_cookies" id="gdpr_cookies" class="cmn-toggle js_accepted_cookies" value="2">
									<label for="gdpr_cookies"></label>
								</div>
								<label class="toggler_label ">
									{:lang_general_banner_cookie_only_essential}
								</label>
							</div>
							<div>
								<button class="gdpr_close js_gdpr_close" type="button" data-type="kartra_page" data-type-id="191" data-type-owner="DpwDQa6g">
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