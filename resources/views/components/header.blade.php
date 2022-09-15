<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Nextrack management system">
    <meta name="author" content="Trieste">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/logo/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/logo/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/logo/favicon/site.webmanifest') }}">
    <title>Nextrack</title>
    <script src="https://kit.fontawesome.com/212286cc85.js" crossorigin="anonymous"></script>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Bootstrap Core CSS -->
    <link href="{{ asset('assets/bootstrap/dist/css/bootstrap.css') }}" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="{{ asset('assets/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css') }}" rel="stylesheet">
    @if($standardDisplay['profile']->theme == "dark")
        <!-- animation CSS -->
        <link href="{{ asset('css/dark/animate.css') }}" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('css/dark/style.css') }}" rel="stylesheet">
        <!-- color CSS -->
        <link href="{{ asset('css/dark/colors/default-dark.css') }}" id="theme" rel="stylesheet">
        <!-- datatables -->
        <link href="{{ asset('css/dark/datatables.css') }}" rel="stylesheet" type="text/css" />
    @else
        <!-- animation CSS -->
        <link href="{{ asset('css/light/animate.css') }}" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('css/light/style.css') }}" rel="stylesheet">
        <!-- color CSS -->
        <link href="{{ asset('css/light/colors/default-light.css') }}" id="theme" rel="stylesheet">
        <!-- datatables -->
        <link href="{{ asset('css/light/datatables.css') }}" rel="stylesheet" type="text/css" />
    @endif


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->


</head>

<body class="fix-sidebar">
    <!-- Preloader -->
    <div class="preloader">
        <div class="cssload-speeding-wheel"></div>
    </div>
    <div id="wrapper">

        @yield('topMenu')

        <footer class="footer text-center">Nextrack brought to you by Trieste </footer>
    </div>
        <!-- /#page-wrapper -->
    
    
    
    </div>
    
    
    
    <!-- /#wrapper -->
    
</body>

</html>    