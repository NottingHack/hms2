@extends('layouts.app')

@section('pageTitle', 'Register')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header text-center">
      @content('logo', 'svg')
      <h4>Create account</h4>
    </div>

    <form id="register-form" role="form" method="POST" action="{{ url('/register') }}">
      @csrf
      <div class="card-body">
        <input type="hidden" name="invite" value="{{ old('invite', $invite) }}">
        <input type="hidden" name="dateOfBirth" value="">
        @if ($errors->has('invite'))
        <div>
          <span class="from-text">
            <strong>{{ $errors->first('invite') }}</strong>
          </span>
        </div>
        @endif

        @content('auth.register', 'user')
        <hr>

        <div class="form-group">
          <input placeholder="First name" class="form-control{{  $errors->has('firstname') ? ' is-invalid' : '' }}" id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required autofocus>
          @if ($errors->has('firstname'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('firstname') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Last name" class="form-control{{  $errors->has('lastname') ? ' is-invalid' : '' }}" id="lastname" type="text" name="lastname" value="{{ old('lastname') }}" required>
          @if ($errors->has('lastname'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('lastname') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Username" class="form-control{{  $errors->has('username') ? ' is-invalid' : '' }}" id="username" type="text" name="username" value="{{ old('username') }}" required>
          @if ($errors->has('username'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('username') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Email Address" class="form-control{{  $errors->has('email') ? ' is-invalid' : '' }}" id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
          @if ($errors->has('email'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Password" class="form-control{{  $errors->has('password') ? ' is-invalid' : '' }}" id="password" type="password" name="password" required autocomplete="new-password">
          @if ($errors->has('password'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Confirm Password" class="form-control{{  $errors->has('password') ? ' is-invalid' : '' }}" id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
          @if ($errors->has('password'))
          <span class="invalid-feedback">
            {{ $errors->first('password') }}
          </span>
          @endif

        </div>

        <hr>
        @content('auth.register', 'profile')
        <hr>

        <div class="form-group">
          <input placeholder="Address 1" class="form-control{{  $errors->has('address1') ? ' is-invalid' : '' }}" id="address1" type="text" name="address1" value="{{ old('address1') }}" required autocomplete="address-line1">
          @if ($errors->has('address1'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('address1') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Address 2" class="form-control{{  $errors->has('address2') ? ' is-invalid' : '' }}" id="address2" type="text" name="address2" value="{{ old('address2') }}" autocomplete="address-line2">
          @if ($errors->has('address2'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('address2') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Address 3" class="form-control{{  $errors->has('address3') ? ' is-invalid' : '' }}" id="address3" type="text" name="address3" value="{{ old('address3') }}" autocomplete="address-line3">
          @if ($errors->has('address3'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('address3') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="City" class="form-control{{  $errors->has('addressCity') ? ' is-invalid' : '' }}" id="addressCity" type="text" name="addressCity" value="{{ old('addressCity') }}" required autocomplete="city">
          @if ($errors->has('addressCity'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('addressCity') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="County" class="form-control{{  $errors->has('addressCounty') ? ' is-invalid' : '' }}" id="addressCounty" type="text" name="addressCounty" value="{{ old('addressCounty') }}" required autocomplete="region">
          @if ($errors->has('addressCounty'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('addressCounty') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Postcode" class="form-control{{  $errors->has('addressPostcode') ? ' is-invalid' : '' }}" id="addressPostcode" type="text" name="addressPostcode" value="{{ old('addressPostcode') }}" required autocomplete="postal-code">
          @if ($errors->has('addressPostcode'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('addressPostcode') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <input placeholder="Contact Number" class="form-control{{  $errors->has('contactNumber') ? ' is-invalid' : '' }}" id="telephone" type="text" name="contactNumber" value="{{ old('contactNumber') }}" required autocomplete="tel">
          @if ($errors->has('contactNumber'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('contactNumber') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-check">
          <input id="agreeToRules" class="form-check-input{{  $errors->has('agreeToRules') ? ' is-invalid' : '' }}" type="checkbox" name="agreeToRules" value="1" required>
          <label for="agreeToRules" class="form-check-label">I have read and agree to the <a href="{{ Meta::get('rules_html') }}">{{ config('branding.space_name') }} rules</a>.</label>
          @if ($errors->has('agreeToRules'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('agreeToRules') }}</strong>
          </span>
          @endif
        </div>

      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block">Register</button>
      </div>
    </form>
  </div>
</div>
@endsection
