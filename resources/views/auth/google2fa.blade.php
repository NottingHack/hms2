@extends('layouts.app')

@section('pageTitle', 'Log In, 2FA')

@section('content')
<div class="container">
  <div class="row justify-content-md-center">
    <div class="col-md-8 ">
      <div class="card">
        <div class="card-header">Two Factor Authentication</div>
        <div class="card-body">
          <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>

          @if ($errors->any())
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
          @endif

          <strong>Enter the pin from Google Authenticator Enable 2FA</strong><br/><br/>
          <form class="form-horizontal" action="{{ route('2faVerify') }}" method="POST">
            @csrf
            <div class="form-group{{ $errors->has('one_time_password-code') ? ' has-error' : '' }}">
              <label for="one_time_password" class="col-md-4 control-label">One Time Password</label>
              <div class="col-md-6">
                <input id="one_time_password" name="one_time_password" class="form-control"  type="text" required/>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-primary" type="submit">Authenticate</button>
              </div>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
