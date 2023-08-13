@extends('layouts.app')

@section('pageTitle')
Membership Payments for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  @feature('standing_order_membership_payments')
  <div class="card w-100">
    <h3 class="card-header"><i class="fad fa-university" aria-hidden="true"></i> Standing order details</h3>
    <div class="card-body">
      @content('banking.transactions.index', 'details')

      <p>For convenience, if you click on the Copy icon next to the codes (<i class="far fa-copy"></i>), it will automatically copy to your system, allowing you to easily paste it into your standing order details without having to retype any digits.</p>
      <dl>
        <dt>Account number</dt>
        <dd>
          <span class="align-middle">
            <span id="accountNo">{{ $accountNo }}</span>&nbsp;
            <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#accountNo')"><i class="far fa-copy"></i></button>
          </span>
        </dd>
        <dt>Sort Code</dt>
        <dd>
          <span class="align-middle">
            <span id="sortCode">{{ $sortCode }}</span>&nbsp;
            <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#sortCode')"><i class="far fa-copy"></i></button>
          </span>
        </dd>
        <dt>Reference</dt>
        <dd>
          <span class="align-middle">
            <span id="paymentRef">{{ $paymentRef }}</span>&nbsp;
            <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#paymentRef')"><i class="far fa-copy"></i></button>
          </span>
        </dd>

        <dt>Our Account Name</dt>
        <dd>
          <span class="align-middle">
            <span id="accountName">{{ $accountName }}</span>&nbsp;
            <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#accountName')"><i class="far fa-copy"></i></button>
          </span>
        </dd>
      </dl>
    </div>
  </div>
  @endfeature

  <br>
  @content('banking.transactions.index', 'transactions')
  @if ( Auth::user() == $user || Gate::allows('bankTransactions.view.all'))
  @forelse ($bankTransactions as $bankTransaction)
  @if ($loop->first)
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
  @endif
        <tr>
          <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
          <td><span class="money">@money($bankTransaction->getAmount(), 'GBP')</span></td>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
  @endif
  @empty
  <p>No payments matched to this account.</p>
  @endforelse
  @elseif(Auth::user() != $user && Gate::allows('bankTransactions.view.limited'))
  @if ($bankTransactions->count() > 0)
  <div class="card">
    <h5 class="card-header">Last Payment Date:</h5>
    <div class="card-body">{{ $bankTransactions[0]->getTransactionDate()->toDateString() }}</div>
  </div>
  @else
  <p>No payments yet for this account.</p>
  @endif
  @endif
</div>
@endsection
