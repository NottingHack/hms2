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
        @if (null != config('services.stripe.key'))
        <li><a href="{{ route('donate') }}">Donate</a></li>
        @endif
        <li><a href="{{  Meta::get('rules_html') }}" target="_blank">Rules</a></li>
        <li><a href="{{  Meta::get('members_guide_html') }}" target="_blank">Members Guide</a></li>
        <li><a href="{{ route('instrumentation.status') }}">Instrumentation Status</a></li>
      </ul>
    </div>
    <div class="col-sm-3 d-none d-sm-block">
      <h5>Address</h5>
      <ul>
        <li>{{ config('branding.address_1') }}</li>
        <li>{{ config('branding.address_2') }}</li>

        @if (config('branding.address_3'))
        <li>{{ config('branding.address_3') }}</li>
        @endif

        <li>{{ config('branding.city') }}</li>

        @if (config('branding.county'))
        <li>{{ config('branding.county') }}</li>
        @endif

        <li>{{ config('branding.postcode') }}</li>
      </ul>
    </div>
    <div class="col-sm-3">
      <h5>Legal</h5>
      <ul>
        <li>{{ config('branding.company_name') }}</li>
        <li>No. {{ config('branding.company_number') }}</li>
        <li>Reg. in England & Wales</li>
        <li><a href="{{ route('privacy-and-terms') }}">Privacy & Terms</a></li>
        <li><a href="{{ route('cookie-policy') }}">Cookie Policy</a></li>
      </ul>
    </div>
    <div class="w-100"><hr></div>
    <div class="col social-networks">
      @foreach (config('branding.social_networks') as $network)
      @if ($network['link'])
      <a href="{{ $network['link'] }}" target="_blank"><i class="{{ $network['icon'] }}"></i></a>
      @endif
      @endforeach
    </div>
  </div>
</footer>
<!-- footer end -->
