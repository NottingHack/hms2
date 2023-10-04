@extends('layouts.app')

@section('pageTitle', 'Role history for ' . $user->getFullname())

@section('content')
@if (Route::currentRouteName() == 'users.admin.role-updates')
<div class="container">
  <h4>
    {{ $user->getFullname() }} <a class="btn-sm btn-primary mb-2" href="{{ route('users.admin.show', $user->getId()) }}"><i class="fa fa-eye"></i></a>
  </h4>
  <hr>
</div>
@endif

<div class="container">
  <p></p>
  @forelse ($roleUpdates as $roleUpdate)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Date</th>
        <th>Role Added</th>
        <th>Role Removed</th>
        <th>Updated By</th>
        <th>Reason</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Date" class="text-monospace">{{ $roleUpdate->getCreatedAt()->toDateTimeString() }}</td>
          <td data-title="Role Added" @if ($roleUpdate->getRoleAdded()) class="table-success"@endif>
            @if ($roleUpdate->getRoleAdded())
            <strong class="text-capitalize">{{ $roleUpdate->getRoleAdded()->getCategory() }}:</strong> {{ $roleUpdate->getRoleAdded()->getDisplayName() }}
            @else
            &nbsp;
            @endif
          </td>
          <td data-title="Role Removed"  @if ($roleUpdate->getRoleRemoved()) class="table-danger"@endif>
            @if ($roleUpdate->getRoleRemoved())
            <strong class="text-capitalize">{{ $roleUpdate->getRoleRemoved()->getCategory() }}:</strong> {{ $roleUpdate->getRoleRemoved()->getDisplayName() }}
            @else
            &nbsp;
            @endif
          </td>
          <td data-title="Updated By">
            @if ($roleUpdate->getUpdateBy())
            {{ $roleUpdate->getUpdateBy()->getFullname() }}
            @else
            &nbsp;
            @endif
          </td>
          <td>
            @if ($roleUpdate->getReason())
            {{ $roleUpdate->getReason() }}
            @else
            &nbsp;
            @endif
          </td>
        </tr>
  @if ($loop->last)
      </tbody>
    </table>
  </div>

  <div class="pagination-links">
    {{ $roleUpdates->links() }}
  </div>
  @endif
  @empty
  <p>Nothing history for this user.</p>
  @endforelse
</div>
@endsection
