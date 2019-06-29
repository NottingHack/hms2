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
  <p>Please use the note or coin acceptors located in the first floor members box room to add money to your Snackspace account.</p>
  <p>Details of your Snackspace and Tool Usage transactions are shown below.</p>

  </div>
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Date</th>
          <th class="d-none d-md-tabel-cell">Type</th>
          <th>Description</th>
          <th>Amount</th>
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
@can ('snackspace.transaction.create.all')
<div class="container">
  <div class="card">
    <a href="{{ route('users.snackspace.transactions.create', $user->getId()) }}"  class="btn btn-primary"><i class="fas fa-plus" aria-hidden="true"></i> Add manual transaction</a>
  </div>
</div>
@endcan

@endsection
