@extends('layouts.app')

@section('pageTitle', 'Reset Password')

<!-- Main Content -->
@section('content')
<div class="container">
  @if (session('status'))
  <div class="alert alert-primary" role="alert">
    {{ session('status') }}
  </div>
  @endif

  <p>Enter the email address you registered with to receive a link which will allow you to reset your password.</p>

  <form role="form" method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="form-group">
      <label class="form-label" for="email">E-Mail Address</label>
      <input class="form-control" id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
      @if ($errors->has('email'))
      <span class="help-block">
        <strong>{{ $errors->first('email') }}</strong>
      </span>
      @endif
    </div>
    <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
  </form>
</div>
@endsection
