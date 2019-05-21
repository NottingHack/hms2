<!-- navbar -->
<nav class="row navbar navbar-expand-md" role="navigation">
  {{-- build the nav toggler --}}
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
    <div class="navbar-toggler-icon"><i class="fas fa-bars"></i>&nbsp;Menu</div>
  </button>


  {{-- right side guest --}}
  @guest
  <ul class="order-md-1 navbar-nav flex-row">
    {{-- Login / Register --}}
    <li class="nav-item {{ Route::currentRouteName() == 'login' ? 'active': '' }}">
      <a class="nav-link" href="{{ route('login') }}">Log In</a>
    </li>
    @if (SiteVisitor::inTheSpace())
    <li class="nav-item d-none d-md-block {{ Route::currentRouteName() == 'registerInterest' ? 'active': '' }}">
      <a class="nav-link" href="{{ route('registerInterest') }}">Register Interest</a>
    </li>
    @endif
  </ul>
  @endguest

  {{-- right side user toggler --}}
  @auth
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarUser" aria-controls="navbarUser" aria-expanded="false" aria-label="Toggle navigation">
    <div class="navbar-toggler-icon">{{ Auth::user()->getFirstName() }}&nbsp;<i class="fas fa-caret-down"></i></div>
  </button>
  @endauth

  {{-- build the nav (left side) --}}
  <div class="collapse navbar-collapse" id="navbarMenu">
    <ul class="navbar-nav mr-auto">
      @foreach ($mainNav as $link)
      @if (count($link['links']) > 0)
      <li class="nav-item {!! $link['active'] ? 'active' : '' !!} dropdown">
        <a class="nav-link dropdown-toggle" data-toggle="dropdown" aria-haspopups="true" aria-expanded="false" href="{{ $link['url'] }}">{{ $link['text'] }}</a>
        <div class="dropdown-menu">
          @foreach ($link['links'] as $subLink)
          <a class="dropdown-item {!! $subLink['active'] ? 'active' : '' !!}" href="{{ $subLink['url'] }}">{{ $subLink['text'] }}</a>
          @endforeach
        </div>
      </li>
      @else
      <li class="nav-item {!! $link['active'] ? 'active' : '' !!}">
        <a class="nav-link" href="{{ $link['url'] }}">{{ $link['text'] }}</a>
      </li>
      @endif
      @endforeach

      @guest
      {{-- Register --}}
      @if (SiteVisitor::inTheSpace())
      <li class="nav-item d-md-none {{ Route::currentRouteName() == 'registerInterest' ? 'active': '' }}"><a class="nav-link" href="{{ route('registerInterest') }}">Register Interest</a></li>
      @endif
      @endguest
    </ul>
  </div>

  {{-- right side user --}}
  @auth
  <div class="collapse navbar-collapse" id="navbarUser">
    {{-- user drop down --}}
    <ul class="navbar-nav ml-auto d-none d-md-flex">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="navbar-text d-none d-lg-inline">Logged in as&nbsp;</span>{{ Auth::user()->getFirstName() }}</a>
          <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{ route('users.show', Auth::user()->getId()) }}">Update Details</a>
            <a class="dropdown-item" href="{{ route('users.changePassword') }}">Change Password</a>
            <a class="dropdown-item" href="{{ route('bank-transactions.index') }}">Standing Order details</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
    </ul>

    {{-- user collapsed --}}
    <ul class="navbar-nav d-md-none text-right">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('users.show', Auth::user()->getId()) }}">Update Details</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('users.changePassword') }}">Change Password</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('bank-transactions.index') }}">Standing Order details</a>
        </li>
        <li class="nav-item"><hr class="border-top-3"></li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
    </ul>
  </div>
  @endauth
</nav>
<!-- navbar end -->
