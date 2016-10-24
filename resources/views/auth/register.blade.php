@extends('layouts.app')

@section('content')
<h2>Register</h2>

<form role="form" method="POST" action="{{ url('/register') }}">
  {{ csrf_field() }}

  <div class="row">
    <label for="name" class="form-label">Name</label>
    <div class="form-control">
      <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
      @if ($errors->has('name'))
      <span class="help-block">
        <strong>{{ $errors->first('name') }}</strong>
      </span>
      @endif
    </div>
  </div>

  <div class="row">
    <label for="username" class="form-label">Username</label>
    <div class="form-control">
      <input id="username" type="text" name="username" value="{{ old('username') }}" required>
      @if ($errors->has('username'))
      <p class="help-text">
        <strong>{{ $errors->first('username') }}</strong>
      </p>
      @endif
    </div>
  </div>

  <div class="row">
    <label for="email" class="form-label">E-Mail Address</label>
    <div class="form-control">
      <input id="email" type="email" name="email" value="{{ old('email') }}" required>
      @if ($errors->has('email'))
      <p class="help-text">
        <strong>{{ $errors->first('email') }}</strong>
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
    <label for="password-confirm" class="columns small-12 medium-2">Confirm Password</label>
    <div class="columns small-12 medium-10">
      <input id="password-confirm" type="password" name="password_confirmation" required>

      @if ($errors->has('password_confirmation'))
      <p class="help-text">
        {{ $errors->first('password_confirmation') }}
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
</div>
@endsection
