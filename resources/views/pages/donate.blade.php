@extends('layouts.app')

@section('pageTitle', 'Donate')

@section('content')
<div class="container">
  @content('pages.donate', 'main')
  @if (null != config('services.stripe.key'))
  @guest
  <donation-stripe-payment space-type="{{ ucfirst(config('branding.space_type')) }}" :guest="true"></donation-stripe-payment>
  @else
  <donation-stripe-payment space-type="{{ ucfirst(config('branding.space_type')) }}"></donation-stripe-payment>
  @endguest
  @else
  <p>Sorry donations can not be accepted at this time.</p>
  @endif
</div>
@endsection
