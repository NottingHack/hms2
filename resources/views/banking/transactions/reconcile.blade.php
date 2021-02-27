@extends('layouts.app')

@section('pageTitle', 'Reconcile Transaction')

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th>Date</th>
          <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
        </tr>
        <tr>
          <th>Description</th>
          <td>{{ $bankTransaction->getDescription() }}</td>
        </tr>
        <tr>
          <th>Amount</th>
          <td><span class="money">@money($bankTransaction->getAmount(), 'GBP')</span></td>
        </tr>
        <tr>
          <th>Bank Account</th>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <hr>
  <form role="form" method="POST" action="{{ route('bank-transactions.match', $bankTransaction->getId()) }}">
    @csrf
    @method('PATCH')

    <member-select-two name="user_id" :with-account="true"></member-select-two>
    <br>
    <button type="submit" class="btn btn-primary btn-block" name="action" value="membership"><i class="fas fa-check fa-lg" aria-hidden="true"></i> Match Membership Transaction</button>
    <button type="submit" class="btn btn-primary btn-block" name="action" value="snackspace"><i class="fas fa-check fa-lg" aria-hidden="true"></i> Create Snackspace Transaction</button>
  </form>
</div>
@endsection
