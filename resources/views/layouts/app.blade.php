<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>@hasSection('pageTitle') @yield('pageTitle') |@endif {{ config('app.name', 'Laravel') }}</title>

  <!-- Styles -->
  <link href="{{ mix('/css/app.css') }}" rel="stylesheet">

  <!-- Scripts -->
  <script src="{{ mix('/js/manifest.js') }}" defer></script>
  <script src="{{ mix('/js/vendor.js') }}" defer></script>
  <script src="{{ mix('/js/app.js') }}" defer></script>

  <!-- fav icons -->
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png" />
  <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png" />
  <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png" />
  <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png" />
  <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png" />
  <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png" />
  <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png" />
</head>

<body class="with-footer">
  <div id="app">
    @include('layouts.header')

    @include('cookieConsent::index')

    <!-- main body -->
    <div class="content">
@include('partials.flash')
      <div class="row">
        <div class="col-sm-12">
@yield('content')
        </div>
      </div>
    </div>
    <!-- main body end -->

    @include('layouts.footer')

    <!-- Scripts stack -->
    @stack('scripts')
    <!-- Scripts stack end -->
  </div>
</body>
</html>
