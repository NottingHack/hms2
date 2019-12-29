@extends('layouts.app')

@section('pageTitle', 'Annual Meetings')

@section('content')
<div class="container">
  <p>
    Manage Annual and Extraordinary General Meetings.
  </p>
</div>

@can('governance.meeting.create')
<div class="container">
  <a href="{{ route('governance.meetings.create') }}" class="btn btn-primary btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add a new Meeting</a>
  <br>
</div>
@endcan

<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="w-25">Title</th>
          <th>Start Time</th>
          <th>Type</th>
          <th>Quorum Needed</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($meetings as $meeting)
        <tr>
          <td data-title="Title">{{ $meeting->getTitle() }}</td>
          <td data-title="Start Time">{{ $meeting->getStartTime()->toDateTimeString() }}</td>
          <td data-title="Type">
            @if($meeting->isExtraordinary())
            Extraordinary
            @else
            Annual
            @endif
          </td>
          <td data-title="Quorum">{{ $meeting->getQuorum() }}</td>
          <td data-title="Actions" class="actions">
            <a href="{{ route('governance.meetings.show', $meeting->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="far fa-eye" aria-hidden="true"></i> View</a>
            @can('governance.meeting.edit')
            <a href="{{ route('governance.meetings.edit', $meeting->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
            @endcan
            @can('governance.meeting.checkIn')
            @if($meeting->getStartTime()->isFuture())
            <a  href="{{ route('governance.meetings.check-in', $meeting->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="fas fa-user-check" aria-hidden="true"></i> Check-in</a>
            @endif
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div classs="pagination-links center">
    {{ $meetings->links() }}
  </div>
</div>

@endsection
