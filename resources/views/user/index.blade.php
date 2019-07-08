@extends('layouts.app')

@section('pageTitle')
@isset($role)
{{ $users->total() }} Users with Role: {{ $role->getDisplayName() }}
@else
{{ $users->total() }} Users
@endif
@endsection

@section('content')
<div class="container">
  <div class="table-responsive no-more-tables">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th>Username</th>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($users as $user)
        <tr>
          <td data-title="Username">{{ $user->getUsername() }}</td>
          <td data-title="Name">{{ $user->getFullName() }}</td>
          <td data-title="Email">{{ $user->getEmail() }}</td>
          <td data-title="Actions" class="actions">
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('users.admin.show', $user->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View</a>
            <a class="btn btn-primary btn-sm mb-1" href="{{ route('users.edit', $user->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="pagination-links">
    {{ $users->links() }}
  </div>
</div>
@endsection
