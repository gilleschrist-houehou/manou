<!DOCTYPE html>
<html lang="en">

<head>
    <link rel='shortcut icon' type='image/x-icon' href='{{ asset('/public/dist-admin/images/icon/favicon.ico') }}' />
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Manou | @yield('title')</title>

    <!-- Fontfaces CSS-->
    <link href="{{ asset('/public/dist-admin/css/font-face.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/font-awesome-4.7/css/font-awesome.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/font-awesome-5/css/fontawesome-all.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/mdi-font/css/material-design-iconic-font.min.css') }}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="{{ asset('/public/vendor/bootstrap-4.1/bootstrap.min.css') }}" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="{{ asset('/public/vendor/animsition/animsition.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/wow/animate.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/css-hamburgers/hamburgers.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/slick/slick.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/select2/select2.min.css') }}" rel="stylesheet" media="all">
    <link href="{{ asset('/public/vendor/perfect-scrollbar/perfect-scrollbar.css') }}" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="{{ asset('/public/dist-admin/css/theme.css') }}" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <div class="page-wrapper">
        <div class="page-content--bge5">
            <div class="container">
                <div class="login-wrap">
                    <div class="login-content">
                        <div class="login-logo">
                            <a href="#">
                                <img src="{{ asset('/public/dist-admin/images/icon/logo.png') }}" alt="CoolAdmin">
                            </a>
                        </div>
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="{{ asset('/public/vendor/jquery-3.2.1.min.js') }}"></script>
    <!-- Bootstrap JS-->
    <script src="{{ asset('/public/vendor/bootstrap-4.1/popper.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/bootstrap-4.1/bootstrap.min.js') }}"></script>
    <!-- Vendor JS       -->
    <script src="{{ asset('/public/vendor/slick/slick.min.js') }}">
    </script>
    <script src="{{ asset('/public/vendor/wow/wow.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/animsition/animsition.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js') }}">
    </script>
    <script src="{{ asset('/public/vendor/counter-up/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/counter-up/jquery.counterup.min.js') }}">
    </script>
    <script src="{{ asset('/public/vendor/circle-progress/circle-progress.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('/public/vendor/chartjs/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('/public/vendor/select2/select2.min.js') }}">
    </script>

    <!-- Main JS-->
    <script src="{{ asset('/public/dist-admin/js/main.js') }}"></script>

</body>

</html>
<!-- end document-->