@extends('layouts.app')

@section('pageTitle', 'Edit Self Book Global Settings')

@section('content')
<div class="container">
  <h2>Self Book Global Settings</h2>
  <hr>
  <form role="form" method="POST" action="{{ route('gatekeeper.access-state.update') }}">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label for="maxLength" class="form-label">Max booking length</label>
      <input class="form-control @error('maxLength') is-invalid @enderror" id="maxLength" type="number" name="maxLength" value="{{ old('maxLength', $maxLength) }}" aria-describedby="maxLengthHelpBlock" required autofocus>
      <small id="maxLengthHelpBlock" class="form-text text-muted">
        Minutes
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('maxLength') }}
      </div>
    </div>

    <div class="form-group">
      <label for="maxConcurrentPerUser" class="form-label">Max concurrent bookings per user</label>
      <input class="form-control @error('maxConcurrentPerUser') is-invalid @enderror" id="maxConcurrentPerUser" type="number" name="maxConcurrentPerUser" value="{{ old('maxConcurrentPerUser', $maxConcurrentPerUser) }}" required>
      <div class="invalid-feedback">
        {{ $errors->first('maxConcurrentPerUser') }}
      </div>
    </div>

    <div class="form-group">
      <label for="maxGuestsPerUser" class="form-label">Max guests per user</label>
      <input class="form-control @error('maxGuestsPerUser') is-invalid @enderror" id="maxGuestsPerUser" type="number" name="maxGuestsPerUser" value="{{ old('maxGuestsPerUser', $maxGuestsPerUser) }}" required>
      <div class="invalid-feedback">
        {{ $errors->first('maxGuestsPerUser') }}
      </div>
    </div>

    <div class="form-group">
      <label for="minPeriodBetweenBookings" class="form-label">Min period between bookings</label>
      <input class="form-control @error('minPeriodBetweenBookings') is-invalid @enderror" id="minPeriodBetweenBookings" type="number" name="minPeriodBetweenBookings" value="{{ old('minPeriodBetweenBookings', $minPeriodBetweenBookings) }}" aria-describedby="minPeriodBetweenHelpBlock" required>
      <small id="minPeriodBetweenHelpBlock" class="form-text text-muted">
        Minutes
      </small>
      <div class="invalid-feedback">
        {{ $errors->first('minPeriodBetweenBookings') }}
      </div>
    </div>

    <div class="form-group">
      <label for="bookingInfoText" class="form-label">Booking info text</label>
      <input class="form-control @error('bookingInfoText') is-invalid @enderror" id="bookingInfoText" type="text" name="bookingInfoText" value="{{ old('bookingInfoText', $bookingInfoText) }}" maxlength="255" required>
      <div class="invalid-feedback">
        {{ $errors->first('bookingInfoText') }}
      </div>
    </div>

    <br>
    <hr>
    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block">Save</button>
    </div>
  </form>
</div>
@endsection
