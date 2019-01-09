@extends('layouts.app')

@section('pageTitle')
Rfid card for {{ $rfidTag->getUser()->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('rfid-tags.update', $rfidTag->getId()) }}">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
    <div class="form-group">
      <label for="rfidSerial" class="form-label">Card Serial</label>
      <div class="form-control-plaintext">
          {{ $rfidTag->getBestRfidSerial() }}
      </div>
    </div>

    <div class="form-group">
      <label for="lasetUsed" class="form-label">Last Used</label>
      <div class="form-control-plaintext">
          {{ $rfidTag->getLastUsed() ? $rfidTag->getLastUsed()->toDateTimeString() : '' }}
      </div>
    </div>

    <div class="form-group">
      <label for="friendlyName" class="form-label">Card Name</label>
      <input class="form-control" id="friendlyName" type="text" name="friendlyName" value="{{ old('friendlyName', $rfidTag->getFriendlyName()) }}" autofocus>
      @if ($errors->has('friendlyName'))
      <p class="help-text">
        <strong>{{ $errors->first('friendlyName') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="state" class="form-label">State</label>
      <select id="state" name="state" class="select2-basic-single" style="width: 100%" required>
        @foreach (\HMS\Entities\GateKeeper\RfidTagState::STATE_STRINGS as $value => $string)
        <option value="{{ $value }}" {{ old('state', $rfidTag->getState()) == $value ? 'selected' : '' }}>{{ $string }}</option>
        @endforeach
      </select>
      @if ($errors->has('state'))
      <p class="help-text">
        <strong>{{ $errors->first('state') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <div class="card">
        <button type="submit" class="btn btn-success">
          Update
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
