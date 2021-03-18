@extends('layouts.app')

@section('pageTitle', 'Company Information')

@section('content')
<div class="container">
  <h2>{{ config('branding.company_name') }}</h2>
  <p>{{ config('branding.company_name') }} is a private company limited by guarantee</p>
  <p>Company No. {{ config('branding.company_number') }}<br>
  Registered in England & Wales</p>

  <h5>Registered Address</h5>
  <ul class="list-unstyled">
    <li>Nottingham Hackspace</li>
    <li>{{ config('branding.address_1') }}</li>
    <li>{{ config('branding.address_2') }}</li>

    @if (config('branding.address_3'))
    <li>{{ config('branding.address_3') }}</li>
    @endif

    <li>{{ config('branding.city') }}</li>

    @if (config('branding.county'))
    <li>{{ config('branding.county') }}</li>
    @endif

    <li>{{ config('branding.postcode') }}</li>
  </ul>
</div>
@endsection
