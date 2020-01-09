@extends('layouts.app')

@section('pageTitle')
{{ $meeting->getTitle() }} Check-in
@endsection

@section('content')
<div class="container">
  <h2>Meeting Starts at {{ $meeting->getStartTime()->toTimeString() }}</h2>
  <hr>
  <meeting-check-in
    :meeting-id="{{ $meeting->getId() }}"
    ></meeting-check-in>
</div>
@endsection
