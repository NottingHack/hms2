@extends('layouts.app')

@section('pageTitle', 'Donate')

@section('content')
<div class="container">
@if (null != config('services.stripe.key'))
@guest
  <donation-stripe-payment :guest="true"></donation-stripe-payment>
@else
  <donation-stripe-payment></donation-stripe-payment>
@endguest
@else
<p>Sorry donations can not be accepted at this time.</p>
@endif
</div>
@endsection
