@extends('layouts.app')

@section('pageTitle')
Bank - {{ $bank->getName() }}
@endsection

@section('content')
<div class="container">
  <h2>Bank details</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th>ID</th>
          <td>{{ $bank->getId() }}</td>
        </tr>
        <tr>
          <th>Name</th>
          <td>{{ $bank->getName() }}</td>
        </tr>
        <tr>
          <th>Sort Code</th>
          <td>{{ $bank->getSortCode() }}</td>
        </tr>
        <tr>
          <th>Account Number</th>
          <td>{{ $bank->getAccountNumber() }}</td>
        </tr>
        <tr>
          <th>Name on Account</th>
          <td>{{ $bank->getAccountName() }}</td>
        </tr>
        <tr>
          <th>Type</th>
          <td>{{ $bank->getTypeString() }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  @can('bank.edit')
  <a href="{{ route('banking.banks.edit', $bank->getId()) }}" class="btn btn-info btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  @endcan

  <br>
  <h2>Bank Transactions</h2>
  <hr>

  @if (HMS\Entities\Banking\BankType::AUTOMATIC == $bank->getType())
  @feature('ofx_bank_upload')
  @can('bankTransactions.ofxUpload')
  <a href="{{ route('banking.banks.bank-transactions.ofx-upload', $bank->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-file-upload" aria-hidden="true"></i> Upload OFX</a>
  <br>
  @endcan
  @endfeature
  @else
  @can('bankTransactions.edit')
  <a href="{{ route('banking.banks.bank-transactions.create', $bank->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add Bank Transaction</a>
  <br>
  @endcan
  @endif

  <p>Latest first</p>

  <div classs="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="w-10">Date</th>
          <th>Description</th>
          <th class="w-10">Amount</th>
          <th>Account Matched</th>
          <th>Snackspace Matched</th>
          <th >Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($bankTransactions as $bankTransaction)
        <tr>
          <td data-title="Date">{{ $bankTransaction->getTransactionDate()->toDateString() }}</td>
          <td data-title="Description">{{ $bankTransaction->getDescription() }}</td>
          <td data-title="Amount"><span class="money">@money($bankTransaction->getAmount(), 'GBP')</span></td>
          <td data-title="Account Matched">
            @if ($bankTransaction->getAccount())
            <span class="align-middle">
              {{ $bankTransaction->getAccount()->getPaymentRef() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('banking.accounts.show', $bankTransaction->getAccount()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
            @endif
          </td>
          <td data-title="Snackspace Matched">
            @if ($bankTransaction->getTransaction())
            <span class="align-middle">
              {{ $bankTransaction->getTransaction()->getUser()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $bankTransaction->getTransaction()->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
            @endif
          </td>
          <td class="actions">
            @if (HMS\Entities\Banking\BankType::AUTOMATIC != $bank->getType())
            @can('bankTransactions.edit')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('banking.bank-transactions.edit', $bankTransaction->getId()) }}"><i class="fas fa-search-dollar" aria-hidden="true"></i> Edit</a>
            @endcan
            @endif
            @if (is_null($bankTransaction->getAccount()) && is_null($bankTransaction->getTransaction()))
            @can('bankTransactions.reconcile')
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('banking.bank-transactions.reconcile', $bankTransaction->getId()) }}"><i class="fas fa-search-dollar" aria-hidden="true"></i> Reconcile</a>
            @endcan
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links">
    {{ $bankTransactions->links() }}
  </div>
</div>
@endsection
