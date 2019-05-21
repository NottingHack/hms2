@extends('layouts.app')

@section('pageTitle')
Membership Payments for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <div class="card w-100">
    <h3 class="card-header"><i class="far fa-university" aria-hidden="true"></i> Standing order details</h3>
    <div class="card-body">
      <p>Setting up standing order and importance of using the right reference words go here.</p>
      <dl>
        <dt>Account number</dt>
        <dd>{{ $accountNo }}</dd>
        <dt>Sort Code</dt>
        <dd>{{ $sortCode }}</dd>
        <dt>Reference</dt>
        <dd>{{ $paymentRef }}</dd>
      </dl>
    </div>
  </div>

  <br>
  <p>Words about matched payments we have received.</p>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Date</th>
          <th>Amount</th>
          <th>Bank Account</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bankTransactions as $bankTransaction)
        <tr>
          <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
          <td><span class="money">@format_pennies($bankTransaction->getAmount())</span></td>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
</div>
@endsection
