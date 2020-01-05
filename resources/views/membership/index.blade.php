@extends('layouts.app')

@section('pageTitle', 'Awaiting Approvals')

@section('content')
<div class="container">
  <p>New members awaiting review and approval</p>

  @forelse ($approvals as [$user, $rejectedLog])
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Name</th>
        <th>Rejected Reason</th>
        <th>Rejected By</th>
        <th>User Updated</th>
        <th>Actions</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Name">{{ $user->getFullname() }}</td>
          <td>@isset($rejectedLog){{ $rejectedLog->getReason() }}@endisset</td>
          <td>@isset($rejectedLog){{ $rejectedLog->getRejectedBy()->getFullname() }}@endisset</td>
          <td>@isset($rejectedLog){{ $rejectedLog->getUserUpdatedAt() ? $rejectedLog->getUserUpdatedAt()->toDateTimeString() : 'Not Yet'}}@endisset</td>
          <td data-title="Actions" class="actions">
            <a href="{{ route('membership.approval', $user->getId()) }}" class="btn btn-primary btn-sm"><i class="far fa-eye" aria-hidden="true"></i> Review</a>
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  @endif
  @empty
  <p>Nothing to approve.</p>
  @endforelse
</div>
@endsection
