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
<table class="table table-bordered table-hover table-responsive">
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
              <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('users.show', $user->getId()) }}">View</a> 
              - <a class="btn btn-primary btn-sm btn-sm-spacing" href="{{ route('users.edit', $user->getId()) }}">Edit</a> 
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    </div>
<div class="container">
<div class="pagination text-center">
    {{ $users->links() }}
</div>
</div>

@endsection
