@extends('layouts.app')

@section('pageTitle', 'Awaiting payment')

@section('content')
<div class="container">
  @content('pages.awaitingPayment', 'main')
  <p>
    <a class="btn btn-primary" href="{{ route('banking.bank-transactions.index') }}">View Standing Order Details</a>
  </p>
</div>
@endsection
