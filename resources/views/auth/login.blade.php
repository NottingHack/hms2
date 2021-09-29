@extends('layouts.app')

@section('pageTitle', 'Log In')

@push('scripts')
<script>
  {{-- work around to push and run after jQuery is loaded since --}}
  window.addEventListener('DOMContentLoaded', function() {
    (function($) {
      $('form#login').submit(function(){
          $(this).find(':button#submit').prop('disabled', true);
      });
    })(jQuery);
  });
</script>
@endpush

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col">
      <div class="card card-auth">
        <div class="card-body">
          @content('logo', 'svg')

          <h4>Login</h4>

          <form id="loginForm" role="form" method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="form-group">
              <input id="login" type="text" name="login" value="{{ old('login') }}" class="form-control{{ $errors->has('login') ? ' is-invalid' : '' }}" placeholder="Email address or Username" required autofocus autocomplete="username">
              @if ($errors->has('login'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('login') }}</strong>
              </span>
              @endif
            </div>

            <div class="form-group">
              <input id="password" type="password" name="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" required autocomplete="current-password">
              @if ($errors->has('password'))
              <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
              </span>
              @endif
            </div>

            <button type="submit" class="btn btn-lg btn-info btn-block">Sign in</button>
          </form>
          <br>
          <a href="{{ route('password.request') }}" class="text-info mt-2">
            Forgot your password?
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
