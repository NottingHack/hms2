@extends('layouts.app')

@section('pageTitle', 'Role')

@section('content')
<div class="container">
  <h1>{{ $role->getDisplayName() }}</h1>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-hover ">
      <tbody>
        <tr>
          <th>Name:</th>
          <td>{{ $role->getName() }}</td>
        </tr>
        <tr>
          <th>Description:</th>
          <td>{{ $role->getDescription() }}</td>
        </tr>
        <tr>
          <th>Email:</th>
          <td>{{ $role->getEmail() }}</td>
        </tr>
        <tr>
          <th>Slack Channel:</th>
          <td>{{ $role->getSlackChannel() }}</td>
        </tr>
        <tr>
          <th>Retained:</th>
          <td>{{ $role->getRetained() ? 'Yes' : 'No' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
@can('role.view.all')
  <h2>Permissions</h2>
  <hr>
  @foreach ($role->getPermissions() as $permission)
  <span class="badge badge-pill badge-primary">{{ $permission->getName() }}</span>
  @endforeach
  <br>
  <br>
  @can('role.edit.all')
  <a href="{{ route('roles.edit', $role->getId()) }}" class="btn btn-info btn-block"> <i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
  <br>
  @endcan

  <h2>Users</h2>
  <hr>
  <div class="table-responsive">
    <table class="table table-bordered table-sm table-hover">
      <tbody>
      @foreach ($users as $user)
      <tr>
        <td>{{ $user->getFullName() }}</td>
        <td>
          <a href="javascript:void(0);" onclick="$(this).find('form').submit();" class="btn btn-danger btn-sm" aria-label="delete">
            <form action="{{ route('roles.removeUser', ['role' => $role->getId(), 'user' => $user->getId()]) }}" method="POST" style="display: inline">
              @method('DELETE')
              @csrf
            </form>
            <i class="fas fa-trash" aria-hidden="true"></i> Remove
          </a>
        </td>
      </tr>
      @endforeach
     </tbody>
    </table>
  </div>
  <div classs="pagination-links center">
      {{ $users->links() }}
  </div>
</div>
@endcan
@endsection
