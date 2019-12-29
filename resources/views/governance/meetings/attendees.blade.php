@extends('layouts.app')

@section('pageTitle')
Attendees for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    The following members have Check-in.
  </p>
</div>
@forelse ($meeting->getAttendees() as $attendee)
@if ($loop->first)
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Name</th>
        <th>Actions</th>
      </thead>
      <tbody>
@endif
        <tr>
          <td>{{ $attendee->getFullname() }}</td>
          <td></td>
        </tr>
@if ($loop->last)
      </tbody>
    </table>
  </div>
</div>
@endif
@empty
<div class="container">
  No Attendees Checked-in.
</div>
@endforelse
@endsection
