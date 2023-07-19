@extends('layouts.app')

@section('pageTitle', 'CSV Downloads')

@section('content')
<div class="container">
  <p>
    These CSVs contain personal data and should only be download when absolutle need.<br>
    They should be deleted from your device as soon as you are finished with the data.
  </p>
  <ul>
    @can('profile.view.all')
    <li><a href="{{ route('csv-download.current-members') }}" target="_blank">Download CSV of current Member names and emails</a></li>
    @endcan
    @can('profile.view.all')
    <li><a href="{{ route('csv-download.opa-csv') }}" target="_blank">Download CSV of current Member emails for use with OPA Vote</a></li>
    @endcan
    @can('profile.view.all')
    <li><a href="{{ route('csv-download.low-payers') }}" target="_blank">Download CSV for Low Payer audits</a></li>
    @endcan
    @can('profile.view.all')
    <li><a href="{{ route('csv-download.payment-change') }}" target="_blank">Download CSV for Membership Payment Changes</a></li>
    @endcan
    @can('profile.view.all')
    <li><a href="{{ route('csv-download.member-payments') }}" target="_blank">Download CSV for All Matched Payments</a></li>
    @endcan
    @feature('boxes')
    @can('profile.view.all')
    @can('box.view.all')
    <li><a href="{{ route('csv-download.member-boxes') }}" target="_blank">Download CSV of Members Boxes</a></li>
    @endcan
    @endcan
    @endfeature
  </ul>
</div>
@endsection
