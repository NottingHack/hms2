@extends('layouts.app')

@section('content')
<div class="container">
  <h1>Welcome to HMS!</h1>
  <p class="lead">Already a member? <a href="{{ route('login') }}">Log in!</a></p>

  @content('welcome', 'main')
</div>
@endsection
