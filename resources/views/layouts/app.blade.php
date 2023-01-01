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
  <link href="{{ mix('/css/vue.css') }}" rel="stylesheet">

  <!-- Scripts -->
  @if (null != config('services.stripe.key'))
  <script src="https://js.stripe.com/v3/"></script>{{-- Stripe.JS can not be webpacked--}}
  @endif
  <script src="{{ mix('/js/manifest.js') }}" defer></script>
  <script src="{{ mix('/js/vendor.js') }}" defer></script>
  <script src="{{ mix('/js/app.js') }}" defer></script>

  <!-- fav icons -->
  <link rel="shortcut icon" href="/images/{{ config('branding.theme', "nottinghack") }}/favicon.ico?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}" type="image/x-icon">
  <link rel="apple-touch-icon" sizes="60x60" href="/images/{{ config('branding.theme', "nottinghack") }}/apple-touch-icon-60x60.png">
  <link rel="apple-touch-icon" sizes="76x76" href="/images/{{ config('branding.theme', "nottinghack") }}/apple-touch-icon-76x76.png">
  <link rel="apple-touch-icon" sizes="120x120" href="/images/{{ config('branding.theme', "nottinghack") }}/apple-touch-icon-120x120.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/images/{{ config('branding.theme', "nottinghack") }}/apple-touch-icon-152x152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/{{ config('branding.theme', "nottinghack") }}/apple-touch-icon.png?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}">
  <link rel="icon" type="image/png" sizes="32x32" href="/images/{{ config('branding.theme', "nottinghack") }}/favicon-32x32.png?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}">
  <link rel="icon" type="image/png" sizes="16x16" href="/images/{{ config('branding.theme', "nottinghack") }}/favicon-16x16.png?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}">
  <link rel="manifest" href="/images/{{ config('branding.theme', "nottinghack") }}/site.webmanifest?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}">
  <link rel="mask-icon" href="/images/{{ config('branding.theme', "nottinghack") }}/safari-pinned-tab.svg?v=XBJgQp70gw{{ config('branding.theme', "nottinghack") }}" color="#195905">
  <meta name="apple-mobile-web-app-title" content="HMS">
  <meta name="application-name" content="HMS">
  <meta name="msapplication-TileColor" content="#195905">
  <meta name="theme-color" content="#ffffff">
  <!-- head end -->
</head>

<body class="d-flex flex-column min-vh-100">
  @include('layouts.header')

  @include('cookie-consent::index')

  <!-- main body -->
  <main id="app" class="flex-fill my-3">
@include('partials.flash')
    <div class="row mr-0">
      <div class="col-sm-12">
@yield('content')
      </div>
    </div>
    <flash></flash>
  </main>
  <!-- main body end -->

  @include('layouts.footer')

  <!-- Scripts stack -->
  @stack('scripts')
  <!-- Scripts stack end -->
</body>
</html>
