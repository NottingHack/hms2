<!-- footer -->
<footer>
  <div class="footer my-3">
    <div class="col-sm-3">
      <h5>HMS {{ $version }}</h5>
      <ul>
        <li><a href="https://github.com/NottingHack/hms2" target="_blank">Get Source</a></li>
        <li><a href="{{ route('credits') }}">Credits</a></li>
        <li><a href="https://nottinghack.org.uk" target="_blank">Nottinghack Website</a></li>
        <li>© {{ Carbon\Carbon::now()->year }} Nottinghack</li>
        @if (config('app.env') != 'production')
        <li>
          <span class="d-block d-sm-none">Breakpoint: xs</span>
          <span class="d-none d-sm-block d-md-none">Breakpoint: sm</span>
          <span class="d-none d-md-block d-lg-none">Breakpoint: md</span>
          <span class="d-none d-lg-block d-xl-none">Breakpoint: lg</span>
          <span class="d-none d-xl-block">Breakpoint: xl</span>
        </li>
        <li>Larvel Version {{ app()->version() }}</li>
        @endif
      </ul>
    </div>
    <div class="col-sm-3">
      <h5>About us</h5>
      <ul>
        <li><a href="{{ route('companyInformation') }}">Company Information</a></li>
        <li><a href="{{ route('contactUs') }}">Contact us</a></li>
        <li><a href="{{  Meta::get('rules_html') }}" target="_blank">Rules</a></li>
        <li><a href="{{  Meta::get('members_guide_html') }}" target="_blank">Members Guide</a></li>
        <li><a href="{{ route('instrumentation.status') }}">Instrumentation Status</a></li>
      </ul>
    </div>
    <div class="col-sm-3 d-none d-sm-block">
      <h5>Address</h5>
      <ul>
        <li>Unit F6 Roden House</li>
        <li>Roden Street</li>
        <li>Nottingham</li>
        <li>NG3 1JH</li>
      </ul>
    </div>
    <div class="col-sm-3">
      <h5>Legal</h5>
      <ul>
        <li>Nottingham Hackspace Ltd</li>
        <li>No. 07766826</li>
        <li>Reg. in England & Wales</li>
        <li><a href="{{ route('privacy-and-terms') }}">Privacy & Terms</a></li>
        <li><a href="{{ route('cookie-policy') }}">Cookie Policy</a></li>
      </ul>
    </div>
    <div class="w-100"><hr></div>
    <div class="col social-networks">
      <a href="https://twitter.com/HSNOTTS" class="twitter" target="_blank"><i class="fab fa-twitter"></i></a>
      <a href="https://www.facebook.com/nottinghack/" class="facebook" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://groups.google.com/group/nottinghack?hl=en" class="google" target="_blank"><i class="fab fa-google-plus-g"></i></a>
      <a href="https://www.flickr.com/photos/nottinghack" class="flickr" target="_blank"><i class="fab fa-flickr"></i></a>
      <a href="https://www.youtube.com/user/nottinghack" class="youtube" target="_blank"><i class="fab fa-youtube"></i></a>
    </div>
  </div>
</footer>
<!-- footer end -->
