@extends('layouts.app')

@section('pageTitle')
{{ $meeting->getTitle() }} Record Absence
@endsection

@section('content')
<div class="container">
  <h2>Meeting Starts at {{ $meeting->getStartTime()->toTimeString() }}</h2>
  <hr>
  <p>
    Use the search bellow to find a member to record their absence.<br>
    You can search via Name, Username, Email or Post Code.<br>
    Search via Username or Email will give the smallest set of results.
  </p>
  <form role="form" method="POST" action="{{ route('governance.meetings.absence-record', $meeting->getId()) }}">
    @csrf

    <member-select-two name="user_id" :current-only="true"></member-select-two>
    <br>
    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-times fa-lg" aria-hidden="true"></i> Record Absence</button>
  </form>
</div>
@endsection
