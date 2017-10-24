@extends('layouts.app') @section('pageTitle', 'Reset Password') @section('content')

<p>Choose a new password for your HMS account.</p>

<form role="form" method="POST" action="{{ url('/password/reset') }}">
    {{ csrf_field() }}

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="row">
        <label for="email" class="form-label">E-Mail Address</label>
        <div class="form-control">
            <input id="email" type="email" name="email" value="{{ $email or old('email') }}" required autofocus> @if ($errors->has('email'))
            <span class="help-block">
        <strong>{{ $errors->first('email') }}</strong>
      </span> @endif
        </div>
    </div>

    <div class="row">
        <label for="password" class="form-label">Password</label>
        <div class="form-control">
            <input id="password" name="password" type="password" required> @if ($errors->has('password'))
            <span class="help-block">
        <strong>{{ $errors->first('password') }}</strong>
      </span> @endif
        </div>
    </div>

    <div class="row">
        <label for="password-confirm" class="form-label">Confirm Password</label>
        <div class="form-control">
            <input id="password-confirm" name="password_confirmation" type="password" required> @if ($errors->has('password_confirmation'))
            <span class="help-block">
        <strong>{{ $errors->first('password_confirmation') }}</strong>
      </span> @endif
        </div>
    </div>

    <div class="row">
        <div class="form-buttons">
            <button type="submit" class="button">
        Reset Password
      </button>
        </div>
    </div>
</form>
@endsection
