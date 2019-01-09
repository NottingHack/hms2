@extends('layouts.app')

@section('pageTitle', 'Reset Password')

@section('content')
<div class="container">
  <p>Choose a new password for your HMS account.</p>

  <form role="form" method="POST" action="{{ url('/password/reset') }}">
    {{ csrf_field() }}

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
      <label for="email" class="form-label">E-Mail Address</label>
      <input class="form-control" id="email" type="email" name="email" value="{{ $email ?? old('email') }}" autocomplete="username" required autofocus>
      @if ($errors->has('email'))
      <span class="help-block">
        <strong>{{ $errors->first('email') }}</strong>
      </span>
      @endif
    </div>

    <div class="form-group">
      <label for="password" class="form-label">New Password</label>
      <input class="form-control" id="password" name="password" type="password" required autocomplete="new-password">
      @if ($errors->has('password'))
      <span class="help-block">
        <strong>{{ $errors->first('password') }}</strong>
      </span>
      @endif
    </div>

    <div class="form-group">
      <label for="password-confirm" class="form-label">Confirm Password</label>
      <input class="form-control" id="password-confirm" name="password_confirmation" type="password" required autocomplete="new-password">
      @if ($errors->has('password_confirmation'))
      <span class="help-block">
        <strong>{{ $errors->first('password_confirmation') }}</strong>
      </span>
      @endif
    </div>

    <button type="submit" class="btn btn-primary">Reset Password</button>
  </form>
</div>
@endsection
