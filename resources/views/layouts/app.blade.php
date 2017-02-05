<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ elixir('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>

    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/5fd8ad5172.js"></script>
</head>
<body class="with-footer">
  <header>
    <div class="row expanded header">
      <div class="columns shrink">
        <img src="/images/hackspace-logo-white-75.png" width="75" height="75">
      </div>
      <div class="columns">
        <h1>Nottingham Hackspace</h1>
      </div>
    </div>

    <div class="row expanded userbar">
      <ul class="menu align-right">
        @if (Auth::guest())
        <li><a href="{{ url('/login') }}">Log In</a></li>
        @if (SiteVisitor::inTheSpace())
        <li><a href="{{ route('registerInterest') }}">Register Interest</a></li>
        @endif
        @else
        <li>Logged in as {{ Auth::user()->getFirstName() }} @if (Auth::viaRemember()) (via Remember Me) @endif</li>
        <li>
          <a href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
          <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>
        </li>
        @endif
      </ul>
    </div>
  </header>

  <!-- main body -->
  <div class="content">

  @include('partials.flash')

  <div class="row align-top">
      @if (!Auth::guest())
      <div class="columns small-12 small-order-1 medium-2 medium-order-0 large-2">
        <ul class="menu vertical">
          <li class="active"><a href="#">News</a></li>
          <li><a href="#">Tools</a></li>
          <li><a href="#">Projects</a></li>
          <li><a href="#">Snackspace</a></li>
          <li><a href="#">Account</a></li>
          <li><a href="#">Links</a></li>
          <li><a href="#">Admin</a></li>
          <ul>
            <li><a href="{{ route('roles.index') }}">Roles</a></li>
          </ul>
        </ul>
      </div>
      @endif

      @if (Auth::guest())
      <div class="columns">
      @else
      <div class="columns small-12 small-order-0 medium-10 medium-order-1 large-7">
      @endif
        @yield('content')
      </div>

      @if (!Auth::guest())
      <div class="columns small-12 small-order-3 medium-12 medium-order-2 large-3">
        <!-- this is where upcoming tool bookings might go -->
      </div>
      @endif

    </div>

    <div class="row">
      @include('cookieConsent::index')
    </div>

  </div>

  <!-- footer -->
  <footer>
    <div class="row expanded footer">
      <div class="columns small-12 medium-3">
        <ul class="nomarkers">
          <li>HMS Version 2.0.0</li>
          <li><a href="http://github.com/nottinghack/hms2">Get Source</a></li>
          <li><a href="#">Credits</a></li>
          <li><a href="http://www.nottinghack.org.uk">Nottinghack Website</a></li>
          <li class="copyright">&copy; 2016 Nottinghack</li>
        </ul>
      </div>
      <div class="columns small-12 medium-3">
        <ul class="nomarkers">
          <li><a href="#"><i class="fa fa-fw fa-twitter"></i>&nbsp;Twitter</a></li>
          <li><a href="#"><i class="fa fa-fw fa-envelope"></i>&nbsp;Google Group</a></li>
          <li><a href="#"><i class="fa fa-fw fa-flickr"></i>&nbsp;Flickr</a></li>
          <li><a href="#"><i class="fa fa-fw fa-youtube"></i>&nbsp;YouTube</a></li>
          <li><a href="#"><i class="fa fa-fw fa-facebook"></i>&nbsp;Facebook</a></li>
        </ul>
      </div>
      <div class="columns small-12 medium-3">
        <ul class="nomarkers">
          <li>Nottingham Hackspace Ltd</li>
          <li>No. 07766826</li>
          <li>Reg. in England &amp; Wales</li>
        </ul>
      </div>
      <div class="columns small-12 medium-3">
        <address>
          Unit F6 BizSpace<br>
          Roden House<br>
          Business Centre<br>
          Nottingham<br>
          NG3 1JH
        </address>
      </div>
    </div>
  </footer>


  <!-- Scripts -->
  <script src="{{ elixir('js/app-base.js')}}"></script>
  <script src="{{ elixir('js/app.js') }}"></script>

  @stack('scripts')
</body>
</html>
