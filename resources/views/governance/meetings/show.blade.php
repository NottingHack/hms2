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
            @if($meeting->isExtraordinary())
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
        <tr>
          <th>Proxies Registered:</th>
          <td>{{ $registeredProxies }}</td>
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
          <th class="w-50">Attendees in the room:</th>
          <td>{{ $meeting->getAttendees()->count() }}</td>
        </tr>
        <tr>
          <th>Proxies Represented:</th>
          <td>{{ $representedProxies }}</td>
        </tr>
        <tr>
          <th>Check-In Count:</th>
          <td class="@if($checkInCount >= $meeting->getQuorum()) table-success @else table-danger @endif">{{ $checkInCount }}</td>
        </tr>
      </tbody>
    </table>
  </div>

@can('governance.meeting.edit')
<a href="{{ route('governance.meetings.edit', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
@endcan
@can('governance.meeting.checkIn')
@if($meeting->getStartTime()->isFuture())
<a  href="{{ route('governance.meetings.check-in', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-check" aria-hidden="true"></i> Check-In</a>
@endif
@endcan
</div>

@endsection
