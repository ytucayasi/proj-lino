<!doctype html>
<html class="no-js" lang="en">

<head>
    <title>Lino</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="author" content="ThemeZaa">
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <meta name="description"
        content="Elevate your online presence with Crafto - a modern, versatile, multipurpose Bootstrap 5 responsive HTML5, SCSS template using highly creative 52+ ready demos.">

    <!-- favicon icon -->
    <link rel="shortcut icon" href="images/favicon.png">
    <link rel="apple-touch-icon" href="images/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

    <!-- google fonts preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- slider revolution CSS files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/layers.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/navigation.css') }}">

    <!-- style sheets and font icons  -->
    <link rel="stylesheet" href="{{ asset('css/vendors.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/icon.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/decor-store.css') }}" />
</head>

<body data-mobile-nav-style="classic">
    <!-- start header -->
    <livewire:layout.header />
    <!-- end header -->
    {{ $slot }}
    <!-- start footer -->
    <livewire:layout.footer />
    <!-- end footer -->
    <!-- start cookie message -->
    <div id="cookies-model" class="cookie-message bg-dark-gray border-radius-8px">
        <div class="cookie-description fs-14 text-white mb-20px lh-22">We use cookies to enhance your browsing
            experience, serve personalized ads or content, and analyze our traffic. By clicking "Allow cookies" you
            consent to our use of cookies. </div>
        <div class="cookie-btn">
            <a href="#"
                class="btn btn-transparent-white border-1 border-color-transparent-white-light btn-very-small btn-switch-text btn-rounded w-100 mb-15px"
                aria-label="btn">
                <span>
                    <span class="btn-double-text" data-text="Cookie policy">Cookie policy</span>
                </span>
            </a>
            <a href="#"
                class="btn btn-white btn-very-small btn-switch-text btn-box-shadow accept_cookies_btn btn-rounded w-100"
                data-accept-btn aria-label="text">
                <span>
                    <span class="btn-double-text" data-text="Allow cookies">Allow cookies</span>
                </span>
            </a>
        </div>
    </div>
    <!-- end cookie message -->
    <!-- start sticky elements -->
    <div class="sticky-wrap d-none d-xl-inline-block z-index-1" data-animation-delay="100" data-shadow-animation="true">
        <div class="elements-social social-icon-style-10">
            <ul class="fs-15 fw-500">
                <li class="me-30px"><a class="facebook" href="https://www.facebook.com/" target="_blank">
                        <i class="fa-brands fa-facebook-f me-10px"></i>
                        <span class="alt-font">Facebook</span>
                    </a>
                </li>
                <li class="me-30px">
                    <a class="dribbble" href="http://www.dribbble.com" target="_blank">
                        <i class="fa-brands fa-dribbble me-10px"></i>
                        <span class="alt-font">Dribbble</span>
                    </a>
                </li>
                <li class="me-30px">
                    <a class="twitter" href="http://www.twitter.com" target="_blank">
                        <i class="fa-brands fa-twitter me-10px"></i>
                        <span class="alt-font">Twitter</span>
                    </a>
                </li>
                <li>
                    <a class="instagram" href="http://www.instagram.com" target="_blank">
                        <i class="fa-brands fa-instagram me-10px"></i>
                        <span class="alt-font">Instagram</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- end sticky elements -->
    <!-- start scroll progress -->
    <div class="scroll-progress d-none d-xxl-block">
        <a href="#" class="scroll-top" aria-label="scroll">
            <span class="scroll-text">Scroll</span><span class="scroll-line"><span class="scroll-point"></span></span>
        </a>
    </div>
    <!-- end scroll progress -->

    <!-- javascript libraries -->
    <script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/vendors.min.js') }}"></script>

    <!-- slider revolution core javaScript files -->
    <script type="text/javascript" src="{{ asset('js/jquery.themepunch.tools.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery.themepunch.revolution.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/revolution.extension.slideanims.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/revolution.extension.actions.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/revolution.extension.layeranimation.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/revolution.extension.navigation.min.js') }}"></script>

    <!-- Slider Revolution add on files -->
    <script type='text/javascript' src='{{ asset('js/revolution.addon.particles.min.js?ver=1.0.3') }}'></script>

    <!-- Slider's main "init" script -->
    <script type="text/javascript">

        /* https://learn.jquery.com/using-jquery-core/document-ready/ */
        jQuery(document).ready(function () {
            /* initialize the slider based on the Slider's ID attribute from the wrapper above */
            jQuery('#decor-store-slider').show().revolution({
                sliderType: "standard",
                /* sets the Slider's default timeline */
                delay: 9000,
                /* options are 'auto', 'fullwidth' or 'fullscreen' */
                sliderLayout: 'fullscreen',
                /* RESPECT ASPECT RATIO */
                autoHeight: 'off',
                /* options that disable autoplay */
                stopLoop: "on",
                stopAfterLoops: 0,
                stopAtSlide: 1,
                navigation: {
                    keyboardNavigation: "on",
                    keyboard_direction: "horizontal",
                    mouseScrollNavigation: "off",
                    mouseScrollReverse: "default",
                    onHoverStop: "off",
                    touch: {
                        touchenabled: "on",
                        touchOnDesktop: "on",
                        swipe_threshold: 75,
                        swipe_min_touches: 1,
                        swipe_direction: "horizontal",
                        drag_block_vertical: true
                    },
                    arrows: {

                        enable: false,
                        style: 'uranus',
                        rtl: false,
                        hide_onleave: false,
                        hide_onmobile: false,
                        hide_under: 0,
                        hide_over: 778,
                        hide_delay: 200,
                        hide_delay_mobile: 1200,
                        left: {
                            container: 'slider',
                            h_align: 'left',
                            v_align: 'center',
                            h_offset: 10,
                            v_offset: 10
                        },
                        right: {
                            container: 'slider',
                            h_align: 'right',
                            v_align: 'center',
                            h_offset: 10,
                            v_offset: 10
                        }

                    }

                },
                /* Lazy Load options are "all", "smart", "single" and "none" */
                lazyType: "smart",
                spinner: "spinner0",
                /* DISABLE FORCE FULL-WIDTH */
                fullScreenAlignForce: 'off',
                hideThumbsOnMobile: 'off',
                hideSliderAtLimit: 0,
                hideCaptionAtLimit: 0,
                hideAllCaptionAtLilmit: 0,
                /* [DESKTOP, LAPTOP, TABLET, SMARTPHONE] */
                responsiveLevels: [1240, 1024, 778, 480],
                /* [DESKTOP, LAPTOP, TABLET, SMARTPHONE] */
                gridwidth: [1220, 1024, 778, 480],
                /* [DESKTOP, LAPTOP, TABLET, SMARTPHONE] */
                gridheight: [900, 1000, 960, 720],
                /* [DESKTOP, LAPTOP, TABLET, SMARTPHONE] */
                visibilityLevels: [1240, 1024, 1024, 480],
                fallbacks: {
                    simplifyAll: 'on',
                    nextSlideOnWindowFocus: 'off',
                    disableFocusListener: false
                },
            });
        });
    </script>
    <script type="text/javascript" src="{{ asset('js/main.js') }}"></script>
</body>

</html>