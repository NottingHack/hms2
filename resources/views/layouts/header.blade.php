<!-- header -->
<header id="headerApp">
  <div class="header align-items-center py-3">
    <div class="col-sm-auto">
      <a href="/">
        <!-- Hackspace logo SVG -->
        @content('logo', 'svg')
      </a>
    </div>
    <div class="col-sm">
      <div class="row align-items-center">
        <div class="col-md-10 col-lg-8">
@hasSection('pageTitle')
          <h1 class="tiny-header d-none d-md-block"><a href="/">{{ config('branding.space_name') }}</a></h1>
          <h2 class="big-header">@yield('pageTitle')</h2>
@else
          <h1><a href="/">{{ config('branding.space_name') }}</a></h1>
@endif
        </div>
@can('search.users')
        <div class="col-md-10 col-lg-4">
          <member-search action="users.admin.show"></member-search>
        </div>
@endcan
      </div>
    </div>
  </div>

@include('layouts.navbar')

</header>
<!-- header end -->
