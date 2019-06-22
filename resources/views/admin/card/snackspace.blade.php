@can('snackspace.transaction.view.all')
<div class="card">
  <div class="card-header">Snackspace account</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Balance is: <span class="money">@format_pennies($user->getProfile() ? $user->getProfile()->getBalance() : 0)</span></li>
  </ul>
  @forelse ($snackspaceTransactions as $transaction)
  @if ($loop->first)
  <table class="table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Description</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
  @endif
      <tr>
        <td>{{ $transaction->getTransactionDatetime()->toDateString() }}</td>
        <td>{{ $transaction->getDescription() }}</td>
        <td class="money">@format_pennies($transaction->getAmount())</td>
      </tr>
  @if ($loop->last)
    </tbody>
  </table>
  @endif
  @empty
  <ul class="list-group list-group-flush">
    <li class="list-group-item">No Transactions yet</li>
  </ul>
  @endforelse
  <div class="card-footer">
    <a href="{{ route('users.snackspace.transactions', $user->getId()) }}" class="btn btn-primary  mb-1"><i class="far fa-eye" aria-hidden="true"></i> View Transactions</a>
    @can('snackspace.transaction.create.all')
    <a href="{{ route('users.snackspace.transactions.create', $user->getId()) }}"  class="btn btn-primary  mb-1"><i class="fas fa-plus" aria-hidden="true"></i> Add manual transaction</a>
    @endcan
    {{-- <a href="#" class="btn btn-primary">Make a Payment</a> --}} {{-- Vue/Stripe --}}
  </div>
  </div>
@endcan
