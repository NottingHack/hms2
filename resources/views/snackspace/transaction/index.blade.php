@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <div class="card w-100">
    <h3 class="card-header"><i class="far fa-money-bill" aria-hidden="true"></i> Balance</h3>
    <div class="card-body text-center">
        <h1><span class="money">@format_pennies($user->getProfile() ? $user->getProfile()->getBalance() : 0)</span></h1>
    </div>
  </div>

  <br>
  <p>Words about purchases/payments records, credit limit and how to pay off balance in the space at the cash acceptors.</p>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td>Date</td>
          <td class="d-none d-md-tabel-cell">Type</td>
          <td>Description</td>
          <td>Amount</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($transactions as $transaction)
        <tr>
          <td>{{ $transaction->getTransactionDatetime()->toDateTimeString() }}</td>
          <td class="d-none d-md-tabel-cell">{{ $transaction->getTypeString() }}</td>
          <td>{{ $transaction->getDescription() }}</td>
          <td class="money">@format_pennies($transaction->getAmount())</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div classs="pagination-links">
    {{ $transactions->links() }}
  </div>
</div>
@endsection
