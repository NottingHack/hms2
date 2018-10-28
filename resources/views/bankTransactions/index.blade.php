@extends('layouts.app')

@section('pageTitle')
Membership Payments for {{ $user->getFirstname() }}
@endsection

@section('content')

<div class="container">
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
      <td>{{ $bankTransaction->getAmount() }}</td>
      <td>{{ $bankTransaction->getBank()->getName() }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<div class="pagination-links">
  {{ $bankTransactions->links() }}
</div>

</div>
@endsection
