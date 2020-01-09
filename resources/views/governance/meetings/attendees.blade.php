@extends('layouts.app')

@section('pageTitle')
Attendees for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    The following members have Checked-in.
  </p>
  @can('governance.meeting.checkIn')
  @if ($meeting->getStartTime()->isFuture() || $meeting->getStartTime()->isToday())
  <a  href="{{ route('governance.meetings.check-in', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-check" aria-hidden="true"></i> Check-in</a><br>
  @endif
  @endcan
</div>

@forelse ($meeting->getAttendees() as $attendee)
@if ($loop->first)
<div class="container">
  <table class="table table-bordered table-hover">
    <thead>
      <th>Name</th>
    </thead>
    <tbody>
@endif
      <tr>
        <td>
          <span class="align-middle">
            {{ $attendee->getFullname() }}
            <div class="btn-group float-right" role="group" aria-label="View User">
              <a href="{{ route('users.admin.show', $attendee->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
              </div>
          </span>
        </td>
      </tr>
@if ($loop->last)
    </tbody>
  </table>
</div>
@endif
@empty
<div class="container">
  No Attendees Checked-in.
</div>
@endforelse
@endsection
