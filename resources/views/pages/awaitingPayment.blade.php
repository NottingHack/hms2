@extends('layouts.app')

@section('pageTitle', 'Awaiting payment')

@section('content')
<div class="container">
  <p>
    Your membership details have been approved and we are now waiting on your payment to show up in our account. If you have further questions, please talk to the Membership Team.
  </p>
  <p>
    <a class="btn btn-primary" href="{{ route('banking.bank-transactions.index') }}">View Standing Order Details</a>
  </p>
</div>
@endsection
