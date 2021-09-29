@extends('layouts.app')

@section('pageTitle', 'Register Interest')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col">
      <div class="card card-auth">
        <div class="card-body">
          @content('logo', 'svg')

          <h4>Register Interest</h4>

          <form role="form" method="POST" action="{{ route('registerInterest') }}">
            @csrf

            <div class="form-group">
              <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control{{ $errors->has('email') ? ' is-invalid ' : '' }}" placeholder="Email address" required autofocus>
              @if ($errors->has('email'))
              <span class="invalid-feedback">
                <strong>{{ $errors->first('email') }}</strong>
              </span>
              @endif
            </div>

            <button type="submit" class="btn btn-lg btn-info btn-block">Register Interest</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
