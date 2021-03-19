@extends('layouts.app')

@section('pageTitle', 'Awaiting approval')

@section('content')
<div class="container">
  @content('pages.awaitingApproval', 'main')
  @unless(Auth::user()->hasVerifiedEmail())
  <p>
    You have not verified your email address yet. Please check your in-box for a 'Verify Email Address' mail and give the link a quick click.
  </p>
  @endunless
  <p>
    <a class="btn btn-primary" href="{{ route('membership.edit', Auth::user()->getId()) }}">Update Details</a>
  </p>
</div>
@endsection
