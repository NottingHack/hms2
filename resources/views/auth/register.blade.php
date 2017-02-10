@extends('layouts.app')

@section('content')
<h2>Register</h2>

<form role="form" method="POST" action="{{ url('/register') }}">
  {{ csrf_field() }}
  <input type="hidden" name="invite" value="{{ old('invite', $invite) }}" >
  @if ($errors->has('invite'))
  <div>
      <span class="help-block">
        <strong>{{ $errors->first('invite') }}</strong>
      </span>
  </div>
  @endif

  <p>Thank you for showing your interest in becoming a member of Nottingham Hackspace, please fill in the fields below to create your account. Once submitted your details will be reviewed by a member of our Membership Team (this normally takes less than 24 hours). Once reviewed you will be emailed details on the next step to becoming a member.</p>

  <div class="form-container">
    <div class="row">
      <label for="firstname" class="form-label">First name</label>
      <div class="form-control">
        <input id="firstname" type="text" name="firstname" value="{{ old('firstname') }}" required autofocus>
        @if ($errors->has('firstname'))
        <span class="help-block">
          <strong>{{ $errors->first('firstname') }}</strong>
        </span>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="lastname" class="form-label">Last name</label>
      <div class="form-control">
        <input id="lastname" type="text" name="lastname" value="{{ old('lastname') }}" required>
        @if ($errors->has('lastname'))
        <span class="help-block">
          <strong>{{ $errors->first('lastname') }}</strong>
        </span>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="username" class="form-label">Username</label>
      <div class="form-control">
        <input id="username" type="text" name="username" value="{{ old('username') }}" required>
        @if ($errors->has('username'))
        <p class="help-text">
          <strong>{{ $errors->first('username') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="email" class="form-label">E-Mail Address</label>
      <div class="form-control">
        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>
        @if ($errors->has('email'))
        <p class="help-text">
          <strong>{{ $errors->first('email') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="password" class="form-label">Password</label>
      <div class="form-control">
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @if ($errors->has('password'))
        <p class="help-text">
          <strong>{{ $errors->first('password') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="password-confirm" class="columns small-12 medium-2">Confirm Password</label>
      <div class="columns small-12 medium-10">
        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        @if ($errors->has('password_confirmation'))
        <p class="help-text">
          {{ $errors->first('password_confirmation') }}
        </p>
        @endif
      </div>
    </div>
  </div>

  <p>Nottingham Hackspace is an incorporated non-profit, run entirely by members. As such, we have to maintain a membership register for inspection by Companies House. Any information you provide won't be used for anything other than hackspace business, and certainly won't be passed on or sold to any third parties.</p>

  <div class="form-container">
    <div class="row">
      <label for="address1" class="form-label">Address 1</label>
      <div class="form-control">
        <input id="address1" type="text" name="address1" value="{{ old('address1') }}" required autocomplete="address-line1">
        @if ($errors->has('address1'))
        <p class="help-text">
          <strong>{{ $errors->first('address1') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="address2" class="form-label">Address 2</label>
      <div class="form-control">
        <input id="address2" type="text" name="address2" value="{{ old('address2') }}" autocomplete="address-line2">
        @if ($errors->has('address2'))
        <p class="help-text">
          <strong>{{ $errors->first('address2') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="address3" class="form-label">Address 3</label>
      <div class="form-control">
        <input id="address3" type="text" name="address3" value="{{ old('address3') }}" autocomplete="address-line3">
        @if ($errors->has('address3'))
        <p class="help-text">
          <strong>{{ $errors->first('address3') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="addressCity" class="form-label">City</label>
      <div class="form-control">
        <input id="addressCity" type="text" name="addressCity" value="{{ old('addressCity') }}" required autocomplete="city">
        @if ($errors->has('addressCity'))
        <p class="help-text">
          <strong>{{ $errors->first('addressCity') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="addressCounty" class="form-label">County</label>
      <div class="form-control">
        <input id="addressCounty" type="text" name="addressCounty" value="{{ old('addressCounty') }}" required autocomplete="region">
        @if ($errors->has('addressCounty'))
        <p class="help-text">
          <strong>{{ $errors->first('addressCounty') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="addressPostcode" class="form-label">Postcode</label>
      <div class="form-control">
        <input id="addressPostcode" type="text" name="addressPostcode" value="{{ old('addressPostcode') }}" required autocomplete="postal-code">
        @if ($errors->has('addressPostcode'))
        <p class="help-text">
          <strong>{{ $errors->first('addressPostcode') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <label for="contactNumber" class="form-label">Contact Number</label>
      <div class="form-control">
        <input id="telephone" type="text" name="contactNumber" value="{{ old('contactNumber') }}" required autocomplete="tel">
        @if ($errors->has('contactNumber'))
        <p class="help-text">
          <strong>{{ $errors->first('contactNumber') }}</strong>
        </p>
        @endif
      </div>
    </div>

    <div class="row">
      <div class="form-buttons">
        <button type="submit" class="button">
          Register
        </button>
      </div>
    </div>
  </div>
</form>
@endsection
