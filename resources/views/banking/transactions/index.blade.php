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
        <dd id="accountNo">
          <span class="align-middle">
            {{ $accountNo }}&nbsp;<button class="btn btn-light btn-sm btn-sm-spacing" onclick="copyToClipboard('#accountNo')"><i class="far fa-copy "></i></button>
          </span>
        </dd>
        <dt>Sort Code</dt>
        <dd id="sortCode">
          <span class="align-middle">
            {{ $sortCode }}&nbsp;<button class="btn btn-light btn-sm btn-sm-spacing" onclick="copyToClipboard('#sortCode')"><i class="far fa-copy "></i></button>
          </span>
        </dd>
        <dt>Reference</dt>
        <dd id="paymentRef">
          <span class="align-middle">
            {{ $paymentRef }}&nbsp;<button class="btn btn-light btn-sm btn-sm-spacing" onclick="copyToClipboard('#paymentRef')"><i class="far fa-copy "></i></button>
          </span>
        </dd>
      </dl>
    </div>
  </div>

  <br>
  <p>Words about matched payments we have received.</p>
  @if( Auth::user() == $user || Gate::allows('bankTransactions.view.all'))
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
  @elseif(Auth::user() != $user && Gate::allows('bankTransactions.view.limited'))
  @isset($bankTransactions)
  <div class="card">
    <h5 class="card-header">Last Payment Date:</h5>
    <div class="card-body">{{ $bankTransactions[0]->getTransactionDate()->toDateString() }}</div>
  </div>
  @endisset
  @endif
</div>
@endsection
