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
          <td>
            {{ $role->getEmail() }}
            @if ($role->getEmailPassword())
            <small>(Password Set)</small>
            @endif
          </td>
        </tr>
        <tr>
          <th>Slack Channel:</th>
          <td>{{ $role->getSlackChannel() }}</td>
        </tr>
        <tr>
          <th>Discord Channel:</th>
          <td>{{ $role->getDiscordChannel() }}</td>
        </tr>
        @can('role.edit.all')
        <tr>
          <th>Discord Channel (Private):</th>
          <td>{{ $role->getDiscordPrivateChannel() }}</td>
        </tr>
        @endcan
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
  <p>Count: {{ $users->total() }}</p>
  <div class="table-responsive">
    <table class="table table-bordered table-sm w-50">
        <thead>
          <th>Name</th>
          <th>Action</th>
        </thead>
      <tbody>
      @foreach ($users as $user)
      <tr>
        <td>{{ $user->getFullname() }}</td>
        <td>
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
      @endforeach
     </tbody>
    </table>
  </div>
  <div classs="pagination-links center">
      {{ $users->links() }}
  </div>
  @if ($role->getCategory() != "Member")
  @can('role.grant.all')
  <form id='addUser' role="form" method="POST" action="{{ route('roles.addUser', ['role' => $role->getId()]) }}">
      @method('PATCH')
      @csrf
      <div class="form-group">
        <label>Select a user to add to this Role</label>
        <member-select-two></member-select-two>
      </div>
      <div class="form-group text-center">
        <button type="submit" class="btn btn-success btn-block"><i class="fas fa-plus" aria-hidden="true"></i> Add User</button>
      </div>
    </form>
    @endcan
    @endif
</div>
@endcan
@endsection
