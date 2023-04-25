@extends('layouts.app')

@section('pageTitle', $user->getFullname())

@section('content')
<div class="container">
  <div class="card">
    <h5 class="card-header">It is important to keep all your contact details up to date.</h5>
    <form id="user-edit-form" role="form" method="POST" action="{{ route('users.update', $user->getId()) }}">
      @csrf
      @method('PATCH')

      <div class="card-body">
        <label>Username</label>
        <h5>{{ old('username', $user->getUsername()) }}</h5>
        <small id="userName" class="form-text text-muted">Your username cannot be changed.</small>

        <hr>

        <div class="form-group">
          <label for="firstname" class="form-label">First name</label>
          <input placeholder="First name" class="form-control" id="firstname" type="text" name="firstname" value="{{ old('firstname', $user->getFirstname()) }}" required autofocus>
          @if ($errors->has('firstname'))
          <span class="help-block">
            <strong>{{ $errors->first('firstname') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <label for="lastname" class="form-label">Last name</label>
          <input placeholder="Last name" class="form-control" id="lastname" type="text" name="lastname" value="{{ old('lastname', $user->getLastname()) }}" required>
          @if ($errors->has('lastname'))
          <span class="help-block">
            <strong>{{ $errors->first('lastname') }}</strong>
          </span>
          @endif
        </div>

        <div class="form-group">
          <label for="email" class="form-label">Email address</label>
          <input placeholder="Email Address" class="form-control" id="email" type="email" name="email" value="{{ old('email', $user->getEmail()) }}" required>
          @if ($errors->has('email'))
          <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
        </div>

        @if ($user->getProfile())
        <hr>

        <div class="form-group">
          <label for="address1" class="form-label">Address 1</label>
          <input placeholder="Address 1" class="form-control" id="address1" type="text" name="address1" value="{{ old('address1', $user->getProfile()->getAddress1()) }}" required autocomplete="address-line1">
          @if ($errors->has('address1'))
          <p class="help-text">
            <strong>{{ $errors->first('address1') }}</strong>
          </p>
          @endif
        </div>

        <div class="form-group">
          <label for="address2" class="form-label">Address 2</label>
          <input placeholder="Address 2" class="form-control" id="address2" type="text" name="address2" value="{{ old('address2', $user->getProfile()->getAddress2()) }}" autocomplete="address-line2">
          @if ($errors->has('address2'))
          <p class="help-text">
            <strong>{{ $errors->first('address2') }}</strong>
          </p>
          @endif
        </div>

        <div class="form-group">
          <label for="address3" class="form-label">Address 3</label>
          <input placeholder="Address 3" class="form-control" id="address3" type="text" name="address3" value="{{ old('address3', $user->getProfile()->getAddress3()) }}" autocomplete="address-line3">
          @if ($errors->has('address3'))
          <p class="help-text">
            <strong>{{ $errors->first('address3') }}</strong>
          </p>
          @endif
        </div>

        <div class="form-group">
          <label for="addressCity" class="form-label">City</label>
          <input placeholder="City" class="form-control" id="addressCity" type="text" name="addressCity" value="{{ old('addressCity', $user->getProfile()->getAddressCity()) }}" required autocomplete="city">
          @if ($errors->has('addressCity'))
          <p class="help-text">
            <strong>{{ $errors->first('addressCity') }}</strong>
          </p>
          @endif
        </div>

        <div class="form-group">
          <label for="addressCounty" class="form-label">County</label>
          <input placeholder="County" class="form-control" id="addressCounty" type="text" name="addressCounty" value="{{ old('addressCounty', $user->getProfile()->getAddressCounty()) }}" required autocomplete="region">
          @if ($errors->has('addressCounty'))
          <p class="help-text">
            <strong>{{ $errors->first('addressCounty') }}</strong>
          </p>
          @endif
        </div>

        <div class="form-group">
          <label for="addressPostcode" class="form-label">Postcode</label>
          <input placeholder="Postcode" class="form-control" id="addressPostcode" type="text" name="addressPostcode" value="{{ old('addressPostcode', $user->getProfile()->getAddressPostcode()) }}" required autocomplete="postal-code">
          @if ($errors->has('addressPostcode'))
          <p class="help-text">
            <strong>{{ $errors->first('addressPostcode') }}</strong>
          </p>
          @endif
        </div>

        <hr>

        <div class="form-group">
          <label for="contactNumber" class="form-label">Contact Number</label>
          <input placeholder="Contact Number" class="form-control" id="telephone" type="text" name="contactNumber" value="{{ old('contactNumber', $user->getProfile()->getContactNumber()) }}" required autocomplete="tel">
          @if ($errors->has('contactNumber'))
          <p class="help-text">
            <strong>{{ $errors->first('contactNumber') }}</strong>
          </p>
          @endif
        </div>

        <hr>

        <div class="form-group">
          <label for="unlockText" class="form-label">Unlock Text</label>
          <input class="form-control" id="unlockText" type="text" name="unlockText" value="{{ old('unlockText', $user->getProfile()->getUnlockText()) }}">
          @if ($errors->has('unlockText'))
          <p class="help-text">
            <strong>{{ $errors->first('unlockText') }}</strong>
          </p>
          @endif
        </div>

        <hr>

        <div class="form-group">
          <label for="discordUserId" class="form-label">Discord Username</label>
          <input class="form-control" id="discordUserId" type="text" name="discordUserId" value="{{ old('discordUserId', $user->getProfile()->getDiscordUserId()) }}">
	  <small class="form-text text-muted">
	    This can be copied by clicking on your username at the bottom left. It should include the &num; and four digit number.
	  </small>
          @if ($errors->has('discordUserId'))
            <p class="help-text">
              <strong>{{ $errors->first('discordUserId') }}</strong>
            </p>
          @endif
        @endif {{-- userProfile() --}}
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block">Update</button>
      </div>
    </form>
  </div>
</div>
@endsection
