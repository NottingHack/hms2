@extends('layouts.app')

@section('pageTitle', 'Edit Transaction')

@section('content')
<div class="container">
  <form role="form" method="POST" action="{{ route('banking.bank-transactions.update', $bankTransaction->getId()) }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
      <label for="transactionDate" class="form-label">Date</label>
      <input id="transactionDate" class="form-control @error('transactionDate') is-invalid @enderror" type="date" name="transactionDate" value="{{ old('transactionDate', $bankTransaction->getTransactionDate()->toDateString()) }}" required autofocus>
      @error('transactionDate')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <input id="description" class="form-control" type="text" name="description" placeholder="Description of Transaction" value="{{ old('description', $bankTransaction->getDescription()) }}" max="255" required >
      @error('description')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="amount" class="form-label">Amount in pence (use positive number to add credit, negative number to debit)</label>
      <input id="amount" class="form-control" type="number" name="amount" placeholder="in pence" value="{{ old('amount', $bankTransaction->getAmount()) }}" required @if ($bankTransaction->getTransaction()) readonly @endif>
      @if ($bankTransaction->getTransaction())
      <small id="amountHelpBlock" class="form-text text-muted">
        Amount cannot be changed once matched for Snackspace
      </small>
      @endif
      @error('amount')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-success btn-block">Update</button>
  </form>
  <hr>
  <p>The following can not be changed at this time</p>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th class="w-25">Bank</th>
          <td>{{ $bankTransaction->getBank()->getName() }}</td>
        </tr>
        <tr>
          <th>Membership Account Matched</th>
          <td>
            @if ($bankTransaction->getAccount())
            <span class="align-middle">
              {{ $bankTransaction->getAccount()->getPaymentRef() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('banking.accounts.show', $bankTransaction->getAccount()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
            @else
            Not Matched
            @endif
          </td>
        </tr>
        <tr>
          <th>Snackspace Matched</th>
          <td>
            @if ($bankTransaction->getTransaction())
            <span class="align-middle">
              {{ $bankTransaction->getTransaction()->getUser()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $bankTransaction->getTransaction()->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
            @else
            Not Matched
            @endif
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
@endsection
