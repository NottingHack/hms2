@can('snackspace.transaction.view.self')
<div class="card">
  <div class="card-header">Snackspace account</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">Your Balance is: <span class="money">@money($user->getProfile() ? $user->getProfile()->getBalance() : 0, 'GBP')</span></li>
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
        <td class="money">@money($transaction->getAmount(), 'GBP')</td>
      </tr>
  @if ($loop->last)
    </tbody>
  </table>
  @endif
  @empty
  <ul class="list-group list-group-flush">
    <li class="list-group-item">No Transactions yet.</li>
  </ul>
  @endforelse
  <div class="card-footer">
    <a href="{{ route('users.snackspace.transactions', $user->getId()) }}" class="btn btn-primary mb-1"><i class="far fa-eye" aria-hidden="true"></i> View Transactions</a>
    @if (null !== config('services.stripe.key'))
    @if ($user->can('snackspace.payment') || ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < -100))
    <snackspace-stripe-payment
    @if ($user->can('snackspace.payment.debtOnly') && $user->getProfile()->getBalance() < 0)
    :balance="{{ $user->getProfile()->getBalance() }}"
    @endif
    >
    </snackspace-stripe-payment>
    @endif
    @endif
  </div>
</div>
@endcan
