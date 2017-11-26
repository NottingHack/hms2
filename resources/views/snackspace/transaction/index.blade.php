@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fa fa-money" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Balance</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>£ @format_pennies($user->getProfile()->getBalance())</dt>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fa fa-wrench" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Laser Cutter</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>£ @format_pennies($user->getProfile()->getBalance())</dt>
          </dl>
        </div>
      </div>
    </div>

    <div class="col-sm">
      <div class="card">
        <div class="card-header icon-card-body">
          <div class="icon-card-icon"><i class="fa fa-money" aria-hidden="true"></i></div>
          <div class="icon-card-content">
            <h3>Something Else</h3>
          </div>
        </div>
        <div class="card-body">
          <dl>
            <dt>£ @format_pennies($user->getProfile()->getBalance())</dt>
          </dl>
        </div>
      </div>
    </div>
  </div>
</div>

<br>

<div class="container">
  <table class="table table-bordered table-hover table-responsive">
    <thead>
      <tr>
        <td>Date</td>
        <td>Type</td>
        <td>Description</td>
        <td>Amount</td>
      </tr>
    </thead>
    <tbody>
      @foreach ($transactions as $transaction)
      <tr>
        <td>{{ $transaction->getTransactionDatetime()->toDateTimeString() }}</td>
        <td>{{ $transaction->getType() }}</td>
        <td>{{ $transaction->getDescription() }}</td>
        <td class="money">@format_pennies($transaction->getAmount())</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div classs="pagination-links">
    {{ $transactions->links() }}
  </div>
</div>
@endsection
