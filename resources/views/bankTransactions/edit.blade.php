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
          <td><span class="money">@format_pennies($bankTransaction->getAmount())</span></td>
        </tr>
        <tr>
          <th>Bank Account</th>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <hr>
  <form role="form" method="POST" action="{{ route('bank-transactions.update', $bankTransaction->getId()) }}">
    @csrf
    @method('PATCH')

    <div class="existing-account-select2">
      <select name="existing-account" class="js-data-existing-account-ajax" style="width:100%">
      </select>
    </div>
    <br>
    <div class="card">
      <button type="submit" class="btn btn-success"><i class="fas fa-check fa-lg" aria-hidden="true"></i> Match Transaction</button>
    </div>
  </form>
</div>
@endsection
