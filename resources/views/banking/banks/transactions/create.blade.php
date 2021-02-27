@extends('layouts.app')

@section('pageTitle')
Add Bank Transaction to {{ $bank->getName() }}
@endsection

@section('content')
<div class="container">
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <tr>
          <th class="w-25">Bank</th>
          <td>{{ $bank->getName() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <form role="form" method="POST" action="{{ route('banking.banks.bank-transactions.store', $bank->getId()) }}">
    @csrf

    <div class="form-group">
      <label for="transactionDate" class="form-label">Date</label>
      <input id="transactionDate" class="form-control @error('transactionDate') is-invalid @enderror" type="date" name="transactionDate" value="{{ old('transactionDate') }}" required autofocus>
      @error('transactionDate')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <input id="description" class="form-control" type="text" name="description" placeholder="Description of Transaction" value="{{ old('description') }}" max="255" required >
      <small id="amountHelpBlock" class="form-text text-muted">
        Auto matching of Account and Snackspace ref's will be attempted
      </small>
      @error('description')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="amount" class="form-label">Amount in pence (use positive number to add credit, negative number to debit)</label>
      <input id="amount" class="form-control" type="number" name="amount" placeholder="in pence" value="{{ old('amount') }}" required>
      @error('amount')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block">Add Bank Transaction</button>
  </form>
</div>
@endsection
