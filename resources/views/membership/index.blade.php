@extends('layouts.app')

@section('pageTitle', 'Awaiting Approvals')

@section('content')
<div class="container">
  <p>Words about the list of new members below that are awaiting the details reviewed</p>

  @forelse ($users as $user)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Name</th>
        <th>Actions</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Name">{{ $user->getFullname() }}</td>
          <td data-title="Actions" class="actions">
            <a href="{{ route('membership.approval', $user->getId()) }}" class="btn btn-primary btn-sm btn-sm-spacing"><i class="far fa-eye" aria-hidden="true"></i> Review</a>
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $users->links() }}
  </div>
  @endif
  @empty
  <p>Nothing to approve.</p>
  @endforelse
</div>
@endsection
