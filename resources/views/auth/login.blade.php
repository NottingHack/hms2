@extends('layouts.app')

@section('content')

<h2>Log In</h2>

<p>Enter your email address or username and password to log in.</p>

<form role="form" method="POST" action="{{ url('/login') }}">
  {{ csrf_field() }}

  <div class="row">
    <label for="login" class="form-label">E-Mail or Username</label>
    <div class="form-control">
      <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus>

      @if ($errors->has('login'))
      <p class="help-text">
        <strong>{{ $errors->first('login') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <label for="password" class="form-label">Password</label>
    <div class="form-control">
      <input id="password" type="password" name="password" required>
      @if ($errors->has('password'))
      <p class="help-text">
        <strong>{{ $errors->first('password') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <label class="form-control medium-offset-2">
      <input type="checkbox" name="remember"> Remember Me
    </label>
  </div>

  <div class="row">
    <div class="form-buttons">
      <button type="submit" class="button">
        Log In
      </button>

      <a class="button secondary" href="{{ url('/password/reset') }}">
        Forgot Your Password?
      </a>
    </div>
  </div>
</form>
@endsection
