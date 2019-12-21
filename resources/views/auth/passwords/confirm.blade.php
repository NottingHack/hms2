@extends('layouts.app')

@section('pageTitle', 'Confirm Password')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col">
      <div class="card card-auth">
        <div class="card-header">{{ __('Confirm Password') }}</div>

        <div class="card-body">
          <p>
            {{ __('Please confirm your password before continuing.') }}
          </p>

          <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="form-group">
              <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password" >

              @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>

            <button type="submit" class="btn btn-lg btn-info btn-block">
              {{ __('Confirm Password') }}
            </button>
          </form>
          @if (Route::has('password.request'))
          <br>
          <a href="{{ route('password.request') }}" class="text-info mt-2">
            Forgot your password?
          </a>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
