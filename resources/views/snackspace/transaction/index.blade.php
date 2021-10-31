@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
@if (Auth::user() != $user)
<div class="container">
  <h3>
    {{ $user->getFullname() }} <a class="btn-sm btn-primary mb-2" href="{{ route('users.admin.show', $user->getId()) }}"><i class="fa fa-eye"></i></a>
  </h3>
  <hr>
</div>
@endif

<div class="container">
  <div class="card w-100">
    <h3 class="card-header"><i class="fad fa-money-bill" aria-hidden="true"></i> Balance</h3>
    <div class="card-body text-center">
        <h1><span class="money">@money($user->getProfile() ? $user->getProfile()->getBalance() : 0, 'GBP')</span></h1>
    </div>
  </div>

  <br>
  @content('snackspace.transaction.index', 'acceptors')
  @if (Auth::user() == $user && null != config('services.stripe.key'))
  <p>
    @if ($user->can('snackspace.payment') || ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < -100))
    Or click the button below to add money using a card.<br>
    <snackspace-stripe-payment
    @if ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < 0)
    :balance="{{ $user->getProfile()->getBalance() }}"
    @endif
    >
    </snackspace-stripe-payment>
    @endif
  </p>
  @endif
  <hr>
  <p>
    Details of your Snackspace and Tool Usage transactions are shown below.<br>
    We do not store card details and only ever see the last 4 digits.
  </p>
</div>

<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Date</th>
          <th class="d-none d-md-table-cell">Type</th>
          <th>Description</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($transactions as $transaction)
        <tr>
          <td>{{ $transaction->getTransactionDatetime()->toDateTimeString() }}</td>
          <td class="d-none d-md-table-cell">{{ $transaction->getTypeString() }}</td>
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
