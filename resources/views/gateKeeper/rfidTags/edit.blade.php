@extends('layouts.app')

@section('pageTitle')
Rfid card for {{ $rfidTag->getUser()->getFirstname() }}
@endsection

@section('content')
<form role="form" method="POST" action="{{ route('rfid-tags.update', $rfidTag->getId()) }}">
  {{ csrf_field() }}
  {{ method_field('PATCH') }}
  <div class="row">
    <label for="rfidSerial" class="form-label">Card Serial</label>
    <div class="form-control">
        {{ $rfidTag->getBestRfidSerial() }}
    </div>
  </div>

  <div class="row">
    <label for="lasetUsed" class="form-label">Last Used</label>
    <div class="form-control">
        {{ $rfidTag->getLastUsed() ? $rfidTag->getLastUsed()->toDateTimeString() : '' }}
    </div>
  </div>

  <div class="row">
    <label for="friendlyName" class="form-label">Card Name</label>
    <div class="form-control">
      <input id="friendlyName" type="text" name="friendlyName" value="{{ old('friendlyName', $rfidTag->getFriendlyName()) }}" autofocus>
      @if ($errors->has('friendlyName'))
      <p class="help-text">
        <strong>{{ $errors->first('friendlyName') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <label for="state" class="form-label">State</label>
    <div class="form-control">
      <select id="state" name="state" class="select2-basic-single" required>
        @foreach ($rfidTag->statusStrings as $value => $string)
        <option value="{{ $value }}" {{ old('state', $rfidTag->getState()) == $value ? 'selected' : '' }}>{{ $string }}</option>
        @endforeach
      </select>
      @if ($errors->has('state'))
      <p class="help-text">
        <strong>{{ $errors->first('state') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="form-buttons">
      <button type="submit" class="button">
        Update
      </button>
    </div>
  </div>
</form>

@endsection
