@extends('layouts.app')

@section('pageTitle', 'Snackspace Payments Report')

@section('content')
<div class="container">
  <h2>From {{ $startDate->toFormattedDateString() }} to {{ $endDate->toFormattedDateString() }}</h2>
  <p>Break down of income recorded by HMS and hows its allocated</p>
  <h4>Payments</h4>
  <table class="table">
    <tr>
      <th class="w-25">Cash Payments:</th>
      <td>@format_pennies($cashPayments)</td>
    </tr>
    <tr>
      <th>Online Payments:</th>
      <td>@format_pennies($cardPayments)</td>
    </tr>
    <tr>
      <th>Bank Payments:</th>
      <td>@format_pennies($bankPayments)</td>
    </tr>
    <tr>
      <th>Manual Payments:</th>
      <td>@format_pennies($manualCredits)</td>
    </tr>
    <tr>
      <th>TotalPayments:</th>
      <td>@format_pennies($totalPayments)</td>
    </tr>
  </table>
  <h4>Purchases</h4>
  <table class="table">
    <tr>
      <th class="w-25">For Vending Purchases:</th>
      <td>@format_pennies($forVendPurchases)</td>
    </tr>
    <tr>
      <th>For Tool Usages:</th>
      <td>@format_pennies($forToolPurchases)</td>
    </tr>
    <tr>
      <th>For Membership Boxes:</th>
      <td>@format_pennies($forBoxPurchases)</td>
    </tr>
    <tr>
      <th>For Other:</th>
      <td>@format_pennies($forOtherPurchases)</td>
    </tr>
  </table>
  <hr>
  <h3>Change report period</h3>
  <form role="form" method="GET" actin="{{ route('snackspace.payment-report') }}">
    <div class="form-group">
      <label for="startDate" class="form-label">Date</label>
      <input class="form-control @error('startDate') is-invalid @enderror" id="startDate" type="date" name="startDate" value="{{ old('startDate', $startDate->toDateString()) }}" required>
      @error('startDate')
      <p class="invalid-feedback">
        <strong>{{ $errors->first('startDate') }}</strong>
      </p>
      @enderror
    </div>
    <div class="form-group">
      <label for="endDate" class="form-label">Date</label>
      <input class="form-control @error('endDate') is-invalid @enderror" id="endDate" type="date" name="endDate" value="{{ old('endDate', $endDate->toDateString()) }}" required>
      @error('endDate')
      <p class="invalid-feedback">
        <strong>{{ $errors->first('endDate') }}</strong>
      </p>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-redo"></i> Update period</button>
  </form>
</div>
@endsection
