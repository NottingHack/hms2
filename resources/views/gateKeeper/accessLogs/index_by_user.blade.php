@extends('layouts.app')

@section('pageTitle')
Access Logs for {{ $user->getFirstname() }}
@endsection

@section('content')
<div class="container">
  @forelse ($accessLogs as $accessLog)
  @if ($loop->first)
  <p>Latest entries first</p>
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <th>Access Time</th>
        <th>RFID Serial</th>
        <th>Pin</th>
        <th>Door</th>
        <th>Result</th>
        <th>Denied Reason</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Access Time">{{ $accessLog->getAccessTime()->toDateTimeString() }}</td>
          <td data-title="RFID Serial">{{ $accessLog->getRfidSerial() }}</td>
          <td data-title="Pin">{{ $accessLog->getPin() }}</td>
          <td data-title="Door">{{ $accessLog->getDoor() ? $accessLog->getDoor()->getDescription() : ''}}</td>
          <td data-title="Result">{{ $accessLog->getAccessResultString() }}</td>
          <td data-title="Denied Reason">{!! $accessLog->getDeniedReason() ?? '&nbsp;' !!}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $accessLogs->links() }}
  </div>
  @endif
  @empty
  <p>No access logs for this user</p>
  @endforelse
</div>
@endsection
