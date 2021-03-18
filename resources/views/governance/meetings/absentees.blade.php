@extends('layouts.app')

@section('pageTitle')
Absentees for {{ $meeting->getTitle() }}
@endsection

@section('content')
<div class="container">
  <p>
    The following members have communicated their Absence.
  </p>
  @can('governance.meeting.recordAbsence')
  @if ($meeting->getStartTime()->isFuture() || $meeting->getStartTime()->isToday())
  <a  href="{{ route('governance.meetings.absence', $meeting->getId()) }}" class="btn btn-primary btn-block"><i class="fas fa-user-times" aria-hidden="true"></i> Record Absence</a><br>
  @endif
  @endcan
</div>

@forelse ($meeting->getAbsentees() as $absentee)
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
            {{ $absentee->getFullname() }}
            <div class="btn-group float-right" role="group" aria-label="View User">
              <a href="{{ route('users.admin.show', $absentee->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
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
  No Absentees recorded.
</div>
@endforelse
@endsection
