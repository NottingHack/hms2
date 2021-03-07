@canany(['bankTransactions.view.limited', 'bankTransactions.view.all'])
@if ($user->getAccount())
<div class="card">
  <div class="card-header">Standing Order</div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">
      <span class="align-middle">
        Reference: <span id="paymentRef">{{ $user->getAccount()->getPaymentRef() }}</span>&nbsp;
        <div class="btn-group float-right" role="group" aria-label="View Account">
          <button type="button" class="btn btn-light btn-sm" onclick="copyToClipboard('#paymentRef')"><i class="far fa-copy"></i></button>
        </div>
      </span>
    </li>
    @feature('match_legacy_ref')
    <li class="list-group-item">
        Legacy Reference: {{ $user->getAccount()->getLegacyRef() }}
    </li>
    @endfeature
    @if (count($user->getAccount()->getUsers()) > 1)
    <li class="list-group-item">This is a Joint Account</li>
    @endif
    @cannot('bankTransactions.view.all')
    @can('bankTransactions.view.limited')
    @if ($bankTransactions->count() > 0)
    <li class="list-group-item">Last Payment Date: {{ $bankTransactions[0]->getTransactionDate()->toDateString() }}</li>
    @else
    <li class="list-group-item">No payments yet.</li>
    @endif
    @endcan
    @endcannot
  </ul>
  @can('bankTransactions.view.all')
  @forelse ($bankTransactions as $transaction)
  @if ($loop->first)
  <table class="table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Amount</th>
      </tr>
    </thead>
    <tbody>
  @endif
      <tr>
        <td>{{ $transaction->getTransactionDate()->toDateString() }}</td>
        <td class="money">@money($transaction->getAmount(), 'GBP')</td>
      </tr>
  @if ($loop->last)
    </tbody>
  </table>
  @endif
  @empty
  <ul class="list-group list-group-flush">
    <li class="list-group-item">No payments yet.</li>
  </ul>
  @endforelse
  @endcan
  <div class="card-footer">
    <a href="{{ route('banking.accounts.show', $user->getAccount()->getId()) }}" class="btn btn-primary mb-1"><i class="far fa-eye" aria-hidden="true"></i> View Account</a>
    @feature('cash_membership_payments')
    @can('bankTransactions.edit')
    <a href="{{ route('banking.accounts.bank-transactions.create', $user->getAccount()->getId()) }}" class="btn btn-primary mb-1"><i class="fas fa-plus" aria-hidden="true"></i> Add manual payment</a>
    @endcan
    @endfeature
  </div>
</div>
@endif
@endcanany
