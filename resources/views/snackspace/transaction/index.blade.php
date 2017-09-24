@extends('layouts.app')

@section('pageTitle')
Snackspace account for {{ $user->getFirstname() }}
@endsection

@section('content')
<div>
  <dl>
    <dt>Balance</dt>
    <dd class="money">@format_pennies($user->getProfile() ? $user->getProfile()->getBalance() : 0)</dd>
  </dl>
</div>

<table>
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
@endsection
