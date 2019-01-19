@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm">
      <center>
        <div class="card">
          <div class="card-header icon-card-body">
            <div class="icon-card-icon"><i class="far fa-money-bill" aria-hidden="true"></i></div>
            <div class="icon-card-content">
              <h3>Balance</h3>
            </div>
          </div>
          <div class="card-body">
            <dl>
              <h1><span class="money">@format_pennies($user->getProfile() ? $user->getProfile()->getBalance() : 0)</span></h1>
            </dl>
          </div>
        </div>
      </center>
    </div>
  </div>
</div>

<br>

<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td>Date</td>
          <td class="d-none d-md-block">Type</td>
          <td>Description</td>
          <td>Amount</td>
        </tr>
      </thead>
      <tbody>
        @foreach ($transactions as $transaction)
        <tr>
          <td>{{ $transaction->getTransactionDatetime()->toDateTimeString() }}</td>
          <td class="d-none d-md-block">{{ $transaction->getTypeString() }}</td>
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
