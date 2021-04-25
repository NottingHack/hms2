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
    <li>{{ config('branding.space_name') }}</li>
    <li>{{ config('branding.registered_address_1') }}</li>
    <li>{{ config('branding.registered_address_2') }}</li>

    @if (config('branding.registered_address_3'))
    <li>{{ config('branding.registered_address_3') }}</li>
    @endif

    <li>{{ config('branding.registered_city') }}</li>

    @if (config('branding.registered_county'))
    <li>{{ config('branding.registered_county') }}</li>
    @endif

    <li>{{ config('branding.registered_postcode') }}</li>
  </ul>
</div>
@endsection
