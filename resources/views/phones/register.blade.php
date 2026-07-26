@extends('layouts.app')

@section('pageTitle', 'Register a new extension')

@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('phones.extensions.doRegister') }}">
    @csrf
    <div class="form-group">
      <label for="extension" class="form-label">Extension</label>
      <input id="extension" class="form-control @error('extension') is-invalid @enderror" type="text" name="extension" placeholder="Your extension (e.g. 1234)" required autofocus maxlength="5" value="{{ old('extension') }}">
      <small id="extensionHelpBlock" class="form-text text-muted">
      This is the 3 or 4 digit number which other members can dial to reach you.
      </small>
      @error('extension')
      <p class="help-text">
        <strong>{{ $errors->first('extension') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for="phoneword" class="form-label">Phoneword</label>
      <input id="phoneword" class="form-control @error('phoneword') is-invalid @enderror" type="text" name="phoneword" placeholder="(optional) phoneword for your number" autofocus maxlength="5" value="{{ old('phoneword') }}">
      <small id="phonewordHelpBlock" class="form-text text-muted">
      Phonewords are short words which can be typed using the letters on your phone's keypad are sometimes used to help remember phone numbers. For example, 9327 could be remembered as YEAR.
      </small>
      @error('phoneword')
      <p class="help-text">
        <strong>{{ $errors->first('phoneword') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for="type" class="form-label">Connection Type</label>
      {{ old('type') }}
      <select id="type" name="type" class="form-control">
        @foreach ($types as $type => $description)
        <option value="{{ $type }}" @if (old('type') === $type) selected @endif>{{ $description }}</option>
        @endforeach
      </select>
      <small id="typeHelpBlock" class="form-text text-muted">
        You can connect to the phone network in several different ways.
        <ul>
          <li><strong>SIP</strong> is a commonly used Voice over IP (VoIP) protocol and is supported by IP desk phones, as well as software phones (softphones) on your current mobile device or computer</li>
          <li><strong>DECT</strong> phones are wireless handsets, similar to what you may be using at home.</li>
          <li><strong>POTS</strong> is short for Plain Old Telephone Service, and is a copper pair to which an analogue phone can be connected. This can be a rotary dial or tone (DTFM) dial phone.</li>
          <li><strong>Custom</strong> allows you to reserve a number for a custom configuration on the Asterisk server.</li>
        </ul>
      </small>
      @error('type')
      <p class="help-text">
        <strong>{{ $errors->first('type') }}</strong>
      </p>
      @enderror
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <input id="description" class="form-control @error('description') is-invalid @enderror" type="text" name="description" placeholder="Description (e.g. your name and the service type)" required autofocus maxlength="200" value="{{ old('description') }}">
      <small id="descriptionHelpBlock" class="form-text text-muted">
        This is the description which will be displayed next to your phone number in the directory. Your real name (from your HMS profile) is not published, so you may want to put that here, or your username, or something else people will recognise.
      </small>
      @error('description')
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block">Register</button>
  </form>
</div>
@endsection
