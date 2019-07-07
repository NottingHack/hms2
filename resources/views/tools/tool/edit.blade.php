@extends('layouts.app')

@section('pageTitle', 'Edit '.$tool->getName().' tool')

@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('tools.update', $tool->getId()) }}">
    @csrf
  @method('PATCH')
    <div class="form-group">
      <label for="toolName" class="form-label">Name</label>
      <input id="toolName" class="form-control" type="text" name="toolName" placeholder="Name of Tool" value="{{ old('toolName', $tool->getName()) }}" required autofocus>
      @if ($errors->has('toolName'))
      <p class="help-text">
        <strong>{{ $errors->first('toolName') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group form-check">
      <input id="restricted" class="form-check-input" type="checkbox" name="restricted" {{ old('restricted', $tool->isRestricted()) ? 'checked="checked"' : '' }}>
      <label class="form-check-label" for="restricted">
        Do members require induction to use this tools?
      </label>
    </div>

    <div class="form-group">
      <label for='cost', class="form-label">Cost per hour in pence</label>
      <div class="input-group">
        <input id="cost" class="form-control" type="number" name="cost" value="{{ old('cost', $tool->getPph()) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">p</span>
        </div>
      </div>
      @if ($errors->has('cost'))
      <p class="help-text">
        <strong>{{ $errors->first('cost') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for='bookingLength', class="form-label">Default booking length for this tool</label>
      <div class="input-group">
        <input id="bookingLength" class="form-control" type="number" name="bookingLength"value="{{ old('bookingLength', $tool->getBookingLength()) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">minutes</span>
        </div>
      </div>
      @if ($errors->has('bookingLength'))
      <p class="help-text">
        <strong>{{ $errors->first('bookingLength') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for='lengthMax', class="form-label">Maximum amount of time a booking can be made for</label>
      <div class="input-group">
        <input id="lengthMax" class="form-control" type="number" name="lengthMax" value="{{ old('lengthMax', $tool->getLengthMax()) }}" required>
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">minutes</span>
        </div>
      </div>
      @if ($errors->has('lengthMax'))
      <p class="help-text">
        <strong>{{ $errors->first('lengthMax') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for='bookingsMax', class="form-label">Maximum number of bookings a user can have at any one time</label>
      <input id="bookingsMax" class="form-control" type="number" name="bookingsMax" value="{{ old('bookingsMax', $tool->getBookingsMax()) }}" required>
      @if ($errors->has('bookingsMax'))
      <p class="help-text">
        <strong>{{ $errors->first('bookingsMax') }}</strong>
      </p>
      @endif
    </div>

    <button type="submit" class="btn btn-primary btn-block">Update Tool</button>
  </form>
</div>
@endsection
