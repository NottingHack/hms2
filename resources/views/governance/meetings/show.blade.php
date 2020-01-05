@extends('layouts.app')

@section('pageTitle', $meeting->getTitle())

@section('content')
<div class="container">
  <h2>Details</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover ">
      <tbody>
        <tr>
          <th class="w-50">Start Time:</th>
          <td>{{ $meeting->getStartTime()->toDateTimeString() }}</td>
        </tr>
        <tr>
          <th>Type:</th>
          <td>
            @if ($meeting->isExtraordinary())
            Extraordinary
            @else
            Annual
            @endif
          </td>
        </tr>
        <tr>
          <th>Current Member Count (at start of meeting):</th>
          <td>{{ $meeting->getCurrentMembers() }}</td>
        </tr>
        <tr>
          <th>Voting Member Count (at start of meeting):</th>
          <td>{{ $meeting->getVotingMembers() }}</td>
        </tr>
        <tr>
          <th>Quorum Required ({{ Meta::get('quorum_percent', 20) }}% of Voting Members):</th>
          <td>{{ $meeting->getQuorum() }}</td>
        </tr>
      </tbody>
    </table>
  </div>
  <hr>
  <h2>Attendance</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-hover ">
      <tbody>
        <tr>
          <th class="w-50">
            <span class="align-middle">
              Absentees Comunicated:&nbsp;
              <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                <a href="{{ route('governance.meetings.absentees', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
              </div>
            </span>

          </th>
          <td>{{ $meeting->getAbsentees()->count() }}</td>
        </tr>
        <tr>
          <th>
            <span class="align-middle">
              Proxies Registered:&nbsp;
              <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                <a href="{{ route('governance.proxies.index', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
              </div>
            </span>
          </th>
          <td>{{ $meeting->getProxies()->count() }}</td>
        </tr>
        <tr>
          <th>
            <span class="align-middle">
              Attendees in the room:&nbsp;
              <div class="btn-group float-right" role="group" aria-label="View Proxies Registered">
                <a href="{{ route('governance.meetings.attendees', ['meeting' => $meeting->getId()]) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
              </div>
            </span>
          </th>
          <td>{{ $meeting->getAttendees()->count() }}</td>
        </tr>
        <tr>
          <th>Proxies Represented:</th>
          <td>{{ $representedProxies }}</td>
        </tr>
        <tr>
          <th>Check-in Count:</th>
          <td class="@if ($checkInCount >= $meeting->getQuorum()) table-success @else table-danger @endif">{{ $checkInCount }}</td>
        </tr>
      </tbody>
    </table>
  </div>

@can('governance.meeting.edit')
<a href="{{ route('governance.meetings.edit', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
@endcan
{{-- @can('governance.meeting.view')
<a href="{{ route('governance.proxies.index', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-eye" aria-hidden="true"></i> View Proxies</a>
<a href="{{ route('governance.meetings.attendees', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-eye" aria-hidden="true"></i> View Attendees</a>
<a href="{{ route('governance.meetings.absentees', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-eye" aria-hidden="true"></i> View Absentees</a>
@endcan --}}
@if ($meeting->getStartTime()->isFuture())
@can('governance.meeting.checkIn')
<a  href="{{ route('governance.meetings.check-in', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-check" aria-hidden="true"></i> Check-in</a>
@endcan
@can('governance.meeting.recordAbsence')
<a  href="{{ route('governance.meetings.absence', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-times" aria-hidden="true"></i> Record Absence</a>
@endif
@endcan
</div>

@endsection
