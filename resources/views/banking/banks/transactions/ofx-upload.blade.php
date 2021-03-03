@extends('layouts.app')

@section('pageTitle')
Upload OFX for {{ $bank->getName() }}
@endsection

@section('content')
<div class="container">
  <h2>Bank details</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <tbody>
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
          <th>Latest Bank Transaction Date</th>
          <td>{{ $latestBankTransaction->getTransactionDate()->toDateString() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <br>
  <h2>Upload OFX</h2>
  <form class="form-group" role="form" method="POST" action="{{ route('banking.banks.bank-transactions.store-ofx', $bank->getId()) }}" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <div class="custom-file">
        <input
          type="file"
          class="custom-file-input @error('OfxFile') is-invalid @enderror"
          id="OfxFile"
          name="OfxFile"
          accept=".ofx,application/x-ofx"
          required
          >
        <label class="custom-file-label" for="OfxFile">Choose file</label>
        @error('OfxFile')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>


    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-file-upload" aria-hidden="true"></i> Upload OFX</button>
  </form>
@endsection
