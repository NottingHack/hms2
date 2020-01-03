@extends('layouts.app')

@section('pageTitle', $tool->getName() . ' ' . HMS\Tools\ToolManager::GRANT_STRINGS[$grantType] . 's')

@section('content')
<div class="container">
  <tool-grant-modal :tool-id="{{ $tool->getId() }}" tool-name="{{ $tool->getName() }}" grant-type="{{ $grantType }}" :block="true"></tool-grant-modal><br>
  @forelse ($users as $user)
  @if ($loop->first)
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <th>Name</th>
        <th class="d-none d-md-table-cell">Date Appointed</th>
        <th class="d-none d-md-table-cell">Appointed By</th>
        <th class="w-25">Actions</th>
      </thead>
      <tbody>
  @endif
        <tr>
          <td data-title="Name">{{ $user->getFullname() }}</td>
          <td data-title="Date Appointed" class="d-none d-md-table-cell"></td>
          <td data-title="Appointed By" class="d-none d-md-table-cell"></td>
          <td data-title="Actions" class="actions">
          <a href="{{ route('users.admin.show', $user->getId()) }}" class="btn btn-primary btn-sm mb-1"><i class="far fa-eye" aria-hidden="true"></i> View</a>
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm mb-1" aria-label="delete">
            <form action="{{ route('roles.removeUser', ['role' => $role->getId(), 'user' => $user->getId()]) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            <i class="fas fa-trash" aria-hidden="true"></i> Remove
          </a>
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
  <p>No users with this access level.</p>
  @endforelse
</div>
@endsection
