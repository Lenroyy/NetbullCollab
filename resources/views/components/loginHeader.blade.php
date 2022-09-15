<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Nextrack">
        <meta name="keywords" content="safety, building, construction, hygenists">
        <meta name="author" content="Curtis Thomson">

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logo/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/images/logo/favicon/site.webmanifest') }}">
        <title>Nextrack</title>
        <!-- Bootstrap Core CSS -->
        <link href="{{ asset('assets/bootstrap/dist/css/bootstrap.css') }}" rel="stylesheet">
        <!-- This is a Animation CSS -->
        <link href="{{ asset('css/dark/animate.css') }}" rel="stylesheet">
        <!-- This is a Custom CSS -->
        <link href="{{ asset('css/dark/style.css') }}" rel="stylesheet">
        <!-- color CSS you can use different color css from css/colors folder -->
        <!-- We have chosen the skin-blue (blue.css) for this starter
            page. However, you can choose any other skin from folder css / colors .
            -->
        <link href="{{ asset('css/dark/colors/default-dark.css') }}" id="theme" rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>

        <!-- Preloader -->
        <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
        </div>
        <section id="wrapper" class="login-register" style="overflow: auto;">
            <div class="login-box login-sidebar">
                <div class="white-box">
                    @yield('content')
                </div>
            </div>
        </section>
        <!-- jQuery -->
        <script src="{{ asset('assets/plugins/bower_components/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="{{ asset('assets/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <!-- Menu Plugin JavaScript -->
        <script src="{{ asset('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js') }}"></script>

        <!--Slimscroll JavaScript For custom scroll-->
        <script src="{{ asset('js/jquery.slimscroll.js') }}"></script>
        <!--Wave Effects -->
        <script src="{{ asset('js/waves.js') }}"></script>
        <!-- Custom Theme JavaScript -->
        <script src="{{ asset('js/custom.min.js') }}"></script>

    </body>
</html>
