@extends('layouts.app')

@section('pageTitle', 'Change password for '.$user->getFirstname())

@section('content')
<div class="container">
  <div class="card">
    <div class="card-header">
      <h5>Update your password below.</h5>
    </div>

    <form id="change-password-form" role="form" method="POST" action="{{ route('users.changePassword.update', $user->getId()) }}">
      {{ csrf_field() }}
      {{ method_field('PUT') }}
      <div class="card-body">
        <label for="currentPassword" class="form-label">Current Password</label>
        <input placeholder="Current Password" class="form-control" id="currentPassword" type="password" name="currentPassword" required autofocus autocomplete="current-password">
        @if ($errors->has('currentPassword'))
        <span class="help-block">
          <strong>{{ $errors->first('currentPassword') }}</strong>
        </span><br/>
        @endif

        <hr>

        <label for="Password1" class="form-label">New Password</label>
        <input placeholder="New Password" class="form-control" id="password" type="password" name="password" required autocomplete="new-password">
        @if ($errors->has('password'))
        <span class="help-block">
          <strong>{{ $errors->first('password') }}</strong>
        </span><br/>
        @endif

        <label for="Password2" class="form-label">Confirm New Password</label>
        <input placeholder="Confirm Password" class="form-control" id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password">
        @if ($errors->has('password_confirmation'))
        <span class="help-block">
          <strong>{{ $errors->first('password_confirmation') }}</strong>
        </span><br/>
        @endif

      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block"> Update</button>
      </div>
    </form>
  </div>
  <!-- /card-container -->
</div>
<!-- /container -->
@endsection
