@extends('layouts.app')

@section('pageTitle', $vendingMachine->getDescription() . ' Logs')

@section('content')
<div class="container">
  @forelse ($vendLogs as $vendLog)
  @if ($loop->first)
  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Id</th>
          <th>Transaction</th>
          <th>RFID Serial</th>
          <th>User</th>
          <th>Enqueued Time</th>
          <th>Request Time</th>
          <th>Success Time</th>
          <th>Cancelled Time</th>
          <th>Failed Time</th>
          <th>Amount Scaled</th>
          <th>Position</th>
          <th>Denied Reason</th>
        </tr>
      </thead>
      <tbody>
  @endif
        <tr>
          <td>{{ $vendLog->getId() }}</td>
          <td>{{ $vendLog->getTransaction() ? $vendLog->getTransaction()->getId() : '' }}</td>
          <td>{{ $vendLog->getRFIDSerial() }}</td>
          <td>
            <span class="align-middle">
              {{ $vendLog->getUser()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $vendLog->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
          </td>
          <td>{{ $vendLog->getEnqueuedTime() ? $vendLog->getEnqueuedTime()->toDateTimeString() : '' }}</td>
          <td>{{ $vendLog->getRequestTime() ? $vendLog->getRequestTime()->toDateTimeString() : '' }}</td>
          <td>{{ $vendLog->getSuccessTime() ? $vendLog->getSuccessTime()->toDateTimeString() : '' }}</td>
          <td>{{ $vendLog->getCancelledTime() ? $vendLog->getCancelledTime()->toDateTimeString() : '' }}</td>
          <td>{{ $vendLog->getFailedTime() ? $vendLog->getFailedTime()->toDateTimeString() : '' }}</td>
          <td>@format_pennies($vendLog->getAmountScaled())</td>
          <td>{{ $vendLog->getPosition() }}</td>
          <td>{{ $vendLog->getDeniedReason() }}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $vendLogs->links() }}
  </div>
  @endif
  @empty
  <p>No logs for this Machine.</p>
  @endforelse
</div>
@endsection
