@extends('layouts.app')

@section('pageTitle', 'Cookie Policy')

@section('content')
<div class="container">
  <p>Lots of words
  </p>
  {{-- Strictly necessary cookies --}}
  {{-- These cookies are needed for this website to function correctly --}}
  {{-- hackspace_management_system_session --}}
  {{-- hackspace_management_system_passport --}}
  {{-- These three are CSRF/XRF tokens --}}

  {{-- hackspace_management_system_cookie_consent --}}
  {{-- Tracks if you have consented to private data cookies, (which we don't currently use anyway) --}}

  {{-- TODO: withdraw cookie consent button (delete hackspace_management_system_cookie_consent) --}}
</div>
@endsection
