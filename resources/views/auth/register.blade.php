@extends('layouts.app')

@section('pageTitle', 'Register')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header text-center">
      <svg version="1.1" x="0px" y="0px" width="75" height="75" viewBox="0 0 220 220" enable-background="new 0 0 223.489 220.489" id="logo" class="mb-2" style="fill: #195905;">
        <path id="logo-path" d="m 187.42396,32.576354 c -42.87821,-42.874396 -112.393767,-42.874396 -155.264347,0 -42.879484,42.878211 -42.879484,112.391236 0,155.266896 42.87058,42.87567 112.389957,42.87567 155.264347,0 42.87567,-42.87566 42.87567,-112.388685 0,-155.266896 z m -34.95429,114.891786 -25.04366,-25.03984 7.87686,-7.87687 -4.16546,-4.1642 -21.17074,21.17201 4.16037,4.16801 8.15287,-8.15668 25.05002,25.04113 -37.53878,37.53878 -25.046204,-25.04239 4.272304,-4.26849 -29.852708,-29.85653 -4.277392,4.27358 -25.039847,-25.04366 37.540057,-37.540065 25.041119,25.041119 -8.157951,8.155416 5.127019,5.12575 21.177093,-21.17329 -5.12448,-5.125747 -7.881947,7.880677 -25.042386,-25.043663 37.266593,-37.260239 25.0373,25.041119 -4.26721,4.273576 29.85144,29.853979 4.27485,-4.273576 25.04111,25.044944 z"></path>
      </svg>
      <h4>Create account</h4>
    </div>

    <form id="register-form" role="form" method="POST" action="{{ url('/register') }}">
      @csrf
      <div class="card-body">
        <input type="hidden" name="invite" value="{{ old('invite', $invite) }}">
        @if ($errors->has('invite'))
        <div>
          <span class="from-text">
            <strong>{{ $errors->first('invite') }}</strong>
          </span>
        </div>
        @endif

        <p>Thank you for showing your interest in becoming a member of Nottingham Hackspace, please fill in the fields below to create your account. Once submitted your details will be reviewed by a member of our Membership Team (this normally takes less than 24 hours). Once reviewed you will be emailed details on the next step to becoming a member.</p>

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

        <p>Nottingham Hackspace is an incorporated non-profit, run entirely by members. As such, we have to maintain a membership register for inspection by Companies House. Any information you provide won't be used for anything other than hackspace business, and certainly won't be passed on or sold to any third parties.</p>

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

      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block">Register</button>
      </div>
    </form>
  </div>
</div>
@endsection
