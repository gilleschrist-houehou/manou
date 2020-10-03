<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- <html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml" lang="fr"> --}}
    <head>
      <meta charset="utf-8">
      <meta property="og:url" content="<?="https://".$_SERVER['HTTP_HOST'] ?>" />
      <meta property="og:type" content="Manou" />
      <meta property="og:title" content="Manou | Votre site de publication d'annonces et de recrutements. " />
      <meta property="og:image"  content="<?="https://".$_SERVER['HTTP_HOST']."/public/dist-user/images/templatemo_logo.png" ?>">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Manou | Votre site de publication d'annonces et de recrutements. ">
    <meta name="author" content="">
    {{-- <meta name="google-site-verification" content="F_xUO1cBcZFWvfVDvZnJDH8m-dZlrGZFbe5TlsZCV_Y" />
    <meta name="msvalidate.01" content="F55CC7171BCDB0AA378A43C59F8C91A7" /> --}}
        <link rel='shortcut icon' type='image/x-icon' href='{{ asset('/public/dist-admin/images/icon/favicon.ico') }}' />
        <title>Manou | Votre site de publication d'annonces et de recrutements.</title>
        {{-- <meta name="keywords" content="" />
    <meta name="description" content="" /> --}}
<!--

Urbanic Template

http://www.templatemo.com/tm-395-urbanic

-->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<link rel="shortcut icon" href="PUT YOUR FAVICON HERE">-->
        
        <!-- Google Web Font Embed -->
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
        
        <!-- Bootstrap core CSS -->
        <link href="{{ asset('/public/dist-user/css/bootstrap.css') }}" rel='stylesheet' type='text/css'>

        <!-- Custom styles for this template -->
        <link href="{{ asset('/public/dist-user/js/colorbox/colorbox.css') }}"  rel='stylesheet' type='text/css'>
        <link href="{{ asset('/public/dist-user/css/templatemo_style.css') }}"  rel='stylesheet' type='text/css'>
        <link href="{{ asset('/public/dist-user/css/test.css') }}"  rel='stylesheet' type='text/css'>
        <link href="{{ asset('/public/vendor/font-awesome-4.7/css/font-awesome.min.css') }}" rel="stylesheet" media="all">

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body>

        <div class="templatemo-top-bar" id="templatemo-top">
            <div class="container">
                <div class="subheader">
                    <div id="phone" class="pull-left">
                            <img src="{{ asset('/public/dist-user/images/phone.png') }}" alt="phone"/>
                            +229 96 26 11 15
                    </div>
                    <div id="email" class="pull-right">
                            <img src="{{ asset('/public/dist-user/images/email.png') }}" alt="email"/>
                            manou@contact.com
                    </div>
                </div>
            </div>
        </div>
        <div class="templatemo-top-menu">
            <div class="container">
                <!-- Static navbar -->
                <div class="navbar navbar-default" role="navigation">
                    <div class="container">
                        <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                </button>
                                <a href="{{route('home')}}" class="navbar-brand"><img src="{{ asset('/public/dist-user/images/templatemo_logo.png') }}" alt="manou" title="Urbanic Template" /></a>
                        </div>
                        <div class="navbar-collapse collapse" id="templatemo-nav-bar">
                            
                            <ul class="nav navbar-nav navbar-right" style="margin-top: 40px;">
                              <?php
                                $segment = Request::segment(1);
                              ?>
                                <li class="
                                @if(!$segment || $segment=='annonces')
                                active
                                @endif">
                                  <a href="{{route('home')}}">ANNONCES</a>
                                </li>
                                <li class="
                                @if($segment=='recrutements')
                                active
                                @endif">
                                  <a href="{{route('recrutements')}}">RECRUTEMENTS</a>
                                </li>
                                <li class="
                                @if($segment=='contact')
                                active
                                @endif">
                                  <a href="{{route('contact')}}">CONTACT</a>
                                </li>
                            </ul>
                        </div><!--/.nav-collapse -->
                    </div><!--/.container-fluid -->
                </div><!--/.navbar -->
            </div> <!-- /container -->
        </div>
      
<!-- contenu -->
          @yield('content')


        <div class="templatemo-footer" >
            <div class="container">
                <div class="row">
                    <div class="text-center">

                        <div class="footer_container">
                            <ul class="list-inline">
                                <li>
                                    <a href="#">
                                        <span class="social-icon-fb"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="social-icon-whatsapp"></span>
                                    </a>
                                </li>
                                {{-- <li>
                                    <a href="#">
                                        <span class="social-icon-twitter"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="social-icon-linkedin"></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="social-icon-dribbble"></span>
                                    </a>
                                </li> --}}
                            </ul>
                            <div class="height30"></div>
                            <a class="btn btn-lg btn-orange" href="#" role="button" id="btn-back-to-top"> <i class="fa fa-arrow-up"></i> </a>
                            <div class="height30"></div>
                        </div>
                        <div class="footer_bottom_content">
                        <span id="footer-line">Copyright © <?=date('Y')?> <a href="#">Manou</a></span>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('/public/dist-user/js/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('/public/dist-user/js/bootstrap.min.js') }}"  type="text/javascript"></script>
        <script src="{{ asset('/public/dist-user/js/stickUp.min.js') }}"  type="text/javascript"></script>
        <script src="{{ asset('/public/dist-user/js/colorbox/jquery.colorbox-min.js') }}"  type="text/javascript"></script>
        <script src="{{ asset('/public/dist-user/js/templatemo_script.js') }}"  type="text/javascript"></script>
    <!-- templatemo 395 urbanic -->
    </body>
</html>