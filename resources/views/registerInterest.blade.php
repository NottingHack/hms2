@extends('layouts.app')

@section('content')
<h2>Register Interest</h2>

<p>Enter your email address to register your interest in becoming a Nottinghack member and start the sign up process.</p>

<form role="form" method="POST" action="{{ url('/registerInterest') }}">
  {{ csrf_field() }}

  <div class="row">
    <label for="email" class="form-label">E-Mail</label>
    <div class="form-control">
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

      @if ($errors->has('email'))
      <p class="help-text">
        <strong>{{ $errors->first('email') }}</strong>
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
</form>
@endsection
