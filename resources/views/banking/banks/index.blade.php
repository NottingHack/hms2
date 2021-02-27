@extends('layouts.app')

@section('pageTitle')
Banks
@endsection

@section('content')
<div class="container">
  <h2>Banks</h2>
  <hr>
  @forelse ($banks as $bank)
    @if ($loop->first)
    <div class="table-responsive no-more-tables">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Name</th>
            <th>Sort Code</th>
            <th>Account Number</th>
            <th>Name on Account</th>
            <th>Type</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
    @endif
          <tr>
            <td data-title="Name">{{ $bank->getName() }}</td>
            <td data-title="Sort Code">{{ $bank->getSortCode() }}</td>
            <td data-title="Account Number">{{ $bank->getAccountNumber() }}</td>
            <td data-title="Name of Account">{{ $bank->getAccountName() }}</td>
            <td data-title="Type">{{ $bank->getTypeString() }}</td>
            <td class="actions">
              <a class="btn btn-primary btn-sm mb-1" href="{{ route('banking.banks.show', $bank->getId()) }}"><i class="fas fa-eye" aria-hidden="true"></i> View</a>

              @can('bank.edit')
              <a class="btn btn-primary btn-sm mb-1" href="{{ route('banking.banks.edit', $bank->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
              @endcan

              @if (HMS\Entities\Banking\BankType::AUTOMATIC != $bank->getType())
              @can('bankTransactions.edit')
              <a class="btn btn-primary btn-sm mb-1" href="{{ route('banking.banks.bank-transactions.create', $bank->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Add Transaction</a>
              @endcan
              @endif
            </td>
          </tr>
    @if ($loop->last)
        </tbody>
      </table>
    </div>

    <div class="pagination-links">
      {{ $banks->links() }}
    </div>
    @endif
    @empty
    <p>No Banks yet.</p>
    @endforelse
</div>

@can('bank.create')
<br>
<div class="container">
  <a href="{{ route('banking.banks.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add new bank</a>
</div>
@endcan

<div class="container">
  <br>
  <h5>Type</h5>
  <dl>
    <dt>Automatic</dt>
    <dd>Fully automated bank where new transactions are uploaded via the api endpoint. Only unmatched transaction can be reconciled.</dd>
    <dt>Manual</dt>
    <dd>Transactions are manually entered (via web interface) record of a payment or purchase or via the api.</dd>
    <dt>Cash</dt>
    <dd>Special case MANUAL to represent petty cash account.</dd>
  </dl>
</div>
@endsection
