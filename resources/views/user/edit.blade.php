@extends('layouts.app')

@section('pageTitle', $user->getFullName())

@section('content')
<form id="user-edit-form" role="form" method="POST" action="{{ route('users.update', $user->getId()) }}">
  {{ csrf_field() }}
  {{ method_field('PUT') }}

  <p>Usernames can not be change</p>

  <div class="form-container">
    <div class="row">
      <label for="firstname" class="form-label">First name</label>
      <div class="form-control">
        <input id="firstname" type="text" name="firstname" value="{{ old('firstname', $user->getFirstname()) }}" required autofocus>
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
        <input id="lastname" type="text" name="lastname" value="{{ old('lastname', $user->getLastname()) }}" required>
        @if ($errors->has('lastname'))
        <span class="help-block">
          <strong>{{ $errors->first('lastname') }}</strong>
        </span>
        @endif
      </div>
    </div>
  </div>
  <p>Its important to keep all you contact details up to date.</p>
  <div class="form-container">
    <div class="row">
      <label for="email" class="form-label">E-Mial Address</label>
      <div class="form-control">
        <input id="email" type="text" name="email" value="{{ old('email', $user->getEmail()) }}" required>
        @if ($errors->has('email'))
        <span class="help-block">
          <strong>{{ $errors->first('email') }}</strong>
        </span>
        @endif
      </div>
    </div>

    @if ($user->getProfile())
      <div class="row">
        <label for="address1" class="form-label">Address 1</label>
        <div class="form-control">
          <input id="address1" type="text" name="address1" value="{{ old('address1', $user->getProfile()->getAddress1()) }}" required autocomplete="address-line1">
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
          <input id="address2" type="text" name="address2" value="{{ old('address2', $user->getProfile()->getAddress2()) }}" autocomplete="address-line2">
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
          <input id="address3" type="text" name="address3" value="{{ old('address3', $user->getProfile()->getAddress3()) }}" autocomplete="address-line3">
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
          <input id="addressCity" type="text" name="addressCity" value="{{ old('addressCity', $user->getProfile()->getAddressCity()) }}" required autocomplete="city">
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
          <input id="addressCounty" type="text" name="addressCounty" value="{{ old('addressCounty', $user->getProfile()->getAddressCounty()) }}" required autocomplete="region">
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
          <input id="addressPostcode" type="text" name="addressPostcode" value="{{ old('addressPostcode', $user->getProfile()->getAddressPostcode()) }}" required autocomplete="postal-code">
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
          <input id="telephone" type="text" name="contactNumber" value="{{ old('contactNumber', $user->getProfile()->getContactNumber()) }}" required autocomplete="tel">
          @if ($errors->has('contactNumber'))
          <p class="help-text">
            <strong>{{ $errors->first('contactNumber') }}</strong>
          </p>
          @endif
        </div>
      </div>
    </div>

    <p>Date of birth is not required unless you are under 18.</p>
    <div class="form-container">
      <div class="row">
        <label for="dateOfBirth" class="form-label">Date Of Birth (yyyy-mm-dd)</label>
        <div class="form-control">
          <input id="dateOfBirth" type="date" name="dateOfBirth" value="{{ old('dateOfBirth', $user->getProfile()->getDateOfBirth() ? $user->getProfile()->getDateOfBirth()->toDateString() : '') }}">
          @if ($errors->has('dateOfBirth'))
          <p class="help-text">
            <strong>{{ $errors->first('dateOfBirth') }}</strong>
          </p>
          @endif
        </div>
      </div>
    </div>

    <p>The unlock text is displayed by gatekeeper when opening a door.</p>
    <div class="form-container">
      <div class="row">
        <label for="unlockText" class="form-label">Unlock Text</label>
        <div class="form-control">
          <input id="unlockText" type="text" name="unlockText" value="{{ old('unlockText', $user->getProfile()->getUnlockText()) }}">
          @if ($errors->has('unlockText'))
          <p class="help-text">
            <strong>{{ $errors->first('unlockText') }}</strong>
          </p>
          @endif
        </div>
      </div>
    @endif

    <div class="row">
      <div class="form-buttons">
        <button type="submit" class="button">
          Update
        </button>
      </div>
    </div>
  </div>
</form>

@endsection
