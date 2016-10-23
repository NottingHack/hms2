@extends('layouts.app')

@section('content')

<form role="form" method="POST" action="{{ url('/login') }}">
    {{ csrf_field() }}

    <div class="{{ $errors->has('email') ? ' has-error' : '' }}">
  	<label>E-Mail Address
      <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
    </label>

    @if ($errors->has('email'))
    <span class="help-block">
      <strong>{{ $errors->first('email') }}</strong>
    </span>
    @endif
  </div>

  <div class="{{ $errors->has('password') ? ' has-error' : '' }}">
    <label>Password
      <input id="password" type="password" class="form-control" name="password" required>
    </label>

    @if ($errors->has('password'))
    <span class="help-block">
      <strong>{{ $errors->first('password') }}</strong>
    </span>
    @endif
  </div>

  <div class="col-md-6 col-md-offset-4">
    <label>
      <input type="checkbox" name="remember"> Remember Me
    </label>
  </div>

  <div>
    <button type="submit" class="button">
      Login
    </button>

    <a class="button secondary" href="{{ url('/password/reset') }}">
      Forgot Your Password?
    </a>
  </div>
</form>
@endsection
