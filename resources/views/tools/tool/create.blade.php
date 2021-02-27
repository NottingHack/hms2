@extends('layouts.app')

@section('pageTitle', 'Add new tool')

@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('tools.store') }}">
    @csrf
    <div class="form-group">
      <label for="toolName" class="form-label">Name</label>
      <input id="toolName" class="form-control @error('toolName') is-invalid @enderror" type="text" name="toolName" placeholder="Name of Tool" value="{{ old('toolName') }}" required autofocus maxlength="20">
      <small id="toolNameHelpBlock" class="form-text text-muted">
        Name as used for MQTT topic and when setting up the arduino
      </small>
      @error('toolName')
      <p class="help-text">
        <strong>{{ $errors->first('toolName') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for="displayName" class="form-label">Display name</label>
      <input id="displayName" class="form-control @error('displayName') is-invalid @enderror" type="text" name="displayName" placeholder="Name of Tool" value="{{ old('displayName') }}" required maxlength="100">
      <small id="displayNameHelpBlock" class="form-text text-muted">
        Name for display in HMS
      </small>
      @error('displayName')
      <p class="help-text">
        <strong>{{ $errors->first('displayName') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group form-check">
      <input id="restricted" class="form-check-input" type="checkbox" name="restricted" {{ old('restricted', false) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="restricted">
        Do members require induction to use this tools?
      </label>
    </div>

    <div class="form-group">
      <label for='cost', class="form-label">Cost per hour in pence</label>
      <div class="input-group">
        <input id="cost" class="form-control @error('cost') is-invalid @enderror" type="number" name="cost" value="{{ old('cost', 0) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">p</span>
        </div>
      </div>
      @error('cost')
      <p class="help-text">
        <strong>{{ $errors->first('cost') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for='bookingLength', class="form-label">Default booking length for this tool</label>
      <div class="input-group">
        <input id="bookingLength" class="form-control @error('bookingLength') is-invalid @enderror" type="number" name="bookingLength"value="{{ old('bookingLength', 30) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">minutes</span>
        </div>
      </div>
      @error('bookingLength')
      <p class="help-text">
        <strong>{{ $errors->first('bookingLength') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for='lengthMax', class="form-label">Maximum amount of time a booking can be made for</label>
      <div class="input-group">
        <input id="lengthMax" class="form-control @error('lengthMax') is-invalid @enderror" type="number" name="lengthMax" value="{{ old('lengthMax', 120) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">minutes</span>
        </div>
      </div>
      @error('lengthMax')
      <p class="help-text">
        <strong>{{ $errors->first('lengthMax') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for='bookingsMax', class="form-label">Maximum number of bookings a user can have at any one time</label>
      <input id="bookingsMax" class="form-control @error('bookingsMax') is-invalid @enderror" type="number" name="bookingsMax" value="{{ old('bookingsMax', 1) }}" required>
      @error('bookingsMax')
      <p class="help-text">
        <strong>{{ $errors->first('bookingsMax') }}</strong>
      </p>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block">Add Tool</button>
  </form>
</div>
@endsection
