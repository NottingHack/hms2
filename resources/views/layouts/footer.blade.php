<!-- footer -->
<footer>
  <div class="footer">
    <div class="col-sm-3">
      <h5>HMS {{ $version }}</h5>
      <ul>
        <li><a href="#">Get Source</a></li>
        <li><a href="#">Credits</a></li>
        <li><a href="#">Nottinghack Website</a></li>
        <li>© {{ \Carbon\Carbon::now()->year }} Nottinghack</li>
        @if (config('app.env') != 'production')
        <li>
          <span class="d-block d-sm-none">Breakpoint: xs</span>
          <span class="d-none d-sm-block d-md-none">Breakpoint: sm</span>
          <span class="d-none d-md-block d-lg-none">Breakpoint: md</span>
          <span class="d-none d-lg-block d-xl-none">Breakpoint: lg</span>
          <span class="d-none d-xl-block">Breakpoint: xl</span>
        </li>
        @endif
      </ul>
    </div>
    <div class="col-sm-3">
      <h5>About us</h5>
      <ul>
        <li><a href="#">Company Information</a></li>
        <li><a href="#">Contact us</a></li>
        <li><a href="#">Reviews</a></li>
      </ul>
    </div>
    <div class="col-sm-3 d-none d-sm-block">
      <h5>Address</h5>
      <ul>
        <li>Unit F6 Bizspace</li>
        <li>Roden House</li>
        <li>Business Centre</li>
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
      </ul>
    </div>
    <div class="w-100"><hr></div>
    <div class="col social-networks">
      <a href="#" class="twitter"><i class="fab fa-twitter"></i></a>
      <a href="#" class="facebook"><i class="fab fa-facebook"></i></a>
      <a href="#" class="google"><i class="fab fa-google-plus-g"></i></a>
      <a href="#" class="flickr"><i class="fab fa-flickr"></i></a>
      <a href="#" class="youtube"><i class="fab fa-youtube"></i></a>
    </div>
  </div>
</footer>
<!-- footer end -->
