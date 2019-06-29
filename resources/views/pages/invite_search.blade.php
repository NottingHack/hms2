@extends('layouts.app')

@section('pageTitle', 'Invite Search')

@section('content')
<div class="container">
  <p>
    To resend a membership invitation, please search for the email address used to sign up to HMS.
  </p>
  <p>If there was an issue with the email address (such as a typo or a bounce back), please register the potential new member using the standard sign-up process.</p>
  <invite-search action="{{ route('membership.invites.resend', ['invite' => '_ID_']) }}"></invite-search>
</div>
@endsection
