@extends('layouts.app')

@section('pageTitle')
Add Manual Membership Payment to {{ $account->getPaymentRef() }}
@endsection

@section('content')
<div class="container">
  <h3>Account - {{ $account->getPaymentRef() }}</h3>
  <hr>
  <h4>Users linked to this account</h4>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
        <thead>
          <th>Name</th>
          <th>Email</th>
        </thead>
        @foreach ($account->getUsers() as $user)
        <tr>
          <td>
            <span class="align-middle">
              {{ $user->getFullname() }}
              <div class="btn-group float-right" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $user->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td>{{ $user->getEmail() }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <hr>
  <h4>Add Manual Membership Payment</h4>
  <form role="form" method="POST" action="{{ route('banking.accounts.bank-transactions.store', $account->getId()) }}">
    @csrf

    <div class="form-group">
      <label for="bank_id" class="form-label">Bank</label>
      <select-two
        :invalid="{{ $errors->has('bank_id') ? 'true' : 'false' }}"
        id="bank_id"
        name='bank_id'
        placeholder="Select a bank..."
        style="width: 100%"
        :settings="{minimumResultsForSearch: Infinity}"
        {{ old('bank_id') ? ':value="' . old('bank_id') . '"' : '' }}
        >
        <option value=""></option>
        @foreach ($banks as $bank)
        <option value="{{ $bank->getId() }}" {{ (old('bank_id') == $bank->getId()) ? 'selected=selected' : '' }}>
          {{ $bank->getName() }}
        </option>
        @endforeach
      </select-two>
    </div>

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

    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add</button>
  </form>
</div>
@endsection
