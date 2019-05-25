<div {{-- class="col-lg-4 mb-3" --}}>
  <div class="card">
    <div class="card-header">
      Snackspace account
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">Your Balance is: <span class="money">@format_pennies($user->getProfile() ? $user->getProfile()->getBalance() : 0)</span></li>
    </ul>
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Description</th>
          <th>Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($snackspaceTransactions as $transaction)
        <tr>
          <td>{{ $transaction->getTransactionDatetime()->toDateString() }}</td>
          <td>{{ $transaction->getDescription() }}</td>
          <td class="money">@format_pennies($transaction->getAmount())</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="card-footer">
      <a href="{{ route('users.snackspace.transactions', $user->getId()) }}" class="btn btn-primary">View Transactions</a>
      {{-- <a href="#" class="btn btn-primary">Make a Payment</a> --}} {{-- Vue/Stripe --}}
    </div>
  </div>
</div>
