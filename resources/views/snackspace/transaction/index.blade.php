@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <div class="card w-100">
    <h3 class="card-header"><i class="far fa-money-bill" aria-hidden="true"></i> Balance</h3>
    <div class="card-body text-center">
        <h1><span class="money">@money($user->getProfile() ? $user->getProfile()->getBalance() : 0, 'GBP')</span></h1>
    </div>
  </div>

  <br>
  <p>
    Please use the note or coin acceptors located in the first floor members box room to add money to your Snackspace account.<br>
    @if (Auth::user() == $user && null !== config('services.stripe.key'))
    @if ($user->can('snackspace.payment') || ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < -100))
    Or click the button below to add money using a card.<br>
    <snackspace-stripe-payment
    @if ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < 0)
    :balance="{{ $user->getProfile()->getBalance() }}"
    @endif
    >
    </snackspace-stripe-payment>
    @endif
    @endif
  </p>
  <hr>
  <p>
    Details of your Snackspace and Tool Usage transactions are shown below.<br>
    We do not store card detials and only ever see the last 4 digits.
  </p>
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
          <td class="money">@money($transaction->getAmount(), 'GBP')</td>
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
@unless (Auth::user() == $user)
<div class="container">
  <hr>
  <a href="{{ route('users.snackspace.transactions.create', $user->getId()) }}"  class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add manual transaction</a>
</div>
@endunless
@endcan
@endsection
