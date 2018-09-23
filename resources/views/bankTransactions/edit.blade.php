@extends('layouts.app')

@section('pageTitle', 'Reconcile Transaction')

@section('content')
<table>
  <thead>
    <tr>
      <th>Date</th>
      <th>Description</th>
      <th>Amount</th>
      <th>Bank Account</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
      <td>{{ $bankTransaction->getDescription() }}</td>
      <td>{{ $bankTransaction->getAmount() }}</td>
      <td>{{ $bankTransaction->getBank()->getName() }}</td>
    </tr>
  </tbody>
</table>

<div>
  <form role="form" method="POST" action="{{ route('bank-transactions.update', $bankTransaction->getId()) }}">
    {{ csrf_field() }}
    {{ method_field('PATCH') }}
    <div>
      <select name="existing-account" class="js-data-existing-account-ajax">
      </select>
    </div>
    <div>
      <button type="submit" class="button">Match Transaction</button>
    </div>
  </form>
</div>
@endsection
