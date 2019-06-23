@extends('layouts.app')

@section('pageTitle', 'Invite Search')

@section('content')
<div class="container">
  <p>
    Words about the searching for an invite to resend.
  </p>
  <invite-search action="{{ route('admin.invites.resend', ['invite' => '_ID_']) }}"></invite-search>
</div>
@endsection
