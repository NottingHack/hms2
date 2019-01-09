<!-- navbar -->
<nav class="row navbar navbar-expand-md justify-content-between">
  @if (!Auth::guest() and isset($mainNav) )
  {{-- build the nav toggler --}}
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <div class="navbar-toggler-icon"><i class="fas fa-bars"></i>&nbsp;Menu</div>
  </button>
  {{-- build the nav --}}
  <div class="collapse navbar-collapse" id="navbarSupportedContent">
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
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><hr class="border-top"></li>
      <li class="navbar-text d-md-none d-lg-block">Logged in as {{ Auth::user()->getFirstName() }} @if (Auth::viaRemember()) (via Remember Me) @endif</li>
      <li class="nav-item">
        <a class="nav-link" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Log Out</a>
        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
          {{ csrf_field() }}
        </form>
      </li>
    </ul>
  </div>
  @elseif (Auth::guest())
  <ul class="navbar-nav ml-auto">
    <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Log In</a></li>
    @if (SiteVisitor::inTheSpace())
    <li class="nav-item"><a class="nav-link" href="{{ route('registerInterest') }}">Register Interest</a></li>
    @endif
  </ul>
  @endif
</nav>
<!-- navbar end -->
