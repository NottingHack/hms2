@extends('layouts.app')

@section('pageTitle')
Access Logs
@endsection

@section('content')
<div class="container">
  <h2>Access Logs from {{ $fromDate->toFormattedDateString() }}</h2>
  <form role="form" method="GET" actin="{{ route('snackspace.payment-report') }}">
  <div class="form-group">
      <label for="fromDate" class="form-label">Jump to date</label>
      <input class="form-control @error('fromDate') is-invalid @enderror" id="fromDate" type="date" name="fromDate" value="{{ old('fromDate', $fromDate->toDateString()) }}" required>
      @error('fromDate')
      <p class="invalid-feedback">
        <strong>{{ $errors->first('fromDate') }}</strong>
      </p>
      @enderror
    </div>

    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-forward"></i> Jump</button>
  </form>
  <br>
  <p>Oldest entries first</p>
  @foreach ($accessLogs as $accessLog)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-striped table-hover">
      <thead>
        <th>Access Time</th>
        <th>RFID Serial</th>
        <th>Pin</th>
        <th>Door</th>
        <th>User</th>
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
          <td data-title="User">
            @if ($accessLog->getUser())
            <span class="align-middle">
              {{ $accessLog->getUser()->getFullname() }}
              <div class="btn-group float-right d-none d-md-inline" role="group" aria-label="View User">
                <a href="{{ route('users.admin.show', $accessLog->getUser()->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i></a>
                </div>
            </span>
            @else
            &nbsp;
            @endif
          </td>
          <td data-title="Result">{{ $accessLog->getAccessResultString() }}</td>
          <td data-title="Denied Reason">{!! $accessLog->getDeniedReason() ?? '&nbsp;' !!}</td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $accessLogs->appends(['fromDate' => $fromDate->toDateString()])->links() }}
  </div>
  @endif
  @endforeach
</div>
@endsection
