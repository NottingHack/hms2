@extends('layouts.app')

@section('pageTitle')
Editing Bank - {{ $bank->getName() }}
@endsection


@section('content')
<div class="container">
  <form class="form-group" role="form" method="POST" action="{{ route('banking.banks.update', $bank->getId()) }}">
    @csrf
    @method('PATCH')
    <div class="form-group">
      <label for="name" class="form-label">Name</label>
      <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Name" value="{{ old('name', $bank->getName()) }}" required autofocus maxlength="100">
      <small id="nameHelpBlock" class="form-text text-muted">
        Name for display in HMS
      </small>
      @error('name')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="sortCode" class="form-label">Sort Code</label>
      <input id="sortCode" class="form-control @error('sortCode') is-invalid @enderror" type="text" name="sortCode" placeholder="00-00-00" value="{{ old('sortCode', $bank->getSortCode()) }}" required maxlength="8">
      @error('sortCode')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="accountNumber" class="form-label">Account Number</label>
      <input id="accountNumber" class="form-control @error('accountNumber') is-invalid @enderror" type="text" name="accountNumber" placeholder="00000000" value="{{ old('accountNumber', $bank->getAccountNumber()) }}" required  maxlength="8">
      @error('accountNumber')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <div class="form-group">
      <label for="accountName" class="form-label">Name on Account</label>
      <input id="accountName" class="form-control @error('accountName') is-invalid @enderror" type="text" name="accountName" placeholder="Name on Account" value="{{ old('accountName', $bank->getAccountName()) }}" required  maxlength="100">
      <small id="accountNameHelpBlock" class="form-text text-muted">
        Name, as is on the banks systems, needed by some when setting up a standing order
      </small>
      @error('accountName')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    <label for="type" class="form-label">Type</label>
    <fieldset class="form-group">
      @foreach(HMS\Entities\Banking\BankType::TYPE_STRINGS as $type => $string)
      <div class="form-check form-check-inline">
        <input
          id="type-{{ $type }}"
          class="form-check-input"
          type="radio"
          name="type"
          value="{{ $type }}"
          {{ old('type', $bank->getType()) == $type ? 'checked="checked"' : '' }}
          >
        <label class="form-check-label" for="type-{{ $type }}">
          {{ $string }}
        </label>
      </div>
      @endforeach
      <small id="typeHelpBlock" class="form-text text-muted">
        See below
      </small>
      @error('type')
      <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </fieldset>

    <button type="submit" class="btn btn-success btn-block">Update</button>
  </form>
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
