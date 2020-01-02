@extends('layouts.app')

@section('pageTitle')
Absentees for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    The following members have comunicated their Absence.
  </p>
  @can('governance.meeting.recordAbsence')
  @if($meeting->getStartTime()->isFuture())
  <a  href="{{ route('governance.meetings.absence', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-times" aria-hidden="true"></i> Record Absence</a><br>
  @endif
  @endcan
</div>

@forelse ($meeting->getAbsentees() as $attendee)
@if ($loop->first)
<div class="container">
  <table class="table table-bordered table-hover">
    <thead>
      <th>Name</th>
    </thead>
    <tbody>
@endif
      <tr>
        <td>{{ $attendee->getFullname() }}</td>
      </tr>
@if ($loop->last)
    </tbody>
  </table>
</div>
@endif
@empty
<div class="container">
  No Absentees recorded.
</div>
@endforelse
@endsection
