@extends('layouts.app')

@section('pageTitle', 'Cookie Policy')

@section('content')
<div class="container">
  @content('pages.cookie_policy', 'main')
{{-- Strictly necessary cookies --}}
{{-- These cookies are needed for this website to function correctly --}}

{{-- hackspace_management_system_session --}}
{{-- hackspace_management_system_passport --}}
{{-- XSRF-TOKEN --}}
{{-- These three are CSRF/XRF tokens --}}

{{-- io --}}
{{-- Socket IO session cookie, Used with laravel echo / event broadcasting --}}


{{-- hackspace_management_system_cookie_consent --}}
{{-- Tracks if you have consented to private data cookies, (which we don't currently use anyway) --}}

{{-- __stripe_sid --}}
{{-- __stripe_mid --}}
{{-- the come from strip for fraud tracking --}}

{{-- TODO: withdraw cookie consent button (delete hackspace_management_system_cookie_consent) --}}
</div>
@endsection
