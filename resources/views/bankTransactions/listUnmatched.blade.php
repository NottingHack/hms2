@extends('layouts.app')

@section('pageTitle', 'Unmatched Bank Transactions')

@section('content')
<div class="container">
  @if(count($bankTransactions) > 0)
  <p> Please match any transactions below </p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Amount</th>
          <th>Bank Account</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bankTransactions as $bankTransaction)
        <tr>
          <td>{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
          <td>{{ $bankTransaction->getDescription() }}</td>
          <td><span class="money">@format_pennies($bankTransaction->getAmount())</span></td>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
          <td><a class="btn btn-primary" href="{{ route('bank-transactions.edit', $bankTransaction->getId()) }}"><i class="fas fa-search-dollar fa-lg" aria-hidden="true"></i> Reconcile</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
  @else
  <p> There are no unmatched transactions </p>
  @endif
</div>

@endsection
