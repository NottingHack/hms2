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
  <div class="table-responsive">
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
          <td>{{ $user->getUsername() }}</td>
          <td>{{ $user->getFullName() }}</td>
          <td>{{ $user->getEmail() }}</td>
          <td>
            <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('users.show', $user->getId()) }}"><i class="far fa-eye" aria-hidden="true"></i> View</a>
            <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('users.edit', $user->getId()) }}"><i class="fas fa-pencil" aria-hidden="true"></i> Edit</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div class="container">
  {{ $users->links() }}
</div>
@endsection
