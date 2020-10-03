<!doctype html>
<html lang="en">

  <head>
    <title>Manou</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=DM+Sans:300,400,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('/public/dist-user/fonts/icomoon/style.css') }}">

    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/bootstrap.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/animate.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/jquery.fancybox.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/owl.carousel.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/owl.theme.default.min.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/fonts/flaticon/font/flaticon.css') }} ">
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/aos.css') }} ">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="{{ asset('/public/dist-user/css/style.css') }} ">

  </head>

  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

    
    <div class="site-wrap" id="home-section">

      <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
          <div class="site-mobile-menu-close mt-3">
            <span class="icon-close2 js-menu-toggle"></span>
          </div>
        </div>
        <div class="site-mobile-menu-body"></div>
      </div>



      <header class="site-navbar site-navbar-target" role="banner">

        <div class="container">
          <div class="row align-items-center position-relative">

            <div class="col-lg-4">
              {{-- <nav class="site-navigation text-right ml-auto " role="navigation">
                <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">
                  <li class="active"><a href="index.html" class="nav-link">Home</a></li>
                  <li><a href="project.html" class="nav-link">Project</a></li>
                  <li><a href="services.html" class="nav-link">Services</a></li>
                </ul>
              </nav> --}}
            </div>
            <div class="col-lg-4 text-center">
              <div class="site-logo">
                <a href="index.html">Manou</a>
              </div>


              <div class="ml-auto toggle-button d-inline-block d-lg-none"><a href="#" class="site-menu-toggle py-5 js-menu-toggle text-white"><span class="icon-menu h3 text-white"></span></a></div>
            </div>
            <div class="col-lg-4">
              {{-- <nav class="site-navigation text-left mr-auto " role="navigation">
                <ul class="site-menu main-menu js-clone-nav ml-auto d-none d-lg-block">
                  <li><a href="about.html" class="nav-link">About</a></li>
                  <li><a href="blog.html" class="nav-link">Blog</a></li>
                  <li><a href="contact.html" class="nav-link">Contact</a></li>
                </ul>
              </nav> --}}
            </div>
            

          </div>
        </div>

      </header>

    
    
    
     <div id="demo" class="carousel slide" data-ride="carousel">
  <ul class="carousel-indicators">
    <li data-target="#demo" data-slide-to="0" class="active"></li>
    <li data-target="#demo" data-slide-to="1"></li>
    <li data-target="#demo" data-slide-to="2"></li>
  </ul>
  <div class="carousel-inner" style="width: 100%;height: 100%;">
    <div class="carousel-item active">
      <img src="{{ asset('/public/dist-user/images/2.png') }}" alt="Los Angeles" width="1100" height="500">
      <div class="carousel-caption">
        <h3>Los Angeles</h3>
        <p>We had such a great time in LA!</p>
      </div>   
    </div>
    <div class="carousel-item">
      <img src="{{ asset('/public/dist-user/images/3.png') }}" alt="Chicago" width="1100" height="500">
      <div class="carousel-caption">
        <h3>Chicago</h3>
        <p>Thank you, Chicago!</p>
      </div>   
    </div>
    <div class="carousel-item">
      <img src="{{ asset('/public/dist-user/images/4.png') }}" alt="New York" width="1100" height="500">
      <div class="carousel-caption">
        <h3>New York</h3>
        <p>We love the Big Apple!</p>
      </div>   
    </div>
  </div>
  <a class="carousel-control-prev" href="#demo" data-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </a>
  <a class="carousel-control-next" href="#demo" data-slide="next">
    <span class="carousel-control-next-icon"></span>
  </a>
</div>

    @yield('content')
  
    

    

    
    <footer class="site-footer">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-md-7">
                <h2 class="footer-heading mb-4">About Us</h2>
                <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. </p>

              </div>
              <div class="col-md-4 ml-auto">
                <h2 class="footer-heading mb-4">Features</h2>
                <ul class="list-unstyled">
                  <li><a href="#">About Us</a></li>
                  <li><a href="#">Testimonials</a></li>
                  <li><a href="#">Terms of Service</a></li>
                  <li><a href="#">Privacy</a></li>
                  <li><a href="#">Contact Us</a></li>
                </ul>
              </div>

            </div>
          </div>
          <div class="col-md-4 ml-auto">

            <div class="mb-5">
              <h2 class="footer-heading mb-4">Subscribe to Newsletter</h2>
              <form action="#" method="post" class="footer-suscribe-form">
                <div class="input-group mb-3">
                  <input type="text" class="form-control border-secondary text-white bg-transparent" placeholder="Enter Email" aria-label="Enter Email" aria-describedby="button-addon2">
                  <div class="input-group-append">
                    <button class="btn btn-primary text-white" type="button" id="button-addon2">Subscribe</button>
                  </div>
                </div>
            </div>


            <h2 class="footer-heading mb-4">Follow Us</h2>
            <a href="#about-section" class="smoothscroll pl-0 pr-3"><span class="icon-facebook"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-twitter"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-instagram"></span></a>
            <a href="#" class="pl-3 pr-3"><span class="icon-linkedin"></span></a>
            </form>
          </div>
        </div>
        <div class="row pt-5 mt-5 text-center">
          <div class="col-md-12">
            <div class="border-top pt-5">
              <p>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="icon-heart text-danger" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank" >Colorlib</a>
            <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </p>
            </div>
          </div>

        </div>
      </div>
    </footer>

    </div>

    <script src="{{ asset('/public/dist-user/js/jquery-3.3.1.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/popper.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/bootstrap.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/owl.carousel.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/jquery.sticky.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/jquery.waypoints.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/jquery.animateNumber.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/jquery.fancybox.min.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/jquery.easing.1.3.js') }} "></script>
    <script src="{{ asset('/public/dist-user/js/aos.js') }} "></script>

    <script src="{{ asset('/public/dist-user/js/main.js') }} "></script>

  </body>

</html>
