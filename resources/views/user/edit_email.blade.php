@extends('layouts.app')

@section('pageTitle')
{{ $user->getFullName() }}'s Email
@endsection

@section('content')
<div class="container">
  <div class="card">
    <h5 class="card-header">Update Email for {{ $user->getFullName() }}</h5>
    <form id="user-edit-form" role="form" method="POST" action="{{ route('users.admin.update-email', $user->getId()) }}">
      @csrf
      @method('PATCH')

      <div class="card-body">

        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <input placeholder="Email Address" class="form-control{{  $errors->has('email') ? ' is-invalid' : '' }}" id="email" type="email" name="email" value="{{ old('email', $user->getEmail()) }}" required>
          @if ($errors->has('email'))
          <span class="invalid-feedback">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
          <p id="emailHelp" class="form-text text-muted">The user will be required to verify thier email address.</p>
        </div>
      </div>
      <div class="card-footer">
        <button type="submit" class="btn btn-success btn-block">Update</button>
      </div>
    </form>
  </div>
</div>
@endsection
