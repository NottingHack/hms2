@extends('layouts.app')

@section('pageTitle')
Add Snackspace transaction for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('users.snackspace.transactions.store', $user->getId()) }}">
    @csrf

    <div class="form-group">
      <label for="description" class="form-label">Description</label>
      <input id="description" class="form-control" type="text" name="description" placeholder="Description of Transaction" value="{{ old('description') }}" max="512" required autofocus>
      @if ($errors->has('description'))
      <p class="help-text">
        <strong>{{ $errors->first('description') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <label for="amount" class="form-label">Amount in pence (TODO: Note about credit vs debit)</label>
      <input id="amount" class="form-control" type="number" name="amount" placeholder="in pence" value="{{ old('amount') }}" required>
      @if ($errors->has('amount'))
      <p class="help-text">
        <strong>{{ $errors->first('amount') }}</strong>
      </p>
      @endif
    </div>

    <div class="form-group">
      <div class="card">
        <button type="submit" class="btn btn-primary">Add Transaction</button>
      </div>
    </div>
  </form>
</div>
@endsection
