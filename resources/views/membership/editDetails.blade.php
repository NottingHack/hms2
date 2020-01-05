@extends('layouts.app')

@section('pageTitle', 'Update Details')

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">
      @isset($rejectedLog)
      The Membership Team has looked over your information and have asked for a change, giving the following reason.<br>
      <div class="alert alert-warning" role="alert">{{ $rejectedLog->getReason() }}</div>
      Use the form below to update your details as needed.<br>
      Once updated a new review will be automatically requested and the team will give them another quick check, and if all is well they'll move your membership on to the next stage.<br>
      This normally takes no more than 48 hours.
      @else
      If needed you can update your details below.
      @endisset
    </div>

    <form id="membership-edit-details-form" role="form" method="POST" action="{{ route('membership.update', $user->getId()) }}">
      @csrf
      @method('PUT')

      <div class="card-body">
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
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block">Update and request review</button>
      </div>
    </form>
  </div>
</div>
@endsection
