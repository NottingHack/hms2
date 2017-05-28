@extends('layouts.app')

@section('pageTitle', 'Reset Password')
<!-- Main Content -->
@section('content')

@if (session('status'))
<div class="callout primary">
  {{ session('status') }}
</div>
@endif

<p>Enter the email address you registered with to receive a link which will allow you to reset your password.</p>

<form role="form" method="POST" action="{{ url('/password/email') }}">
  {{ csrf_field() }}

  <div class="row">
    <label class="form-label" for="email">E-Mail Address</label>
    <div class="form-control">
      <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

      @if ($errors->has('email'))
      <span class="help-block">
        <strong>{{ $errors->first('email') }}</strong>
      </span>
      @endif
    </div>
  </div>

  <div class="row">
    <div class="form-buttons">
      <button type="submit" class="button">
        Send Password Reset Link
      </button>
    </div>
  </div>
</form>
@endsection
