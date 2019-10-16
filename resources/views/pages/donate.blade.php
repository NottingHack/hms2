@extends('layouts.app')

@section('page-title', 'Donate')

@section('content')
<div class="container">
@guest
  <donation-stripe-payment :guest="true"></donation-stripe-payment>
@else
  <donation-stripe-payment></donation-stripe-payment>
@endguest
</div>
@endsection
